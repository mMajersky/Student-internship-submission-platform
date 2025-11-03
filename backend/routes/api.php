<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\InternshipPdfController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\InternshipDocumentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\GarantController;


// PDF generation routes from feature/Generate_PDF_template
Route::get('/vykaz-generate/{internship}', [InternshipPdfController::class, 'generate']);

// User retrieval route from develop
Route::get('/user', function (Request $request) {
    $user = $request->user();

    // <-- ZMENA 1: Prikáž Laravelu, aby načítal aj prepojený študentský profil
    $user->load('student');

    // Get permissions for user role (same logic as in AuthController)
    $permissions = [
        'admin' => ['manage_users', 'manage_internships', 'manage_announcements'],
        'garant' => ['manage_internships', 'manage_announcements'],
        'company' => ['review_interns'],
        'student' => ['create_internships'],
    ];
    $userPermissions = $permissions[$user->role] ?? [];

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role, // This is a string, not a relationship
        'role_display_name' => ucfirst($user->role ?? 'unknown'),
        'permissions' => $userPermissions, // Return actual permissions based on role
        // <-- ZMENA 2: Pridaj načítaný profil do JSON odpovede
        'student' => $user->student,
        'email_notifications' => $user->email_notifications ?? true,
    ]);
})->middleware('auth:api');

// User profile and settings routes (all authenticated users)
Route::middleware(['auth:api'])->group(function () {
    Route::get('/user/settings', [UserProfileController::class, 'getSettings']);
    Route::put('/user/settings/email-notifications', [UserProfileController::class, 'updateEmailNotifications']);
    Route::put('/user/profile', [UserProfileController::class, 'updateProfile']);
    Route::put('/user/password', [UserProfileController::class, 'changePassword']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});

// Auth routes from develop
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::middleware('auth:api')->post('/auth/refresh', [AuthController::class, 'refresh']);
Route::middleware('auth:api')->post('/auth/logout', [AuthController::class, 'logout']);

// DEBUG: Check authentication status
Route::get('/debug-auth', function (Request $request) {
    try {
        $token = $request->bearerToken();
        $user = $request->user();
        
        return response()->json([
            'has_bearer_token' => !empty($token),
            'token_preview' => $token ? substr($token, 0, 20) . '...' : null,
            'auth_check' => auth()->check(),
            'auth_api_check' => auth('api')->check(),
            'user_found' => $user !== null,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? null,
            'user_email' => $user->email ?? null,
            'user_role' => $user->role ?? null,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null,
        ]);
    }
});

// Public announcements endpoint - accessible to everyone
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
    // Documents for garants
    Route::get('/internships/{internship}/documents', [InternshipDocumentController::class, 'index']);
    Route::get('/internships/{internship}/documents/{document}', [InternshipDocumentController::class, 'download']);

    // Internship email management
    Route::post('/internships/{id}/resend-approval-email', [InternshipController::class, 'resendApprovalEmail']);
    
    // Comment routes for internships (Garant only can create/update/delete)
    Route::get('/internships/{internship}/comments', [CommentController::class, 'index']);
    Route::post('/internships/{internship}/comments', [CommentController::class, 'store']);
    Route::get('/internships/{internship}/comments/{comment}', [CommentController::class, 'show']);
    Route::put('/internships/{internship}/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/internships/{internship}/comments/{comment}', [CommentController::class, 'destroy']);
    
    // Student routes - for dropdown selection
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    
    // Company routes - for dropdown selection
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    
    // Garant management routes
    Route::get('/garants', [GarantController::class, 'index']);
    Route::post('/garants', [GarantController::class, 'store']);
    Route::get('/garants/{id}', [GarantController::class, 'show']);
    Route::put('/garants/{id}', [GarantController::class, 'update']);
    Route::delete('/garants/{id}', [GarantController::class, 'destroy']);
});

// Student routes - accessible by students
Route::middleware(['auth:api', 'role:student'])->prefix('student')->group(function () {
    // Access to companies for dropdown - view only
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    
    // Internship management for students - view their own and create new
    Route::get('/internships', [InternshipController::class, 'studentIndex']);
    Route::post('/internships', [InternshipController::class, 'studentStore']);
    
    // Students can view comments on their internships (read-only)
    Route::get('/internships/{internship}/comments', [CommentController::class, 'index']);
    Route::get('/internships/{internship}/comments/{comment}', [CommentController::class, 'show']);

    // Documents: upload/download signed agreement, metadata, and generated agreement download
    // Špecifickejšie routes musia byť pred všeobecnejšími!
    Route::get('/internships/{internshipId}/documents/agreement-signed/meta', [StudentDocumentController::class, 'getSignedAgreementMeta']);
    Route::post('/internships/{internshipId}/documents/agreement-signed', [StudentDocumentController::class, 'uploadSignedAgreement']);
    Route::get('/internships/{internshipId}/documents/agreement-signed', [StudentDocumentController::class, 'downloadSignedAgreement']);
    Route::delete('/internships/{internshipId}/documents/agreement-signed', [StudentDocumentController::class, 'deleteSignedAgreement']);
    Route::get('/internships/{internshipId}/documents/agreement-generated', [StudentDocumentController::class, 'downloadGeneratedAgreement']);
});

// Company routes
Route::middleware(['auth:api', 'role:company'])->group(function () {
    // Future company-specific routes
});

// Company action routes for internship confirmation/rejection (public for email links)
Route::get('/internships/company-action', [InternshipController::class, 'companyAction']);

// Admin-only routes
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Future admin-only routes
});
