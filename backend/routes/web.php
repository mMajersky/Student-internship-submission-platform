<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Ensure auth middleware redirects don't break API usage
Route::get('/login', function () {
    return response()->json(['error' => 'Please use API login endpoint'], 401);
})->name('login');

// Protected endpoint - only for testing/development
// In production, this should be moved to API routes with proper authentication
Route::get('/users', [UserController::class, 'index']);

