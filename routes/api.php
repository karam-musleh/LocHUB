<?php

use App\Http\Controllers\Api\DashBoard\LocationController;
use App\Http\Controllers\Api\DashBoard\OfferController;
use App\Http\Controllers\Api\Hubs\HubController;
use App\Http\Controllers\Api\Hubs\SocialController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('location', LocationController::class);
Route::Post('/register', [RegisterController::class, 'register']);
Route::Post('/login', [RegisterController::class, 'login']);
Route::Post('/logout', [RegisterController::class, 'logout'])->middleware('auth:api');
Route::Post('/refresh', [RegisterController::class, 'refresh'])->middleware('auth:api');
Route::Get('/profile', [RegisterController::class, 'profile'])->middleware('auth:api');
Route::Put('/profile', [RegisterController::class, 'updateProfile'])->middleware('auth:api');





Route::prefix('hubs')->middleware('auth:api')->group(function () {

    // جلب كل الهب الخاصة بالمستخدم الحالي
    Route::get('my', [HubController::class, 'myHubs']);

    // إنشاء هب جديد
    Route::post('', [HubController::class, 'store']);

    // جلب تفاصيل هب بالـ slug
    Route::get('{slug}', [HubController::class, 'show']);

    // تحديث هب
    Route::put('{slug}', [HubController::class, 'update']); // أو PUT/PATCH

    // حذف هب
    Route::delete('{slug}', [HubController::class, 'destroy']);
});
Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::patch('hubs/{hub}/status', [HubController::class, 'changeStatus']);
});



Route::prefix('hubs/{hub}')->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);      // كل خدمات الهب
    Route::get('/services/{service}', [ServiceController::class, 'show']);   // تفاصيل خدمة
});

Route::prefix('hubs/{hub}')->middleware('auth:api')->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);     // اضافة خدمة للهب
    Route::put('/services/{service}', [ServiceController::class, 'update']); // تعديل خدمة
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']); // حذف خدمة
    Route::get('/offers', [OfferController::class, 'index']);

    // إضافة عرض جديد للهب
    Route::post('/offers', [OfferController::class, 'store']);

    // تفاصيل عرض محدد
    Route::get('/offers/{offer}', [OfferController::class, 'show']);

    // تعديل عرض محدد
    Route::put('/offers/{offer}', [OfferController::class, 'update']);

    // حذف عرض محدد
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy']);
});

