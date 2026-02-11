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
    //Route::get('/users', [UserController::class, 'index']);
    Route::put('/user', [UserController::class, 'update']);//done
    Route::get('/me', [UserController::class, 'show']);//done

    // Orders API routes
    Route::get('/order/driver', [OrderController::class, 'showbydriver']);//done
    Route::get('/order/allbydriver', [OrderController::class, 'showallbydriver']);//done
    //Route::get('/order/status/{status}', [OrderController::class, 'showbystatus']);
    Route::get('/order/driverarchive', [OrderController::class, 'showdriverarchive']);//done
    Route::get('/order', [OrderController::class, 'show']);//done
    //Route::get('/order', [OrderController::class, 'index']);
    //Route::post('/order', [OrderController::class, 'store']);
    Route::put('/order/assign', [OrderController::class, 'assigndriver']);//done
    Route::put('/order/status', [OrderController::class, 'updatestatus']);//done
    //Route::delete('/order/{id}', [OrderController::class, 'destroy']);

    // Notifications API routes
    //Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/user', [NotificationController::class, 'showbyuser']);//done
    //Route::post('/notifyalluser', [NotificationController::class, 'notifyalluser']);
    Route::put('/notifications/markasread', [NotificationController::class, 'markasread']);//done

    // Payment Transactions API routes
    Route::put('/payment_transaction', [Payment_Transaction::class, 'update']);

});

// Authentication API routes
Route::post('/login', [AuthController::class, 'login']);//done
Route::post('/logout', [AuthController::class, 'logout']);
//Route::post('/user', [UserController::class, 'store']);
Route::post('/sendnotification', [NotificationController::class, 'sendnotification']);
// Payment API routes
Route::get('/payment_transaction/{id}', [Payment_Transaction::class, 'show']);

// Currencies API routes
Route::get('/currencies', [Currencies::class, 'index']);

// Warehouses API routes
Route::get('/warehouses', [Warehouses::class, 'index']);
