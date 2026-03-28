<?php

use App\Http\Controllers\Api\DashBoard\LocationController;
use App\Http\Controllers\Api\DashBoard\OfferController;
use App\Http\Controllers\Api\Hubs\HubController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [RegisterController::class, 'login']);

    Route::apiResource('location', LocationController::class)->only(['index', 'show']);


    /*
    |--------------------------------------------------------------------------
    | Protected Routes (auth:api)
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:api')->group(function () {

        // Auth
        Route::post('/logout', [RegisterController::class, 'logout']);
        Route::post('/refresh', [RegisterController::class, 'refresh']);

        // Profile
        Route::get('/profile', [RegisterController::class, 'profile']);
        Route::put('/profile', [RegisterController::class, 'updateProfile']);


        /*
        |--------------------------------------------------------------------------
        | Hubs Routes
        |--------------------------------------------------------------------------
        */

        Route::prefix('hubs')->group(function () {

            // My hubs
            Route::get('/my', [HubController::class, 'myHubs']);

            // CRUD hubs
            Route::post('/', [HubController::class, 'store']);
            // Route::post('/', [HubController::class, 'store']);
            Route::get('/{slug}', [HubController::class, 'show']);
            Route::put('/{slug}', [HubController::class, 'update']);
            Route::delete('/{slug}', [HubController::class, 'destroy']);


            /*
            |--------------------------------------------------------------------------
            | Services inside hub
            |--------------------------------------------------------------------------
            */

            Route::prefix('{hub}/services')->group(function () {

                Route::get('/', [ServiceController::class, 'index']);
                Route::get('/{service}', [ServiceController::class, 'show']);

                Route::post('/', [ServiceController::class, 'store']);
                Route::put('/{service}', [ServiceController::class, 'update']);
                Route::delete('/{service}', [ServiceController::class, 'destroy']);

            });


            /*
            |--------------------------------------------------------------------------
            | Offers inside hub
            |--------------------------------------------------------------------------
            */

            Route::prefix('{hub}/offers')->group(function () {

                Route::get('/', [OfferController::class, 'index']);
                Route::get('/{offer}', [OfferController::class, 'show']);

                Route::post('/', [OfferController::class, 'store']);
                Route::put('/{offer}', [OfferController::class, 'update']);
                Route::delete('/{offer}', [OfferController::class, 'destroy']);

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:api', 'admin'])->group(function () {

        Route::patch('/hubs/{hub}/status', [HubController::class, 'changeStatus']);

        Route::apiResource('location', LocationController::class)->except(['index', 'show']);

    });

});
