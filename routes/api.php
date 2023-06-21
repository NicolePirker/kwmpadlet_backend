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

Route::group(['middleware' => ['api', 'auth.jwt']], function(){
    Route::get('/', [\App\Http\Controllers\PadletController::class, 'index']);
    Route::get('/padlets', [\App\Http\Controllers\PadletController::class, 'index']);
    Route::get('/padlets/{id}', [\App\Http\Controllers\PadletController::class, 'findByPadletId']);
    Route::post('/padlets', [\App\Http\Controllers\PadletController::class, 'create']);
    Route::put('/padlets/{id}', [\App\Http\Controllers\PadletController::class, 'update']);
    Route::delete('/padlets/{id}', [\App\Http\Controllers\PadletController::class, 'delete']);

    Route::get('/entries/{entryId}', [\App\Http\Controllers\EntryController::class, 'findEntryById']);
    Route::post('/padlets/{padletId}/entries', [\App\Http\Controllers\EntryController::class, 'create']);
    Route::put('/entries/{entryId}', [\App\Http\Controllers\EntryController::class, 'update']);
    Route::delete('/entries/{entryId}', [\App\Http\Controllers\EntryController::class, 'delete']);

    Route::post('/comments/{entryId}', [\App\Http\Controllers\CommentController::class, 'create']);
    Route::put('/comments/{commentId}', [\App\Http\Controllers\CommentController::class, 'update']);
    Route::delete('/comments/{commentId}', [\App\Http\Controllers\CommentController::class, 'delete']);

    Route::post('/ratings/{entryId}', [\App\Http\Controllers\RatingController::class, 'create']);
    Route::put('/ratings/{ratingId}', [\App\Http\Controllers\RatingController::class, 'update']);
    Route::delete('/ratings/{ratingId}', [\App\Http\Controllers\RatingController::class, 'delete']);

    Route::post('auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});
Route::post('auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
