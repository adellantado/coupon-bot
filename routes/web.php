<?php


Route::get('/', function () {
    return view('welcome');
});

Route::get('/bot', 'MainController@listen');
Route::post('/bot', 'MainController@listen');
