<?php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Payment_Transaction;
use App\Http\Controllers\Auth\PasswordResetLinkController;
    //User API Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::get('/user/{id}', [UserController::class, 'show']);


    //Password Reset Route
    Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
    //Authentication API Route
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //Order API Routes
    Route::get('/order',[OrderController::class,'index']);
    Route::post('/order',[OrderController::class,'store']);
    Route::get('/order/{id}',[OrderController::class,'show']);
    Route::get('/order/driver/{driverid}',[OrderController::class,'showbydriver'])->middleware('auth:sanctum');
    Route::get('/order/status/{status}',[OrderController::class,'showbystatus']);
    Route::put('order/status/{id}',[OrderController::class,'updatestatus']);
    Route::delete('order/{id}',[OrderController::class,'destroy']);

    //Notification API Routes
    Route::post('/sendnotification',[App\Http\Controllers\NotificationController::class,'sendnotification']);

    //Payment Transaction API Routes
    Route::get('/payment_transaction/{id}',[Payment_Transaction::class,'show']);
    Route::put('/payment_transaction/{id}',[Payment_Transaction::class,'update']);

    //Currencies API Routes
    Route::get('/currencies',[App\Http\Controllers\Currencies::class,'index']); 