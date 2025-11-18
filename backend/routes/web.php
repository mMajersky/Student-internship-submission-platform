<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Internship;
use App\Models\Notification;
use App\Http\Controllers\EmailController;
use App\Services\NotificationService;
use App\Mail\InternshipStatusChanged;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

// Ensure auth middleware redirects don't break API usage
Route::get('/login', function () {
    return response()->json(['error' => 'Please use API login endpoint'], 401);
})->name('login');

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

Route::get('/test-mail', [EmailController::class, 'test']);

// Company action route for email links (web interface)
Route::get('/internships/company-action', function (Request $request) {
    try {
        $token = $request->query('token');

        if (!$token) {
            return view('emails.company-action', [
                'error' => 'Token is required.',
                'success' => false
            ]);
        }

        // Decrypt and validate the token
        try {
            $data = Crypt::decrypt($token);

            // Check if token has expired
            if (isset($data['expires_at']) && Carbon::now()->timestamp > $data['expires_at']) {
                return view('emails.company-action', [
                    'error' => 'Token has expired.',
                    'success' => false
                ]);
            }

            // Validate required fields
            if (!isset($data['internship_id'], $data['action'], $data['timestamp'])) {
                return view('emails.company-action', [
                    'error' => 'Invalid token format.',
                    'success' => false
                ]);
            }

            // Validate action
            if (!in_array($data['action'], ['confirm', 'reject'])) {
                return view('emails.company-action', [
                    'error' => 'Invalid action.',
                    'success' => false
                ]);
            }

        } catch (\Exception $e) {
            return view('emails.company-action', [
                'error' => 'Invalid or corrupted token.',
                'success' => false
            ]);
        }

        // Load relationships before finding internship
        $internship = Internship::with(['student.user', 'company.user', 'garant.user'])->find($data['internship_id']);

        if (!$internship) {
            return view('emails.company-action', [
                'error' => 'Internship not found.',
                'success' => false
            ]);
        }

        // Check if internship is still in "potvrdená" status (pending company action)
        // Company can only confirm/reject after garant has confirmed (status "potvrdená")
        // Sequence: Created → Confirmed (garant) → Approved (company) → Defended
        if ($internship->status !== \App\Models\Internship::STATUS_POTVRDENA) {
            // Load relationships for the resolved view
            $internship->load(['student', 'company', 'garant.user']);
            return view('emails.internship-resolved', [
                'internship' => $internship
            ]);
        }

        // Store old status before update
        $oldStatus = $internship->status;

        // Update status based on action
        // After garant confirmed (status "potvrdená"), company confirms → "schválená" (Approved)
        // Company rejects → "zamietnutá"
        $newStatus = $data['action'] === 'confirm'
            ? \App\Models\Internship::STATUS_SCHVALENA // Company confirmed - status "schválená" (Approved)
            : \App\Models\Internship::STATUS_ZAMIETNUTA; // Company rejected

        $internship->update(['status' => $newStatus]);

        // Prepare email data
        $emailData = [
            'internshipId' => $internship->id,
            'studentName' => $internship->student->name . ' ' . $internship->student->surname,
            'companyName' => $internship->company->name,
            'academyYear' => $internship->academy_year,
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
        ];

        // If company approved (confirmed): send email to student and garant
        // Respects email_notifications setting
        if ($data['action'] === 'confirm') {
            // Send email to student (respects email_notifications setting)
            if ($internship->student && $internship->student->user) {
                NotificationService::createAndNotify(
                    $internship->student->user->id,
                    Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                    'Firma schválila vašu prax',
                    'Firma ' . $internship->company->name . ' schválila vašu prax. Stav: ' . $oldStatus . ' → ' . $newStatus,
                    ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $newStatus],
                    InternshipStatusChanged::class,
                    $emailData
                );
            }

            // Send email to ALL garants (users with role 'garant') - respects email_notifications setting
            $allGarants = User::where('role', 'garant')->whereNotNull('email')->get();
            
            foreach ($allGarants as $garantUser) {
                NotificationService::createAndNotify(
                    $garantUser->id,
                    Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                    'Firma schválila prax',
                    'Firma ' . $internship->company->name . ' schválila prax študenta ' . $internship->student->name . ' ' . $internship->student->surname . '. Stav: ' . $oldStatus . ' → ' . $newStatus,
                    ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $newStatus],
                    InternshipStatusChanged::class,
                    $emailData
                );
            }
        }

        // If company rejected: send email only to student - respects email_notifications setting
        if ($data['action'] === 'reject') {
            if ($internship->student && $internship->student->user) {
                NotificationService::createAndNotify(
                    $internship->student->user->id,
                    Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                    'Firma zamietla vašu prax',
                    'Firma ' . $internship->company->name . ' zamietla vašu prax. Stav: ' . $oldStatus . ' → ' . $newStatus,
                    ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $newStatus],
                    InternshipStatusChanged::class,
                    $emailData
                );
            }
        }

        return view('emails.company-action', [
            'success' => true,
            'message' => 'Internship ' . ($data['action'] === 'confirm' ? 'confirmed' : 'rejected') . ' successfully.',
            'action' => $data['action'],
            'internship' => $internship
        ]);

    } catch (\Exception $e) {
        return view('emails.company-action', [
            'error' => 'An error occurred while processing the action.',
            'success' => false
        ]);
    }
})->name('internships.company-action');
