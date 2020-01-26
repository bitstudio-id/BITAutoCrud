<?php

namespace Modules\BIT\Http\Controllers;

use App\BitTable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

class BITController extends Controller
{
    public function bitGetDataDetail($id){
        $data = BitTable::with('join','form','parent')
            ->where('bittable_parent_id','=',$id)
            ->get();
        return response()->json($data);
    }

    public function bitGetDataTable(Request $request,$table){
        $data = new stdClass();
        if ($table==='bitform') {
            $data->data = BitTable::with('child')
                ->whereNull('bittable_parent_id')
                ->get();
        }else{
            $data->data = DB::table($table)->select($table . '.*')
                ->get();
        }

        if (empty($data->data)) {
            foreach ($data->data[0] as $k => $val) {
                array_push($column, $k);
            }
        }
        return response()->json($data);
    }

    public function bitGetForm(Request $request, $table, $index = 0)
    {
        $data = new stdClass();
        $ex = ['created_at', 'updated_at'];
        $column = [];
        $column[] = ["title"=>"No", "data"=> null, "name"=> null];
        $scheme = Schema::getColumnListing($table);
        if ($table==='bitform'){
            array_push($ex,'bittable_parent_id','bittable_type','bittable_length','bittable_attributes','bittable_join','bittable_join_to_id','bittable_join_value');
            $scheme = Schema::getColumnListing('bittable');
        }
        $scheme = array_merge(array_diff($scheme, $ex));
        foreach ($scheme as $v) {
            $ob = new stdClass();
            $g = str_replace("_", " ", $v);
            $ob->title = ucwords(substr($g, strpos($g, " ") + 1));
            $ob->name = $v;
            $ob->data = $v;
            array_push($column, $ob);
        }
        $column[] = ["title"=>"Action", "data"=> null, "name"=> null];
        $data->column = $column;
        if ($table==='bitform'){
            $data->form = [
                ['label' => "", 'id' => "bitform_bittable_id", 'input' => "input", 'type' => "hidden", 'url' => ""],
                ['label' => "Label", 'id' => "bitform_label", 'input' => "input", 'type' => "text", 'url' => ""],
                ['label' => "Field Type", 'id' => "bitform_input", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bitform_input')],
                ['label' => "Mode", 'id' => "bitform_type", 'input' => "select", 'type' => "select", 'url' =>  route('bit.select', 'bitform_type')],
                ['label' => "Url Data", 'id' => "bitform_url", 'input' => "input", 'type' => "text", 'url' => ""],
//                ['label' => "Rules Validate", 'id' => "bitform_rules", 'input' => "input", 'type' => "text", 'url' => ""],
//                ['label' => "Message Handle", 'id' => "bitform_messages", 'input' => "input", 'type' => "text", 'url' => ""],
            ];
        } else {
            $data->form = [
                'parent' =>
                    [
                        ['label' => "", 'id' => "bittable_id", 'input' => "input", 'type' => "hidden", 'url' => ""],
                        ['label' => "Table Name", 'id' => "bittable_name", 'input' => "input", 'type' => "text", 'url' => ""],
                    ]
                ,
                'child' =>
                    [
                        ['label' => "", 'id' => "field[$index][bittable_id]", 'input' => "input", 'type' => "hidden", 'url' => ""],
                        ['label' => "Field Name", 'id' => "field[$index][bittable_name]", 'input' => "input", 'type' => "text", 'url' => ""],
                        ['label' => "Field Type", 'id' => "field[$index][bittable_type]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_type')],
                        ['label' => "Field Length/Value", 'id' => "field[$index][bittable_length]", 'input' => "input", 'type' => "text", 'url' => ""],
//                    ['label' => "Field Default", 'id' => "field[$index][bittable_default]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_default')],
                        ['label' => "Field Attributes", 'id'=>'bittable_attributes-'.$index,  'name' => "field[$index][bittable_attributes]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_attributes')],
                    ],
                'join' =>
                    [
                        ['label' => "Join Table Type", 'id' => "field[$index][bittable_join]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_join')],
                        ['label' => "Join Table Primary", 'id' => "field[$index][bittable_join_to_id]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_to_id')],
                        ['label' => "Join Table Scope Value", 'id' => "field[$index][bittable_join_value]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_join_value')],
                    ],
            ];
        }

        return response()->json($data);
    }

    public function bitSave(Request $request)
    {
        if ($request->has('id')){
            foreach ($request->field as $k => $v){
                DB::table('bitform')->updateOrInsert(
                    ['bitform_bittable_id'=>$v['bitform_bittable_id']],
                    $v
                );
            }
            return \response()->json('success',200);
        }
        $field = '';
        DB::beginTransaction();

        try {
            $data = BitTable::updateOrCreate(
                ['bittable_id' => $request->bittable_id],
                [
                    'bittable_name' => $request->bittable_name,
                    'bittable_type' => 'table',
                ]
            );
            DB::table('bitmenu')->updateOrInsert(
                ['bitmenu_bittable_id' => $data->bittable_id],
                ['bitmenu_bittable_id' => $data->bittable_id,]
            );

            foreach ($request->field as $val) {
                $val['bittable_parent_id'] = $data->bittable_id;
                $val['bittable_name'] = $data->bittable_name . '_' . $val['bittable_name'];
                $c = BitTable::updateOrCreate(
                    ['bittable_id' => $val['bittable_id']],
                    $val
                );
                $bitform_url = '';
                $input = 'input';
                $type = '';
                if ($c->bittable_type === 'ENUM') {
                    $input = 'select';
                    $bitform_url = $val['bittable_length'];
                } else if ($c->bittable_type === 'TEXT') {
                    $input = 'textarea';
                } else if ($c->bittable_type === 'DATE') {
                    $type = 'date';
                }

                if ($c->bittable_attributes === 'foreign') {
                    $input = 'select';
                    $bitform_url = '?id='.$val['bittable_join_to_id'].'&text='.$val['bittable_join_value'];
                }
                DB::table('bitform')->updateOrInsert(
                    ['bitform_bittable_id' => $c->bittable_id],
                    [
                        'bitform_bittable_id' => $c->bittable_id,
                        'bitform_label' => $c->bittable_name,
                        'bitform_input' => $input,
                        'bitform_type' => $type,
                        'bitform_url' =>$bitform_url,
                    ]
                );
            }
            $f = BitTable::where('bittable.bittable_parent_id', $data->bittable_id)
                ->with('join', 'join.parent')
                ->get();

            foreach ($f as $key => $value) {
                $length = 119;
                if ($value->bittable_length !== null) {
                    $rep = '"';
                    if (strpos($value->bittable_length, ',')) {
                        $length = $rep.str_replace(',','","',$value->bittable_length).'"';
                    }else{
                        $length = $value->bittable_length;
                    }
                }
                $field .= $value->bittable_name.' '.$value->bittable_type.',';
                if (in_array($value->bittable_type,['INT','BIGINT','ENUM','CHAR','VARCHAR','DECIMAL'])){
                    $field = rtrim($field, ',');
                    $field .= '('.$length.') ,';
                }
                if ($value->bittable_attributes==='primary'){
                    $field = rtrim($field, ',');
                    $field .= 'UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,';
                }
                if ($value->bittable_attributes==='foreign'){
                    $field = rtrim($field, ',');
                    $field .= ' UNSIGNED NULL, ';
                    $field .= 'INDEX ('.$value->bittable_name.'), FOREIGN KEY('.$value->bittable_name.') REFERENCES '.$value->join->parent->bittable_name.'('.$value->join->bittable_name.') ON UPDATE CASCADE ON DELETE CASCADE,';
                }
                if ($value->bittable_attributes==='unique'){
                    rtrim($field, ',');
                    $field .= 'UNIQUE KEY '.$data->bittable_name.'_'.$value->bittable_name.'_unique ('.$value->bittable_name.'),';
                }

            }
            $field .= $data->bittable_name.'_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, '.$data->bittable_name.'_update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
            DB::select('CREATE TABLE '.$data->bittable_name.' ('.$field.')');
            DB::commit();
            return \response()->json('success',200);
        } catch (\Exception $e) {
            DB::rollback();
            return \response()->json($e,500);
        }

    }

    public function bitDelete($id)
    {
        DB::beginTransaction();
        $data = DB::table('bittable')->where('bittable_id','=',$id);
        DB::select('DROP TABLE '.$data->first()->bittable_name);
        try {
            $data->delete();
        } catch (\Exception $e) {
        }
        DB::commit();
    }

    public function bitMenuGet(){
        $data = DB::table('bitmenu')
            ->join('bittable','bittable_id','=','bitmenu_bittable_id')
            ->orderBy('bitmenu_index')
            ->get();
        return response()->json($data,!($data)->isEmpty() ? 200 : 500);
    }

    public function bitMenuSave(Request $request){
        foreach ($request->field as $key => $val){
            DB::table('bitmenu')->updateOrInsert(
                [
                    'bitmenu_id'=> $key
                ],$val
            );
        }
        return \response()->json('success',200);

    }

    public function select($p,Request $request)
    {
        $data = [];
        if ($request->has('id')) {
            $data = DB::table($p)->select($request->id.' as id',$request->text.' as text')->get();
            $data->prepend(0);

        }
        switch ($p) {
            case 'bittable_join_value':
                $data = [0];
                $parent = DB::table('bittable')
                    ->select('bittable_id as id', 'bittable_parent_id', 'bittable_name as name', 'bittable_type','bittable_attributes')
                    ->get();
                foreach ($parent as $k => $v) {
                    if ($v->bittable_type === 'table') {
                        foreach ($parent as $key => $child) {
                            if ($child->bittable_parent_id === $v->id && $child->bittable_attributes !== 'primary'  ) {
                                $child->text = $v->name . ' → ' . $child->name;
                                $data[] = $child;
                            }
                        }
                    }
                }
                break;
            case 'bitform_input':
                $data = [
                    0,
                    ["id" => 'input', "text" => "input"],
                    ["id" => 'select', "text" => "select"],
                    ["id" => 'textarea', "text" => "textarea"],
                ];
                break;
            case 'bitform_type':
                $data = [
                    0,
                    ["id" => 'text', "text" => "text"],
                    ["id" => 'hidden', "text" => "hidden"],
                    ["id" => 'number', "text" => "number"],
                    ["id" => 'email', "text" => "email"],
//                    ["id" => 'radio', "text" => "radio"],
                    ["id" => 'password', "text" => "password"],
//                    ["id" => 'checkbox', "text" => "checkbox"],
                    ["id" => 'date', "text" => "date"],
                ];
                break;
            case 'bittable_join' :
                $data = [
                    0,
                    ["id" => 'left', "text" => "left"],
                    ["id" => 'inner', "text" => "inner"],
                    ["id" => 'right', "text" => "right"]
                ];
                break;
            case 'bittable_to_id' :
                $data = [0];
                $parent = DB::table('bittable')
                    ->select('bittable_id as id', 'bittable_parent_id', 'bittable_name as name', 'bittable_type','bittable_attributes')
                    ->get();
                foreach ($parent as $k => $v) {
                    if ($v->bittable_type === 'table') {
                        foreach ($parent as $key => $child) {
                            if ($child->bittable_parent_id === $v->id && $child->bittable_attributes === 'primary'  ) {
                                $child->text = $v->name . ' → ' . $child->name;
                                $data[] = $child;
                            }
                        }
                    }
                }
                break;
            case 'bittable_type' :
                $data = [
                    0,
                    ["id" => "INT", "text" => "INT", "title" => "A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295"],
                    ["id" => "BIGINT", "text" => "BIGINT", "title" => "An 8-byte integer, signed range is -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807, unsigned range is 0 to 18,446,744,073,709,551,615"],
                    ["id" => "VARCHAR", "text" => "VARCHAR", "title" => "A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size"],
                    ["id" => "TEXT", "text" => "TEXT", "title" => "A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes"],
                    ["id" => "DATE", "text" => "DATE", "title" => "A date, supported range is 1000-01-01 to 9999-12-31"],
                    ["text" => "Numeric",
                        "children" => [
//                            ["id" => "TINYINT", "text" => "TINYINT", "title" => "A 1-byte integer, signed range is -128 to 127, unsigned range is 0 to 255"],
//                            ["id" => "SMALLINT", "text" => "SMALLINT", "title" => "A 2-byte integer, signed range is -32,768 to 32,767, unsigned range is 0 to 65,535"],
//                            ["id" => "MEDIUMINT", "text" => "MEDIUMINT", "title" => "A 3-byte integer, signed range is -8,388,608 to 8,388,607, unsigned range is 0 to 16,777,215"],
//                            ["id" => "integer", "text" => "INT", "title" => "A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295"],

                            ["id" => "DECIMAL", "text" => "DECIMAL", "title" => "A fixed-point number (M, D) - the maximum number of digits (M) is 65 (default 10), the maximum number of decimals (D) is 30 (default 0)"],
                            ["id" => "FLOAT", "text" => "FLOAT", "title" => "A small floating-point number, allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38"],
                            ["id" => "DOUBLE", "text" => "DOUBLE", "title" => "A double-precision floating-point number, allowable values are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308"],
                            ["id" => "REAL", "text" => "REAL", "title" => "Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is a synonym for FLOAT)"],
                            ["id" => "BIT", "text" => "BIT", "title" => "A bit-field type (M), storing M of bits per value (default is 1, maximum is 64)"],
                            ["id" => "boolean", "text" => "BOOLEAN", "title" => "A synonym for TINYINT(1), a value of zero is considered false, nonzero values are considered true"],
//                            ["id" => "SERIAL", "text" => "SERIAL", "title" => "An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE"],
                        ]],
                    ["text" => "Date and time",
                        "children" => [
                            ["id" => "DATE", "text" => "DATE", "title" => "A date, supported range is 1000-01-01 to 9999-12-31"],
                            ["id" => "DATETIME", "text" => "DATETIME", "title" => "A date and time combination, supported range is 1000-01-01 00:00:00 to 9999-12-31 23:59:59"],
                            ["id" => "TIMESTAMP", "text" => "TIMESTAMP", "title"=> "A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 03:14:07 UTC, stored as the number of seconds since the epoch (1970-01-01 00:00:00 UTC)"],
                            ["id" => "TIME", "text" => "TIME", "title" => "A time, range is -838:59:59 to 838:59:59"],
                            ["id" => "YEAR", "text" => "YEAR", "title" => "A year in four-digit (4, default) or two-digit (2) format, the allowable values are 70 (1970) to 69 (2069) or 1901 to 2155 and 0000"],
                        ]],
                    ["text" => "String",
                        "children" => [
                            ["id" => "CHAR", "text" => "CHAR", "title" => "A fixed-length (0-255, default 1) string that is always right-padded with spaces to the specified length when stored"],
                            ["id" => "VARCHAR", "text" => "VARCHAR", "title" => "A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size"],
                            ["id" => "TINYTEXT", "text" => "TINYTEXT", "title" => "A TEXT column with a maximum length of 255 (2^8 - 1) characters, stored with a one-byte prefix indicating the length of the value in bytes"],
                            ["id" => "TEXT", "text" => "TEXT", "title" => "A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes"],
                            ["id" => "MEDIUMTEXT", "text" => "MEDIUMTEXT", "title" => "A TEXT column with a maximum length of 16,777,215 (2^24 - 1) characters, stored with a three-byte prefix indicating the length of the value in bytes"],
                            ["id" => "LONGTEXT", "text" => "LONGTEXT", "title" => "A TEXT column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) characters, stored with a four-byte prefix indicating the length of the value in bytes"],
//                            ["id" => "binary", "text" => "BINARY", "title" => "Similar to the CHAR type, but stores binary byte strings rather than non-binary character strings"],
//                            ["id" => "VARBINARY", "text" => "VARBINARY", "title" => "Similar to the VARCHAR type, but stores binary byte strings rather than non-binary character strings"],
//                            ["id" => "TINYBLOB", "text" => "TINYBLOB", "title" => "A BLOB column with a maximum length of 255 (2^8 - 1) bytes, stored with a one-byte prefix indicating the length of the value"],
//                            ["id" => "MEDIUMBLOB", "text" => "MEDIUMBLOB", "title" => "A BLOB column with a maximum length of 16,777,215 (2^24 - 1) bytes, stored with a three-byte prefix indicating the length of the value"],
//                            ["id" => "BLOB", "text" => "BLOB", "title" => "A BLOB column with a maximum length of 65,535 (2^16 - 1) bytes, stored with a two-byte prefix indicating the length of the value"],
//                            ["id" => "LONGBLOB", "text" => "LONGBLOB", "title" => "A BLOB column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) bytes, stored with a four-byte prefix indicating the length of the value"],
                            ["id" => "ENUM", "text" => "ENUM", "title" => "An enumeration, chosen from the list of up to 65,535 values or the special '' error value"],
                            ["id" => "SET", "text" => "SET", "title" => "A single value chosen from a set of up to 64 members"],
                        ]],
//                    ["text" => "Spatial",
//                        "children" => [
//                            ["id" => "GEOMETRY", "text" => "GEOMETRY", "title" => "A type that can store a geometry of any type"],
//                            ["id" => "POINT", "text" => "POINT", "title" => "A point in 2-dimensional space"],
//                            ["id" => "LINESTRING", "text" => "LINESTRING", "title" => "A curve with linear interpolation between points"],
//                            ["id" => "POLYGON", "text" => "POLYGON", "title" => "A polygon"],
//                            ["id" => "MULTIPOINT", "text" => "MULTIPOINT", "title" => "A collection of points"],
//                            ["id" => "MULTILINESTRING", "text" => "MULTILINESTRING", "title" => "A collection of curves with linear interpolation between points"],
//                            ["id" => "MULTIPOLYGON", "text" => "MULTIPOLYGON", "title" => "A collection of polygons"],
//                            ["id" => "GEOMETRYCOLLECTION", "text" => "GEOMETRYCOLLECTION", "title" => "A collection of geometry objects of any type"],
//                        ]],
//                    ["text" => "JSON",
//                        "children" => [
//                            ["id" => "JSON", "text" => "JSON", "title" => "Stores and enables efficient access to data in JSON (JavaScript Object Notation) documents"],
//                        ]]
                ];
                break;
            case 'bittable_default' :
                $data = [
                    0,
                    ["id" => 'NULL', "text" => "NULL"],
                ];
                break;
            case 'bittable_attributes' :
                $data = [
                    0,
                    ["id" => 'primary', "text" => "PRIMARY"],
                    ["id" => 'foreign', "text" => "FOREIGN"],
                    ["id" => 'unique', "text" => "UNIQUE"]
                ];
                break;
            case 'query_mode' :
                $data = [
                    0,
                    ["id" => 'select', "text" => "Select"],
                    ["id" => 'insert', "text" => "Insert"],
                    ["id" => 'update', "text" => "Update"],
                    ["id" => 'delete', "text" => "Delete"],
                ];
            break;
            case 'query_table' :
                $data = DB::table('bittable')->select('bittable_id as id','bittable_name as text')->whereNull('bittable_parent_id')->get();
                $data->prepend(0);
            break;
            case 'query_field' :
                $data = DB::table('bittable')->select('bittable_id as id','bittable_name as text')->whereNotNull('bittable_parent_id')->get();
                $data->prepend(0);
            break;
        }
        return response()->json($data);
    }

    public function createTree($list, $parent, $parentId = null)
    {
        $tree = array();
        foreach ($list as $key => $eachNode) {
            if ($eachNode->$parent == $parentId) {
                $eachNode->children = $this->createTree($list, $parent, $eachNode->bittable_id);
                $tree[] = $eachNode;
                unset($list[$key]);
            }
        }
        return $tree;
    }
    public function bitQuery(Request $request){
        $data = new stdClass();
        $field = '';
        $th = [];
        foreach($request->field as $k => $v){
            $hm = DB::table('bittable')->where('bittable_id',$v)->first()->bittable_name;
            $th[] = $hm;
            $field .= $hm.',';
        }
        $data->th = $th;
        $field = rtrim($field, ',');
        $table = DB::table('bittable')->where('bittable_id',$request->table)->first()->bittable_name;
        $data->data = DB::select($request->mode.' '.$field.' FROM '.$table);
        return response()->json($data);
    }
}
