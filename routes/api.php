<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\AuthController; //Para autenticar

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::get('/precio','App\Http\Controllers\BusquedaController@precio');
Route::get('/viviendas','App\Http\Controllers\BusquedaController@viviendas');
Route::get('/tweets','App\Http\Controllers\BusquedaController@tweets');
Route::get('/noticias','App\Http\Controllers\BusquedaController@noticias');
Route::get('/restaurantes','App\Http\Controllers\BusquedaController@restaurantes');
Route::get('/bbdd','App\Http\Controllers\BusquedaController@TEST_BBDD');
Route::get('/crear_usuario','App\Http\Controllers\BusquedaController@crear_usuario');
Route::get('/registro_usuario','App\Http\Controllers\BusquedaController@registro_usuario');
Route::get('/login_usuario','App\Http\Controllers\BusquedaController@login_usuario');

//ADMIN
Route::get('/select_busquedas','App\Http\Controllers\BusquedaController@select_busquedas_admin')->middleware('auth:api');
Route::get('/select_cache','App\Http\Controllers\BusquedaController@select_cache_admin')->middleware('auth:api');
Route::get('/select_query','App\Http\Controllers\BusquedaController@select_query')->middleware('auth:api');
Route::get('/select_ranking','App\Http\Controllers\BusquedaController@select_ranking')->middleware('auth:api');
Route::get('/select_usuarios','App\Http\Controllers\BusquedaController@select_usuarios')->middleware('auth:api');
Route::get('/update_user_validar','App\Http\Controllers\BusquedaController@update_user_validar')->middleware('auth:api');
Route::get('/select_ultimos_Tweets','App\Http\Controllers\BusquedaController@select_ultimos_Tweets')->middleware('auth:api');
Route::get('/modificar_ultimos_Tweets','App\Http\Controllers\BusquedaController@modificar_ultimos_Tweets')->middleware('auth:api');
