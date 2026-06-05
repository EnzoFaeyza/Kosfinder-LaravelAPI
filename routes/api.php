<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KostController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;


//REGISTER & LOGIN
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//KOS
Route::get('/kosts', [KostController::class, 'index']);
Route::get('/kosts/{id}', [KostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-kosts', [KostController::class, 'myKosts']);
    Route::post('/kosts', [KostController::class, 'store']);
    Route::put('/kosts/{id}', [KostController::class, 'update']);
    Route::delete('/kosts/{id}', [KostController::class, 'destroy']);
});

//REVIEW
Route::get('/kosts/{id}/reviews', [ReviewController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});

//BOOKING
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::get('/owner-bookings', [BookingController::class, 'ownerBookings']);
    Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
});

Route::post('/midtrans/notification', [PaymentController::class, 'notification']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/midtrans/payment/{bookingId}', [PaymentController::class, 'createSnapToken']);
});




