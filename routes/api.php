<?php

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

Route::middleware(['throttle:api', 'auth:sanctum'])->post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);


//USER CONTROLLER
Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,moderator,user'])->group(function () {
    Route::get('user', [\App\Http\Controllers\UserController::class, 'getUser']);
    Route::patch('user', [\App\Http\Controllers\UserController::class, 'patchUser']);
    Route::post('user/avatar', [\App\Http\Controllers\UserController::class, 'saveOrReplaceUserAvatar']);
});


//GENRE CONTROLLER
Route::middleware(['throttle:api'])->group(function () {
    Route::get('genre', [\App\Http\Controllers\GenreController::class, 'getAll']);
});

Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,moderator'])->group(function () {
    Route::patch('genre/{id}', [\App\Http\Controllers\GenreController::class, 'update']);
});


//FILM CONTROLLER
Route::middleware(['throttle:api'])->group(function () {
    Route::get('films', [\App\Http\Controllers\FilmController::class, 'getFilms']);
    Route::get('films/{id}', [\App\Http\Controllers\FilmController::class, 'getFilmById']);
    Route::get('films/{id}/similar', [\App\Http\Controllers\FilmController::class, 'getSimilarFilms']);
});

Route::middleware(['throttle:api'])->group(function () {
    Route::post('films', [\App\Http\Controllers\FilmController::class, 'createFilm']);
    Route::patch('films/{id}', [\App\Http\Controllers\FilmController::class, 'patchFilm']);
});

//TEST CONTROLLER
Route::middleware(['throttle:api'])->group(function () {
    Route::get('publicEndpoint', [\App\Http\Controllers\FilmController::class, 'publicEndpoint']);
});

Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,moderator'])->group(function () {
    Route::get('privateEndpoint', [\App\Http\Controllers\FilmController::class, 'privateEndpoint']);
    Route::get('privateEndpointWithAttributes', [\App\Http\Controllers\FilmController::class, 'privateEndpointWithAttributes']);
});


//COMMENT CONTROLLER
Route::middleware(['throttle:api'])->group(function () {
    Route::get('films/{id}/comments', [\App\Http\Controllers\CommentController::class, 'getCommentsByFilmId']);
});

Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,moderator,user'])->group(function () {
    Route::post('films/{id}/comments', [\App\Http\Controllers\CommentController::class, 'createComment']);
    Route::patch('comments/{id}', [\App\Http\Controllers\CommentController::class, 'patchComment']);
    Route::delete('comments/{id}', [\App\Http\Controllers\CommentController::class, 'deleteComment']);
});


//PROMO CONTROLLER
Route::middleware(['throttle:api'])->group(function () {
    Route::get('promo', [\App\Http\Controllers\PromoController::class, 'getPromo']);
});

Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,moderator'])->group(function () {
    Route::post('promo/{id}', [\App\Http\Controllers\PromoController::class, 'setPromo']);
});


//FAVORITES CONTROLLER
Route::middleware(['throttle:api', 'auth:sanctum', 'role:admin,user'])->group(function () {
    Route::get('favorite', [\App\Http\Controllers\FavoritesController::class, 'getFavorites']);
    Route::post('films/{id}/favorite', [\App\Http\Controllers\FavoritesController::class, 'addToFavorites']);
    Route::delete('films/{id}/favorite', [\App\Http\Controllers\FavoritesController::class, 'removeFromFavorites']);
});
