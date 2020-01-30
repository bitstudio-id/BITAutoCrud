<?php

Route::prefix('crud')->group(function() {
    Route::get('/get/{table}', 'CrudController@get')->name('crud.get');
    Route::get('/datatable/{table}', 'CrudController@dataTable')->name('crud.dataTable');
    Route::get('/select', 'CrudController@select')->name('crud.select');
    Route::post('/post/{table}', 'CrudController@post')->name('crud.post');
    Route::get('/edit/{table}/{id}', 'CrudController@edit')->name('crud.edit');
    Route::get('/delete/{table}/{id}', 'CrudController@delete')->name('crud.delete');
});

