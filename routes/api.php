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

Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
// Route::middleware('auth:api')->group(function () {

Route::middleware('auth:sanctum')->group(function () {
     Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
     Route::get('tasks', [\App\Http\Controllers\Api\TaskController::class,'index']);
     Route::post('tasks', [\App\Http\Controllers\Api\TaskController::class,'store']);
     Route::post('tasks/{id}', [\App\Http\Controllers\Api\TaskController::class,'update']);
     Route::delete('tasks/{id}', [\App\Http\Controllers\Api\TaskController::class,'delete']);

});


