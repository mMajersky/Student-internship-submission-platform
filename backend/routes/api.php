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
//Route::get('/announcements/published', [AnnouncementController::class, 'published']); //dava error 500, table announcements sa pouzivat pravdepodbne nebude

// Protected routes for Admin/Garant
Route::middleware(['auth:api', 'role:admin,garant'])->group(function () {
    Route::match(['GET', 'PUT'], '/announcement', [AnnouncementController::class, 'single']);
});

// Student routes
Route::middleware(['auth:api', 'role:student'])->group(function () {
    // Future student-specific routes
});

// Company routes
Route::middleware(['auth:api', 'role:company'])->group(function () {
    // Future company-specific routes
});

// Admin-only routes
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Future admin-only routes
});

