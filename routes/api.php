<?php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Payment_Transaction;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Currencies;
use App\Http\Controllers\Warehouses;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->group(function () {

    // Users API routes
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/me', [UserController::class, 'show']);

    // Orders API routes
    Route::get('/order/driver', [OrderController::class, 'showbydriver']);
    Route::get('/order/allbydriver', [OrderController::class, 'showallbydriver']);
    Route::get('/order/status/{status}', [OrderController::class, 'showbystatus']);
    Route::get('/order/driverarchive', [OrderController::class, 'showdriverarchive']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::get('/order', [OrderController::class, 'index']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::put('/order/assign/{id}', [OrderController::class, 'assigndriver']);
    Route::put('/order/status/{id}', [OrderController::class, 'updatestatus']);
    Route::delete('/order/{id}', [OrderController::class, 'destroy']);

    // Notifications API routes
    Route::post('/sendnotification', [NotificationController::class, 'sendnotification']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/user', [NotificationController::class, 'showbyuser']);
    Route::post('/notifyalluser', [NotificationController::class, 'notifyalluser']);

    // Payment Transactions API routes
    Route::put('/payment_transaction/{id}', [Payment_Transaction::class, 'update']);

});

// Authentication API routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/user', [UserController::class, 'store']);

// Payment API routes
Route::get('/payment_transaction/{id}', [Payment_Transaction::class, 'show']);

// Currencies API routes
Route::get('/currencies', [Currencies::class, 'index']);

// Warehouses API routes
Route::get('/warehouses', [Warehouses::class, 'index']);
