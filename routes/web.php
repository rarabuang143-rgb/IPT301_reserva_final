<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('customers', CustomerController::class);
Route::resource('restaurants', RestaurantController::class);
Route::resource('foods', FoodController::class);
Route::resource('orders', OrderController::class);
Route::resource('deliveries', DeliveryController::class);
