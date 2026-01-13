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
use App\Http\Controllers\ExternalInternshipController;


// Company action routes for internship confirmation/rejection (public for email links)
// MUST be at the top, before any middleware groups, to ensure it's truly public
Route::get('/internships/company-action', [InternshipController::class, 'companyAction']);

// PDF generation routes from feature/Generate_PDF_template
Route::get('/vykaz-generate/{internship}', [InternshipPdfController::class, 'generate']);

// User retrieval route from develop
Route::get('/user', function (Request $request) {
    $user = $request->user('api'); // Explicitly use api guard

    // <-- ZMENA 1: Prikáž Laravelu, aby načítal aj prepojený študentský profil
    $user->load('student');

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role, // This is a string, not a relationship
        'role_display_name' => ucfirst($user->role ?? 'unknown'),
        'permissions' => [], // No permission system yet
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

// Auth routes from develop - with rate limiting for security
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

// Password reset - sends email with reset link (no auth required)
Route::post('/password/forgot', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkApi']);

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
    // Documents for garants
    Route::get('/internships/{internship}/documents', [InternshipDocumentController::class, 'index']);
    Route::get('/internships/{internship}/documents/{document}', [InternshipDocumentController::class, 'download']);

    // Internship email management
    Route::post('/internships/{id}/resend-approval-email', [InternshipController::class, 'resendApprovalEmail']);
    Route::post('/internships/{id}/send-evaluation-email', [InternshipController::class, 'sendEvaluationEmail']);

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

    // Company request management routes (garant only)
    Route::get('/company-requests', [CompanyController::class, 'listRequests']);
    Route::get('/company-requests/{id}', [CompanyController::class, 'show']);
    Route::post('/company-requests/{id}/approve', [CompanyController::class, 'approve']);
    Route::post('/company-requests/{id}/reject', [CompanyController::class, 'reject']);
});

// Company registration - accessible by both authenticated students and public (no auth required)
Route::post('/companies/create', [CompanyController::class, 'createCompany']);

// Student routes - accessible by students
Route::middleware(['auth:api', 'role:student'])->prefix('student')->group(function () {
    // Access to companies for dropdown - view only
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);

    // Internship management for students - view their own and create new
    Route::get('/internships', [InternshipController::class, 'studentIndex']);
    Route::get('/internships/{id}', [InternshipController::class, 'studentShow']);
    Route::post('/internships', [InternshipController::class, 'studentStore']);
    Route::post('/internships/{id}/send-report-to-company', [InternshipController::class, 'sendReportToCompany']);

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

    // Report scan documents
    Route::get('/internships/{internshipId}/documents/report-scan/meta', [StudentDocumentController::class, 'getReportScanMeta']);
    Route::post('/internships/{internshipId}/documents/report-scan', [StudentDocumentController::class, 'uploadReportScan']);
});

// Company routes
Route::middleware(['auth:api', 'role:company'])->prefix('company')->group(function () {
    // Internship management for companies - view internships assigned to them
    Route::get('/internships', [InternshipController::class, 'companyIndex']);
    Route::get('/internships/{id}', [InternshipController::class, 'companyShow']);

    // Documents for companies - view and validate
    Route::get('/internships/{internship}/documents', [InternshipDocumentController::class, 'companyIndex']);
    Route::get('/internships/{internship}/documents/{document}', [InternshipDocumentController::class, 'companyDownload']);
    Route::post('/internships/{internship}/documents/{document}/validate', [InternshipDocumentController::class, 'companyValidate']);
});


// Admin-only routes
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Future admin-only routes
});


//charts
use App\Http\Controllers\StatsController;

Route::prefix('stats')
    ->group(function () {

        Route::get('/students-trend', [StatsController::class, 'studentsTrend']);
        Route::get('/internship-types', [StatsController::class, 'internshipTypes']);
        Route::get('/top-companies', [StatsController::class, 'topCompanies']);
        Route::get('/all-companies', [StatsController::class, 'allCompanies']);
        Route::get('/internship-summary', [StatsController::class, 'internshipSummary']);
        Route::get('/internships/export', [StatsController::class, 'exportCsv']);

// External third-party API routes - OAuth client authenticated only (no user JWTs, no role restrictions)
    Route::middleware(['oauth'])->prefix('external')->group(function () {
        // Get all internships as objects
        Route::get('/internships', [ExternalInternshipController::class, 'index']);

        // Defend internship - change status from 'schválená' to 'obhájená'
        Route::post('/internships/{id}/defend', [ExternalInternshipController::class, 'defend']);
    });
});
