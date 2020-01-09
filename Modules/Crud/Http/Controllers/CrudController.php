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
        $data = new stdClass();
        $column =[];
        $id = DB::table('bittable')
            ->where('bittable_type','table')
            ->where('bittable_name',$table)->get();
        $ex = ['created_at', 'updated_at'];
        $scheme = Schema::getColumnListing($table);
        $scheme = array_merge(array_diff($scheme, $ex));
        foreach ($scheme as $v){
            $ob = new stdClass();
            $g = str_replace("_"," ",$v);
            $ob->title = ucwords(substr($g, strpos($g, " ") + 1));
            $ob->name = $v;
            $ob->data = $v;
            array_push($column,$ob);
        }
//        $data->form = DB::table('bitform')->select( '*')->where('bitform_bittable_parent_id',$id)->get();
        $data->column = $column;
        $data->data = DB::table('bittable')->select('*')->where('bittable_parent_id',$id)->get();
        if (empty($data->data)) {
            foreach ($data->data[0] as $k => $val) {
                array_push($column, $k);
            }
        }
        return response()->json($data);
    }

    public function bitGet(Request $request,$table)
    {
        $data = new stdClass();
        $data->form = [
            'parent'=>
                [
                    ['label' => "", 'id' => "bittable_id", 'input' => "input", 'type' => "hidden", 'url' => ""],
                    ['label' => "Table Name", 'id' => "bittable_name", 'input' => "input", 'type' => "text", 'url' => ""],
                ]
            ,
            'child'=>
            [
                ['label' => "", 'id' => "[c]bittable_id", 'input' => "input", 'type' => "hidden", 'url' => ""],
                ['label' => "Field Name", 'id' => "[c]bittable_name", 'input' => "input", 'type' => "text", 'url' => ""],
                ['label' => "Field Type", 'id' => "[c]bittable_type", 'input' => "input", 'type' => "text", 'url' => ""],
                ['label' => "Field Length", 'id' => "[c]bittable_length", 'input' => "input", 'type' => "text", 'url' => ""],
                ['label' => "Field Default", 'id' => "[c]bittable_default", 'input' => "input", 'type' => "text", 'url' => ""],
                ['label' => "Field Nullable", 'id' => "[c]bittable_nullable", 'input' => "input", 'type' => "text", 'url' => ""],
                ['label' => "Join Table Type", 'id' => "[c]bittable_join", 'input' => "select", 'type' => "select", 'url' => route('bit.select','bittable_join')],
                ['label' => "Join To ID", 'ipd' => "[c]bittable_to_id", 'input' => "select", 'type' => "select", 'url' => route('bit.select','bittable_to_id')],
            ],

        ];
        $column =[];
        $ex = ['created_at', 'updated_at'];
        $scheme = Schema::getColumnListing($table);
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

        $data->data = DB::table($table)->select($table.'.*')->get();
        if (empty($data->data)) {
            foreach ($data->data[0] as $k => $val) {
                array_push($column, $k);
            }
        }
        return response()->json($data);
    }
    public function select($p){
        switch ($p) {
            case 'bittable_join' :
                $data = [
                    0,
                    ["id"=> 'left',"text"=> "left"],
                    ["id"=> 'inner',"text"=> "inner"],
                    ["id"=> 'right',"text"=> "right"]
                ];
                break;
            case 'bittable_to_id' :
                $data = [0];
                return $p = DB::table('bittable as p')
                    ->whereNull('p.bittable_parent_id')
                    ->selectSub()
//                    ->leftJoin('bittable as p','p.bittable_id','=','c.bittable_parent_id')
//                    ->select('c.bittable_id','c.bittable_parent_id as p_id','c.bittable_name','p.bittable_id','p.bittable_name as parent')
//                    ->whereNotNull('c.bittable_parent_id')
//                    ->groupBy('c.bittable_id')
                    ->get();
                foreach ($q as $k => $v) {
                    $data[] = ["id"=> $v->bittable_id,"text"=> "Table[".$v->bittable_name."] <> Field[".$v->bittable_name."]"];
                }

        }
        return response()->json($data);
    }
}


