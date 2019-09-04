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
        $query = DB::table($table)->select($table . '.*');

        $data->data = $query->paginate();
//        $data->links = [
//            "first" => "http://example.com/pagination?page=1",
//            "last" => "http://example.com/pagination?page=1",
//            "current" => url()->current(),
//            "prev" => null,
//            "next" => null
//        ];
        return response()->json($data);
    }
}
