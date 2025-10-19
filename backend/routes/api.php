<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\InternshipPdfController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CompanyController;

// PDF generation routes from feature/Generate_PDF_template
// Protected endpoint - requires authentication
Route::get('/vykaz-generate/{internship}', [InternshipPdfController::class, 'generate'])
    ->middleware('auth:api');

// User retrieval route from develop
Route::get('/user', function (Request $request) {
    $user = $request->user();

    // Get permissions based on role
    $permissions = [
        'admin' => ['manage_users', 'manage_internships', 'manage_announcements'],
        'garant' => ['manage_internships', 'manage_announcements'],
        'company' => ['review_interns'],
        'student' => ['create_internships'],
    ];

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role, // Direct string field
        'role_display_name' => ucfirst($user->role ?? ''),
        'permissions' => $permissions[$user->role] ?? []
    ]);
})->middleware('auth:api');

// Auth routes from develop
Route::post('/auth/login', [AuthController::class, 'login']);

// Public announcements endpoint from develop
Route::get('/announcements/published', [AnnouncementController::class, 'published']);

// Protected routes for Admin/Garant
Route::middleware(['auth:api', 'role:admin,garant'])->group(function () {
    Route::match(['GET', 'PUT'], '/announcement', [AnnouncementController::class, 'single']);
    
    // Internship management routes
    Route::get('/internships', [InternshipController::class, 'index']);
    Route::post('/internships', [InternshipController::class, 'store']);
    Route::get('/internships/{id}', [InternshipController::class, 'show']);
    Route::put('/internships/{id}', [InternshipController::class, 'update']);
    Route::delete('/internships/{id}', [InternshipController::class, 'destroy']);
    
    // Student routes - for dropdown selection
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    
    // Company routes - for dropdown selection
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
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

