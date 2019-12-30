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
        $ex = [];
        $column = [];
        $table = $p = $request->table;
        if ($request->prefix) {
            $p = substr_replace(Prefix::prefix($p),"",-1);
        }
        array_push($ex, [$p . '_created_at', $p . '_updated_at', $p . '_by', $p . '_deleted_at', $p . '_decimal', $p . '_pembulatan', $p . '_currency', $p . '_foto', $p . '_user']);
        $data = new \stdClass();
        try {

            $query = DB::table($table)->select($table . '.*');
            if (!$request->has('trash')){
                $query->whereNull($p.'_deleted_at');
            }else{
                $query->whereNotNull($p.'_deleted_at');
            }
            if ($request->join) {
                foreach ($request->join as $value) {
                    $query->leftJoin($value, $value . '_id', '=', $p . '_' . $value . '_id');
                    (Schema::hasColumn($value, $value . '_nama')) ? $query->addSelect($value . '_nama') : null;
                    array_push($ex, [$p . '_' . $value . '_id', $value . '_by', $value . '_id']);
                }
            }
            $data->data = $query->get();
            foreach ($data->data[0] as $k => $val) {
                array_push($column, $k);
            }
            foreach ($ex as $v) {
                $column = array_merge(array_diff($column, $v));
            }
            $data->column = $column;
            $data->buildForm = $this->formBuilder($table);
            $data->table = $p;
            $data->join = $request->join;
        } catch (\Exception $e) {
            return response()->json($e);
        }
        return response()->json($data);

    }
}
