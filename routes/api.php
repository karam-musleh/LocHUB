<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\DashBorad\LocationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('location', LocationController::class);
Route::Post('/register', [RegisterController::class, 'register']);
Route::Post('/login', [RegisterController::class, 'login']);
Route::Post('/logout', [RegisterController::class, 'logout'])->middleware('auth:api');
Route::Post('/refresh', [RegisterController::class, 'refresh'])->middleware('auth:api');
