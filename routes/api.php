<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CandidatureController;
use App\Http\Controllers\API\CompetenceController;
use App\Http\Controllers\API\OffreController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\UserController;
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



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::middleware('auth:api')->group(function () {
    Route::apiResource('competences', CompetenceController::class)->middleware('IsAdmin');
    Route::post('profile', [ProfileController::class, 'storeOrUpdate']);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::delete('profile', [ProfileController::class, 'destroy']);
    Route::apiResource('offres', OffreController::class);
    Route::put('offres/changerStatus/{id}', [OffreController::class,'activerOrDesactiver'])->middleware('IsAdmin');
    Route::get('users', [UserController::class,'index'])->middleware('IsAdmin');
    Route::get('users/{id}', [UserController::class,'show'])->middleware('IsAdmin');
    Route::put('users/changerStatus/{id}', [UserController::class,'activerOrDesactiver'])->middleware('IsAdmin');
    Route::delete('users/destroy/{id}', [UserController::class,'destroy'])->middleware('IsAdmin');
    Route::post('/postuler/offres/{id}', [CandidatureController::class, 'postuler']);});