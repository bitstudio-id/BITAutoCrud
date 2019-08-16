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
Route::get('/test', function () {
    $exitCode = Artisan::call('migrate:refresh', [
        '--force' => true,
    ]);
    return preg_split('/\n|\r\n?/',Artisan::output());
});
