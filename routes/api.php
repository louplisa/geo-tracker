<?php

use App\Http\Controllers\Api\LocationController;
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
Route::get('/locations/nearby', [LocationController::class, 'nearby'])->name('locations.nearby');
Route::post('/locations/name-and-zip-code', [LocationController::class, 'storeByNameAndZipCode'])->name('locations.zip-code');
Route::apiResource('locations', LocationController::class);
Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index'])->name('users.index');


