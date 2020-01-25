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
            $form[] = $value->form;
        }
        $data->form = $form;
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


