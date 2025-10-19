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



Route::get('/add-user', function () {
    $random = rand(1000, 9999);

    $user = User::create([
        'role' => 'admin',
        'password' => bcrypt('secret123'),
        'email' => "admin{$random}@example.com"
    ]);

    return 'Používateľ pridaný s ID: ' . $user->id . "\n email: " . $user->email;
});

