<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\InternshipPdfController;

// PDF generation routes from feature/Generate_PDF_template
Route::get('/vykaz-generate/{internship}', [InternshipPdfController::class, 'generate']);
Route::get('/vykaz-generate-empty', [InternshipPdfController::class, 'generateEmpty']);

// User retrieval route from develop
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

// Auth routes from develop
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public announcements endpoint from develop
Route::get('/announcements/published', [AnnouncementController::class, 'published']);

// Protected routes for Admin/Garant from develop
Route::middleware(['auth:api', 'role:ADMIN,GARANT'])->group(function () {
    // Single announcement endpoint for landing page
    Route::match(['GET', 'PUT'], '/announcement', [AnnouncementController::class, 'single']);
});

// Student routes from develop
Route::middleware(['auth:api', 'role:STUDENT'])->group(function () {
    // Future student-specific routes
});

// Company routes from develop
Route::middleware(['auth:api', 'role:COMPANY'])->group(function () {
    // Future company-specific routes
});

// Admin-only routes from develop
Route::middleware(['auth:api', 'role:ADMIN'])->group(function () {
    // Future admin-only routes
});