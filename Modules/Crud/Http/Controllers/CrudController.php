<?php

namespace Modules\Crud\Http\Controllers;

use App\BitTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

class CrudController extends Controller
{
    public function get(Request $request, $table)
    {

    }

    public function bitGetDataTable(Request $request,$table){
        $data = new stdClass();
        $data->data = DB::table($table)->select($table . '.*')->get();
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
        $column = [];
        $column[] = ["title"=>"No", "data"=> null, "name"=> null];
        $ex = ['created_at', 'updated_at'];
        $scheme = Schema::getColumnListing($table);
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
                    ['label' => "Field Type", 'id' => "field[$index][bittable_type]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_type')],
                    ['label' => "Field Name", 'id' => "field[$index][bittable_name]", 'input' => "input", 'type' => "text", 'url' => ""],
                    ['label' => "Field Length/Value", 'id' => "field[$index][bittable_length]", 'input' => "input", 'type' => "text", 'url' => ""],
//                    ['label' => "Field Default", 'id' => "field[$index][bittable_default]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_default')],
                    ['label' => "Field Attributes", 'id' => "field[$index][bittable_attributes]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_attributes')],
                    ['label' => "Join Table Type", 'id' => "field[$index][bittable_join]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_join')],
                    ['label' => "Join To ID", 'id' => "field[$index][bittable_join_to_id]", 'input' => "select", 'type' => "select", 'url' => route('bit.select', 'bittable_to_id')],
                ],
        ];
        return response()->json($data);
    }

    public function bitSave(Request $request)
    {
        DB::beginTransaction();
        $data = BitTable::updateOrCreate(
            ['bittable_id' => $request->bittable_id],
            [
                'bittable_name' => $request->bittable_name,
                'bittable_type' => 'table',
            ]
        );
        foreach ($request->field as $val) {
            $val['bittable_parent_id'] = $data->bittable_id;
            $val['bittable_name'] = $data->bittable_name . '_' . $val['bittable_name'];
            BitTable::updateOrCreate(
                ['bittable_id' => $val['bittable_id']],
                $val
            );
        }
        DB::commit();
        DB::beginTransaction();
        Schema::create($data->bittable_name, function (Blueprint $table) {
            $table->timestamps();
        });
        DB::commit();
        $f = BitTable::where('bittable.bittable_parent_id', $data->bittable_id)
            ->with('join', 'join.parent')
            ->get();

        foreach ($f as $key => $value) {

            DB::beginTransaction();
            $length = 255;
            if ($value->bittable_length !== null) {
                $length = (int)$value->bittable_length;
                if (gettype($length) !== 'integer') {
                    $length = explode(",", $value->bittable_length);
                }
            }

            Schema::table($data->bittable_name, function ($table) use ($value, $length) {
                $type = $value->bittable_type;
                if ($value->bittable_attributes === 'unsigned') {
                    $table->$type($value->bittable_name)->unsigned();
                } else if ($value->bittable_attributes === 'primary') {
                    $table->$type($value->bittable_name, true, true);
                } else if ($value->bittable_attributes === 'unique') {
                    $table->$type($value->bittable_name)->unique();
                } else {
                    $table->$type($value->bittable_name, $length);
                }
                if ($value->bittable_join !== null || $value->bittable_join !== '' && $value['join']) {
                    $table->foreign($value->bittable_name)
                        ->references($value['join']['bittable_name'])->on($value['join']['parent']['bittable_name'])
                        ->onDelete('cascade')
                        ->onUpdate('cascade');
                }
            });
            DB::commit();
        }
    }

    public function select($p)
    {
        $data = [];
        switch ($p) {
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
                    ->select('bittable_id as id', 'bittable_parent_id', 'bittable_name as name', 'bittable_type')
                    ->get();
                foreach ($parent as $k => $v) {
                    if ($v->bittable_type === 'table') {
                        foreach ($parent as $key => $child) {
                            if ($child->bittable_parent_id === $v->id && $child->bittable_type !== 'table') {
                                $child->text[] = $v->name . ' → ' . $child->name;
                                $data[] = $child;
                            }
                        }
                    }
                }
                break;
            case 'bittable_type' :
                $data = [
                    0,
                    ["id" => "integer", "text" => "INT", "title" => "A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295"],
                    ["id" => "string", "text" => "VARCHAR", "title" => "A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size"],
                    ["id" => "TEXT", "text" => "TEXT", "title" => "A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes"],
                    ["id" => "DATE", "text" => "DATE", "title" => "A date, supported range is 1000-01-01 to 9999-12-31"],
                    ["text" => "Numeric",
                        "children" => [
                            ["id" => "TINYINT", "text" => "TINYINT", "title" => "A 1-byte integer, signed range is -128 to 127, unsigned range is 0 to 255"],
                            ["id" => "SMALLINT", "text" => "SMALLINT", "title" => "A 2-byte integer, signed range is -32,768 to 32,767, unsigned range is 0 to 65,535"],
                            ["id" => "MEDIUMINT", "text" => "MEDIUMINT", "title" => "A 3-byte integer, signed range is -8,388,608 to 8,388,607, unsigned range is 0 to 16,777,215"],
                            ["id" => "integer", "text" => "INT", "title" => "A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295"],
                            ["id" => "bigInteger", "text" => "BIGINT", "title" => "An 8-byte integer, signed range is -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807, unsigned range is 0 to 18,446,744,073,709,551,615"],
                            ["id" => "DECIMAL", "text" => "DECIMAL", "title" => "A fixed-point number (M, D) - the maximum number of digits (M) is 65 (default 10), the maximum number of decimals (D) is 30 (default 0)"],
                            ["id" => "FLOAT", "text" => "FLOAT", "title" => "A small floating-point number, allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38"],
                            ["id" => "DOUBLE", "text" => "DOUBLE", "title" => "A double-precision floating-point number, allowable values are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308"],
                            ["id" => "REAL", "text" => "REAL", "title" => "Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is a synonym for FLOAT)"],
                            ["id" => "BIT", "text" => "BIT", "title" => "A bit-field type (M), storing M of bits per value (default is 1, maximum is 64)"],
                            ["id" => "boolean", "text" => "BOOLEAN", "title" => "A synonym for TINYINT(1), a value of zero is considered false, nonzero values are considered true"],
                            ["id" => "SERIAL", "text" => "SERIAL", "title" => "An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE"],
                        ]],
                    ["text" => "Date and time",
                        "children" => [
                            ["id" => "date", "text" => "DATE", "title" => "A date, supported range is 1000-01-01 to 9999-12-31"],
                            ["id" => "dateTime", "text" => "DATETIME", "title" => "A date and time combination, supported range is 1000-01-01 00:00:00 to 9999-12-31 23:59:59"],
//                            ["id" => "TIMESTAMP", "text" => "TIMESTAMP", "title"=> "A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 03:14:07 UTC, stored as the number of seconds since the epoch (1970-01-01 00:00:00 UTC)"],
                            ["id" => "time", "text" => "TIME", "title" => "A time, range is -838:59:59 to 838:59:59"],
                            ["id" => "year", "text" => "YEAR", "title" => "A year in four-digit (4, default) or two-digit (2) format, the allowable values are 70 (1970) to 69 (2069) or 1901 to 2155 and 0000"],
                        ]],
                    ["text" => "String",
                        "children" => [
                            ["id" => "CHAR", "text" => "CHAR", "title" => "A fixed-length (0-255, default 1) string that is always right-padded with spaces to the specified length when stored"],
                            ["id" => "VARCHAR", "text" => "VARCHAR", "title" => "A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size"],
                            ["id" => "TINYTEXT", "text" => "TINYTEXT", "title" => "A TEXT column with a maximum length of 255 (2^8 - 1) characters, stored with a one-byte prefix indicating the length of the value in bytes"],
                            ["id" => "text", "text" => "TEXT", "title" => "A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes"],
                            ["id" => "MEDIUMTEXT", "text" => "MEDIUMTEXT", "title" => "A TEXT column with a maximum length of 16,777,215 (2^24 - 1) characters, stored with a three-byte prefix indicating the length of the value in bytes"],
                            ["id" => "LONGTEXT", "text" => "LONGTEXT", "title" => "A TEXT column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) characters, stored with a four-byte prefix indicating the length of the value in bytes"],
                            ["id" => "binary", "text" => "BINARY", "title" => "Similar to the CHAR type, but stores binary byte strings rather than non-binary character strings"],
                            ["id" => "VARBINARY", "text" => "VARBINARY", "title" => "Similar to the VARCHAR type, but stores binary byte strings rather than non-binary character strings"],
                            ["id" => "TINYBLOB", "text" => "TINYBLOB", "title" => "A BLOB column with a maximum length of 255 (2^8 - 1) bytes, stored with a one-byte prefix indicating the length of the value"],
                            ["id" => "MEDIUMBLOB", "text" => "MEDIUMBLOB", "title" => "A BLOB column with a maximum length of 16,777,215 (2^24 - 1) bytes, stored with a three-byte prefix indicating the length of the value"],
                            ["id" => "BLOB", "text" => "BLOB", "title" => "A BLOB column with a maximum length of 65,535 (2^16 - 1) bytes, stored with a two-byte prefix indicating the length of the value"],
                            ["id" => "LONGBLOB", "text" => "LONGBLOB", "title" => "A BLOB column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) bytes, stored with a four-byte prefix indicating the length of the value"],
                            ["id" => "enum", "text" => "ENUM", "title" => "An enumeration, chosen from the list of up to 65,535 values or the special '' error value"],
                            ["id" => "SET", "text" => "SET", "title" => "A single value chosen from a set of up to 64 members"],
                        ]],
                    ["text" => "Spatial",
                        "children" => [
                            ["id" => "GEOMETRY", "text" => "GEOMETRY", "title" => "A type that can store a geometry of any type"],
                            ["id" => "POINT", "text" => "POINT", "title" => "A point in 2-dimensional space"],
                            ["id" => "LINESTRING", "text" => "LINESTRING", "title" => "A curve with linear interpolation between points"],
                            ["id" => "POLYGON", "text" => "POLYGON", "title" => "A polygon"],
                            ["id" => "MULTIPOINT", "text" => "MULTIPOINT", "title" => "A collection of points"],
                            ["id" => "MULTILINESTRING", "text" => "MULTILINESTRING", "title" => "A collection of curves with linear interpolation between points"],
                            ["id" => "MULTIPOLYGON", "text" => "MULTIPOLYGON", "title" => "A collection of polygons"],
                            ["id" => "GEOMETRYCOLLECTION", "text" => "GEOMETRYCOLLECTION", "title" => "A collection of geometry objects of any type"],
                        ]],
                    ["text" => "JSON",
                        "children" => [
                            ["id" => "JSON", "text" => "JSON", "title" => "Stores and enables efficient access to data in JSON (JavaScript Object Notation) documents"],
                        ]]
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
                    ["id" => 'unsigned', "text" => "UNSIGNED"],
                    ["id" => 'primary', "text" => "PRIMARY"],
                    ["id" => 'unique', "text" => "UNIQUE"]
                ];
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
}


