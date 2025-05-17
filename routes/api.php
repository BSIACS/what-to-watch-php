<?php

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//AUTH CONTROLLER
Route::middleware(['throttle:api'])->group(function () {
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
});

Route::middleware(['throttle:api', 'auth:sanctum'])->get('logout', [\App\Http\Controllers\AuthController::class, 'logout']);


//USER CONTROLLER
Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,moderator,user'])->group(function () {
    Route::get('user', [\App\Http\Controllers\UserController::class, 'getUser']);
    Route::patch('user', [\App\Http\Controllers\UserController::class, 'patchUser']);
    Route::post('user/avatar', [\App\Http\Controllers\UserController::class, 'saveOrReplaceUserAvatar']);
});
