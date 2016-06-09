<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});



//icecast


Route::get('/ice', ['as' => 'ice.list', 'uses' => 'IceController@getIceList']);
Route::get('/ice/{id}', ['as' => 'ice', 'uses' => 'IceController@ice']);
Route::get('/ice/{id}/status', ['as' => 'ice.status', 'uses' => 'IceController@iceStatus']);
Route::post('/ice', ['as' => 'ice.create', 'uses' => 'IceController@iceCreate']);
Route::put('/ice/{id}', ['as' => 'ice.edit', 'uses' => 'IceController@iceEdit']);
Route::put('/ice/{id}/start', ['as' => 'ice.start', 'uses' => 'IceController@iceStart']);
Route::put('/ice/{id}/stop', ['as' => 'ice.stop', 'uses' => 'IceController@iceStop']);
Route::delete('/ice/{id}', ['as' => 'ice.destroy', 'uses' => 'IceController@iceDestroy']);


//mountpoint
Route::get('/ice/{id}/mount', ['as' => 'mount.list', 'uses' => 'MountController@mountList']);
Route::get('/mount/{id}/config', ['as' => 'single.mount.config', 'uses' => 'MountController@mountSingleConfig']);
Route::get('/mount/{id}', ['as' => 'mount.detail', 'uses' => 'MountController@mountDetail']);
Route::post('/ice/{id}/mount', ['as' => 'mount.create', 'uses' => 'MountController@mountCreate']);
Route::put('/mount/{id}', ['as' => 'mount.edit', 'uses' => 'MountController@mountEdit']);
Route::put('/mount/{id}/enable', ['as' => 'mount.start', 'uses' => 'MountController@mountStart']);
Route::put('/mount/{id}/disable', ['as' => 'mount.stop', 'uses' => 'MountController@mountStop']);
Route::delete('/mount/{id}', ['as' => 'mount.destroy', 'uses' => 'MountController@mountDestroy']);

//test
Route::get('/test', function () {
    return view('test');
});

Route::get('/ice/{id}/test2', function () {
    return view('test2');
});

Route::get('/ice/{id}/test3', function () {
    return view('test3');
});

Route::get('/mount/{id}/test4', function () {
    return view('test4');
});