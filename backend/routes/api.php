<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;

Route::get('/user', function (Request $request) {
    $user = $request->user();
    $user->load('role');
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role ? $user->role->name : null,
        'role_display_name' => $user->role ? $user->role->display_name : null,
        'permissions' => $user->role ? $user->role->permissions : []
    ]);
})->middleware('auth:api');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public announcements endpoint
Route::get('/announcements/published', [AnnouncementController::class, 'published']);

// Protected routes for Admin/Garant
Route::middleware(['auth:api', 'role:ADMIN,GARANT'])->group(function () {
    // Single announcement endpoint for landing page
    Route::match(['GET', 'PUT'], '/announcement', [AnnouncementController::class, 'single']);
});

// Student routes
Route::middleware(['auth:api', 'role:STUDENT'])->group(function () {
    // Future student-specific routes
});

// Company routes
Route::middleware(['auth:api', 'role:COMPANY'])->group(function () {
    // Future company-specific routes
});

// Admin-only routes
Route::middleware(['auth:api', 'role:ADMIN'])->group(function () {
    // Future admin-only routes
});
