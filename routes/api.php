<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CallbackController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UploadController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

// setelah membuat route untuk mengatasi eror agar logout hanya bisa di akses setelah user login maka
// tambahkan middelware auth:sanctum
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// membuat route untuk register
Route::post('/register', [AuthController::class, 'register']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('product', ProductController::class)->middleware('auth:sanctum');
Route::apiResource('banners', BannerController::class);

// route untuk upload image
Route::post('image/upload', [UploadController::class, 'uploadImage'])->middleware('auth:sanctum');

// route untuk upload multiple
Route::post('image/upload-multi', [UploadController::class, 'uploadMultipleImage'])->middleware('auth:sanctum');

// route untuk order
Route::post('/orders', [OrderController::class, 'order'])->middleware('auth:sanctum');

// route untuk callback midtrans
Route::post('midtrans/notification/handling', [CallbackController::class, 'callback']);

Route::post('fcm-token', [AuthController::class, 'updateFcmToken'])->middleware('auth:sanctum');
