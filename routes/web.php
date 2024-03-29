<?php

use Illuminate\Support\Facades\Route;

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
        return view('welcome');
});

Auth::routes();

Route::any('/collector/test', 'CollectorController@test')->name('collector.test');
Route::any('/collector/findXPath', 'CollectorController@findXPath')->name('collector.findXPath');
Route::any('/collector/process/{id}', 'CollectorController@process')->name('collector.process');
Route::any('/collector/putData/{collector}', 'CollectorController@putData')->name('collector.putData');
Route::any('/f/{id}/{type}/{group}', 'CollectorController@frame')->name('collector.frame');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test', 'HomeController@test')->name('home');

Route::any('/collector/{id}/delete', 'CollectorController@delete')->name('collector.delete');
Route::resource('collector', 'CollectorController')->middleware('auth');

Route::get('/howto', function () {
    return view('static/howto');
});