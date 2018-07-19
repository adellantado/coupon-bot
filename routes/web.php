<?php


Route::get('/', function () {
    return view('main');
});

Route::get('/bot', 'MainController@listen');
Route::post('/bot', 'MainController@listen');
