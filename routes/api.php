<?php

use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

// Define your API routes here
Route::prefix('properties')->group(function () {
    Route::post('/', [PropertyController::class, 'store']);  // Add a new property
    Route::post('{id}', [PropertyController::class, 'update']);
    Route::get('/', [PropertyController::class, 'index']);   // Get all properties
    Route::get('{id}', [PropertyController::class, 'show']);  // Get a specific property by ID
    Route::delete('{id}', [PropertyController::class, 'destroy']);
});
