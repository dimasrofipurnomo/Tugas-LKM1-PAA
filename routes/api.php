<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\IphoneController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\PaymentController;

// Customers
Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/customers/{id}', [CustomerController::class, 'show']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{id}', [CustomerController::class, 'update']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

// iPhones
Route::get('/iphones', [IphoneController::class, 'index']);
Route::get('/iphones/{id}', [IphoneController::class, 'show']);
Route::post('/iphones', [IphoneController::class, 'store']);
Route::put('/iphones/{id}', [IphoneController::class, 'update']);
Route::delete('/iphones/{id}', [IphoneController::class, 'destroy']);

// Rentals
Route::get('/rentals', [RentalController::class, 'index']);
Route::get('/rentals/{id}', [RentalController::class, 'show']);
Route::post('/rentals', [RentalController::class, 'store']);
Route::patch('/rentals/{id}/status', [RentalController::class, 'updateStatus']);
Route::delete('/rentals/{id}', [RentalController::class, 'destroy']);

// Payments
Route::get('/payments', [PaymentController::class, 'index']);
Route::get('/payments/{id}', [PaymentController::class, 'show']);
Route::post('/payments', [PaymentController::class, 'store']);
Route::patch('/payments/{id}/confirm', [PaymentController::class, 'confirm']);
Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);