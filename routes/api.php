<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/precio','App\Http\Controllers\BusquedaController@precio');
Route::get('/viviendas','App\Http\Controllers\BusquedaController@viviendas');
Route::get('/tweets','App\Http\Controllers\BusquedaController@tweets');
Route::get('/noticias','App\Http\Controllers\BusquedaController@noticias');
Route::get('/restaurantes','App\Http\Controllers\BusquedaController@restaurantes');
Route::get('/bbdd','App\Http\Controllers\BusquedaController@TEST_BBDD');
Route::get('/crear_usuario','App\Http\Controllers\BusquedaController@crear_usuario');
Route::get('/registro_usuario','App\Http\Controllers\BusquedaController@registro_usuario');
