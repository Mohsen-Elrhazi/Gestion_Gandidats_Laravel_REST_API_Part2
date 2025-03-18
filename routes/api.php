<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OffreController;
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



Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/refresh',[UserController::class,'refresh']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

//    return auth()->user();
});

// Route::middleware('auth:sanctum')->group(function () {
//     Route::resource('offres', OffreController::class);
//     // Route::get('/offres/userOffres', [OffreController::class, 'indexForUser']); 
//     Route::put('updateProfile', [UserController::class, 'updateProfile']);
//     Route::post('/offres/{id}/postuler', [OffreController::class, 'postuler']);


// });