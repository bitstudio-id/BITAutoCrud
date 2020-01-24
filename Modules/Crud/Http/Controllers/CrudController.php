<?php

namespace Modules\Crud\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use stdClass;

class CrudController extends Controller
{
    public function get(Request $request,$table)
    {
        $data = new stdClass();
        $data->column = [];
        $data->form = DB::table('bitform')
            ->leftJoin('bittable as c','c.bittable_id','=','bitform_bittable_id')
            ->leftJoin('bittable as p', 'p.bittable_id','=','c.bittable_parent_id')
            ->select('bitform.*')
            ->addSelect('p.bittable_name')
            ->where('p.bittable_name','=',$table)
            ->get();
        $data->column[]=["title"=>"No", "data"=> null, "name"=> null];
        foreach ($data->form as $key=>$value) {
//            $data->column[] =
        }
        $data->column[]=["title"=>"Action", "data"=> null, "name"=> null];

        $data->data = DB::table($table)
            ->get();
        return response()->json($data);
    }
    public function post(Request $request,$table)
    {
        $data = DB::table($table)->updateOrInsert(
            [
                $table.'_id' => $request->$table.'_id'
            ],
            $request->all()
        );
        return response()->json($data,200);
    }
    public function delete(Request $request, $table)
    {
        $data = DB::table($table)->where($table.'_id','=',$request->id);
        return response()->json($data,200);
    }
}


