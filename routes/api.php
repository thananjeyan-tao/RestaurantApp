<?php

use App\Http\Controllers\V1\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('orders', [OrderController::class, 'store']);
Route::get('orders/active', [OrderController::class, 'activeOrders']);
Route::post('orders/{orderId}/complete', [OrderController::class, 'completeOrder']);
Route::get('orders/priority', [OrderController::class, 'priorityQueue']);
