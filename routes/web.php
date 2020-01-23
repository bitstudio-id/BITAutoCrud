<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {

    return '"\asd"';
});
