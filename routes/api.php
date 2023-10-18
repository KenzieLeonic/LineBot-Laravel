<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/movie', [\App\Http\Controllers\MovieController::class, 'replyMessage']);

Route::get('/movie/pushMessage',[\App\Http\Controllers\MovieController::class,'index']);
Route::apiResource('/movie/create', \App\Http\Controllers\MovieApiController::class);

