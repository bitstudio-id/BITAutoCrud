<?php

namespace Modules\Crud\Http\Controllers;

use App\BitTable;
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
        $form=[];
        $data->form = BitTable::with('child')->where('bittable_name','=',$table)->first();
        $data->column[]=["title"=>"No", "data"=> null, "name"=> null];
        foreach ($data->form->child as $key=>$value) {
            $data->column[] = ["title"=>$value->form->bitform_label, "data"=> $value->bittable_name, "name"=> $value->bittable_name];
            $form[] = $value;
        }
        $data->form = $form;
        $data->column[]=["title"=>"Action", "data"=> null, "name"=> null];
        $data->data = DB::table($table)->get();
        return response()->json($data);
    }
    public function dataTable(Request $request,$table)
    {
        $data = new stdClass();
        $data->data = DB::table($table)->get();
        return response()->json($data);
    }
    public function select(Request $request)
    {
        if ($request->has('id')){
            $id = BitTable::where('bittable_id', $request->id)
                ->with('parent')
                ->first();
            $text = BitTable::where('bittable_id', $request->text)
                ->first()->bittable_name;
            $data = DB::table($id->parent->bittable_name)->select($id->bittable_name.' as id',$text.' as text')->get();

        }else{
            $data = collect();
            $nAr = explode(',', $request->enum);
            foreach ($nAr as $v) {
                $data->push(['id'=>$v,'text'=>$v]);
            }

        }
        $data->prepend(0);
        return $data;
    }
    public function post(Request $request,$table)
    {
        $data = DB::table($table)->updateOrInsert(
            [
                $table.'_id' => $request[$table.'_id']
            ],
            $request->all()
        );
        return response()->json($data,200);
    }
    public function edit($table,$id)
    {
        $data = DB::table($table)->where($table.'_id','=',$id)->first();
        return response()->json($data,200);
    }
    public function delete($table,$id)
    {
        $data = DB::table($table)->where($table.'_id','=',$id)->delete();
        return response()->json($data,200);
    }
}


