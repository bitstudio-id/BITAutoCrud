<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/bit');
});
Route::get('/bit', function () {
    return view('welcome');
});
Route::get('/test', function (\Illuminate\Http\Request $request) {
    $exitCode = Artisan::call('make:migration create_'.$request->table.'_table');
    $result = preg_split('/\n|\r\n?/',Artisan::output());
    $result = substr($result[0], strpos($result[0], ":") + 1);
    $result = str_replace(' ', '', $result);
    while(file_exists(base_path('database\migrations\\'.$result.'.php'))){
        $data = preg_split('/\n|\r\n?/',File::get(base_path('database\migrations\\'.$result.'.php')));
//        $data = File::put(base_path('database\migrations\\'.$result.'.php'), 'Appended Text');
        return ($data);
        break;
    };

});
