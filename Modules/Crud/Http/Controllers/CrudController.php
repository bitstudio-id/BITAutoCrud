<?php

namespace Modules\Crud\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

class CrudController extends Controller
{
    public function get(Request $request,$table)
    {
        $data = new \stdClass();
        $ex = ['created_at', 'updated_at'];
        $scheme = Schema::getColumnListing($table);
        $column =[];
        $scheme = array_merge(array_diff($scheme, $ex));
        foreach ($scheme as $v){
            $ob = new stdClass();
            $g = str_replace("_"," ",$v);
            $ob->title = ucwords(substr($g, strpos($g, " ") + 1));
            $ob->name = $v;
            $ob->data = $v;
            array_push($column,$ob);
        }
        $data->column = $column;
        $data->data = DB::table($table)->select($table . '.*')->get();
        if (empty($data->data)) {
            foreach ($data->data[0] as $k => $val) {
                array_push($column, $k);
            }
        }
        return response()->json($data);
    }
}
