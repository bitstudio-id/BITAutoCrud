<?php

Route::prefix('/bit')->group(function() {
    Route::get('/get/{table}', 'BITController@bitGetForm')->name('bit.get');
    Route::get('/datatable/{table}', 'BITController@bitGetDataTable')->name('bit.datatable');
    Route::get('/query', 'BITController@bitQuery')->name('bit.query');
    Route::get('/bitGetDataDetail/{id}', 'BITController@bitGetDataDetail')->name('bit.bitGetDataDetail');
    Route::get('/bitMenuGet', 'BITController@bitMenuGet')->name('bit.menuget');
    Route::post('/save', 'BITController@bitSave')->name('bit.save');
    Route::get('/delete/{id}', 'BITController@bitDelete')->name('bit.delete');
    Route::post('/menusave', 'BITController@bitMenuSave')->name('bit.menusave');
    Route::get('/select/{param}', 'BITController@select')->name('bit.select');
});
