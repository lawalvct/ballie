<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GlobalAuthController;

/*
|--------------------------------------------------------------------------
| Global Authentication API v1 Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication without requiring tenant slug.
| The system automatically detects the user's tenant from their email.
|
| Routes are prefixed with: /api/v1/auth
|
*/

// Public resources for registration
Route::get('/business-types', [GlobalAuthController::class, 'getBusinessTypes'])->name('business-types');
Route::get('/plans', [GlobalAuthController::class, 'getPlans'])->name('plans');

// Email-based tenant detection and authentication
Route::post('/login', [GlobalAuthController::class, 'login'])->name('login');
Route::post('/register', [GlobalAuthController::class, 'register'])->name('register');
Route::post('/forgot-password', [GlobalAuthController::class, 'forgotPassword'])->name('forgot-password');

// Check which tenant(s) an email belongs to
Route::post('/check-email', [GlobalAuthController::class, 'checkEmail'])->name('check-email');

// Select tenant when user belongs to multiple tenants
Route::post('/select-tenant', [GlobalAuthController::class, 'selectTenant'])->name('select-tenant');
