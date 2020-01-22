<?php

Route::prefix('crud')->group(function() {
    Route::get('/{table}', 'CrudController@get')->name('crud.get');
    Route::post('/post', 'CrudController@post')->name('crud.post');
    Route::delete('/delete/{param}', 'CrudController@delete')->name('crud.delete');
});
Route::prefix('bit')->group(function() {
    Route::get('/get/{table}', 'CrudController@bitGetForm')->name('bit.Get');
    Route::get('/datatable/{table}', 'CrudController@bitGetDataTable')->name('bit.datatable');
    Route::get('/bitMenuGet', 'CrudController@bitMenuGet')->name('bit.bitMenuGet');
    Route::post('/save', 'CrudController@bitSave')->name('bit.Save');
    Route::post('/menusave', 'CrudController@bitMenuSave')->name('bit.Save');
    Route::get('/select/{param}', 'CrudController@select')->name('bit.select');
});
