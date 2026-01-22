<?php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
    //User API Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::get('/user/{id}', [UserController::class, 'show']);

    //Authentication API Route
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

    //Order API Routes
    Route::get('/order',[OrderController::class,'index']);
    Route::post('/order',[OrderController::class,'store']);
    Route::get('/order/{id}',[OrderController::class,'show']);
    Route::get('/order/driver/{driverid}',[OrderController::class,'showbydriver']);
    Route::get('/order/status/{status}',[OrderController::class,'showbystatus']);
    Route::put('order/status/{id}',[OrderController::class,'updatestatus']);
    Route::delete('order/{id}',[OrderController::class,'destroy']);