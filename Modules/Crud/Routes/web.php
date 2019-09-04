<?php

Route::prefix('crud')->group(function() {
    Route::get('/{table}', 'CrudController@get')->name('crud.get');
    Route::post('/post', 'CrudController@post')->name('crud.post');
    Route::delete('/delete/{param}', 'CrudController@delete')->name('crud.delete');
});
