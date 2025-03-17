<?php

use App\Http\Controllers\API\AuthController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('offres', OffreController::class);
    // Route::get('/offres/userOffres', [OffreController::class, 'indexForUser']); 
    Route::put('updateProfile', [AuthController::class, 'updateProfile']);
    Route::post('/offres/{id}/postuler', [OffreController::class, 'postuler']);


});