<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
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

// Password Reset Routes
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset.update');

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

// Evaluation form route (web interface)
Route::get('/internships/evaluation', function (Request $request) {
    try {
        $token = $request->query('token');

        if (!$token) {
            return view('emails.internship-report-form', [
                'error' => 'Token is required.',
                'success' => false,
                'submitUrl' => config('app.url') . '/internships/evaluation'
            ]);
        }

        // Decrypt and validate the token
        try {
            $data = Crypt::decrypt($token);

            // Check if token has expired
            if (isset($data['expires_at']) && Carbon::now()->timestamp > $data['expires_at']) {
                return view('emails.internship-report-form', [
                    'error' => 'Token has expired.',
                    'success' => false,
                    'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
                ]);
            }

            // Validate required fields
            if (!isset($data['internship_id'], $data['action'], $data['timestamp'])) {
                return view('emails.internship-report-form', [
                    'error' => 'Invalid token format.',
                    'success' => false,
                    'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
                ]);
            }

            // Validate action
            if ($data['action'] !== 'evaluation') {
                return view('emails.internship-report-form', [
                    'error' => 'Invalid action.',
                    'success' => false,
                    'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
                ]);
            }

        } catch (\Exception $e) {
            return view('emails.internship-report-form', [
                'error' => 'Invalid or corrupted token.',
                'success' => false,
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }

        // Load internship with relationships including contact persons
        $internship = Internship::with(['student', 'company.contactPersons', 'garant.user'])->find($data['internship_id']);

        if (!$internship) {
            return view('emails.internship-report-form', [
                'error' => 'Internship not found.',
                'success' => false,
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }

        // Check if report has already been submitted
        $report = $internship->internship_report ?: [];
        if (isset($report['submitted_at'])) {
            return view('emails.internship-report-form', [
                'error' => 'Výkaz praxe už bol odoslaný pre túto prax.',
                'success' => false,
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }

        // Get company address
        $companyAddress = '';
        if ($internship->company) {
            $addressParts = array_filter([
                $internship->company->street,
                $internship->company->house_number,
                $internship->company->postal_code,
                $internship->company->city
            ]);
            $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');
        }

        // Get tutor name from contact persons (same as in agreement generation)
        $tutor = $internship->company && $internship->company->contactPersons 
            ? $internship->company->contactPersons->first() 
            : null;
        $tutorName = $tutor ? ($tutor->name . ' ' . $tutor->surname) : '';

        return view('emails.internship-report-form', [
            'success' => false,
            'internship' => $internship,
            'studentName' => $internship->student->name . ' ' . $internship->student->surname,
            'studentProgram' => $internship->student->study_field ?? '',
            'schoolName' => 'Univerzita Konštantína Filozofa v Nitre', // Default school name
            'companyName' => $companyAddress ?: ($internship->company->name ?? ''),
            'tutorName' => $tutorName,
            'academyYear' => $internship->academy_year,
            'startDate' => $internship->start_date?->format('Y-m-d'),
            'endDate' => $internship->end_date?->format('Y-m-d'),
            'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading evaluation form: ' . $e->getMessage());
        $token = $request->query('token', '');
        $submitUrl = $token ? config('app.url') . '/internships/evaluation?token=' . urlencode($token) : config('app.url') . '/internships/evaluation';
        return view('emails.internship-report-form', [
            'error' => 'An error occurred while loading the evaluation form.',
            'success' => false,
            'submitUrl' => $submitUrl
        ]);
    }
})->name('internships.evaluation');

// Submit evaluation route (web interface)
Route::post('/internships/evaluation', function (Request $request) {
    try {
        $token = $request->query('token');
        $submitUrl = $token ? config('app.url') . '/internships/evaluation?token=' . urlencode($token) : config('app.url') . '/internships/evaluation';

        if (!$token) {
            return view('emails.internship-report-form', [
                'error' => 'Token is required.',
                'success' => false,
                'submitUrl' => $submitUrl
            ]);
        }

        // Decrypt and validate the token
        try {
            $data = Crypt::decrypt($token);

            // Check if token has expired
            if (isset($data['expires_at']) && Carbon::now()->timestamp > $data['expires_at']) {
                return view('emails.internship-report-form', [
                    'error' => 'Token has expired.',
                    'success' => false,
                    'submitUrl' => $submitUrl
                ]);
            }

            // Validate required fields
            if (!isset($data['internship_id'], $data['action'], $data['timestamp'])) {
                return view('emails.internship-report-form', [
                    'error' => 'Invalid token format.',
                    'success' => false,
                    'submitUrl' => $submitUrl
                ]);
            }

            // Validate action
            if ($data['action'] !== 'evaluation') {
                return view('emails.internship-report-form', [
                    'error' => 'Invalid action.',
                    'success' => false,
                    'submitUrl' => $submitUrl
                ]);
            }

        } catch (\Exception $e) {
            return view('emails.internship-report-form', [
                'error' => 'Invalid or corrupted token.',
                'success' => false,
                'submitUrl' => $submitUrl
            ]);
        }

        // Load internship with contact persons
        $internship = Internship::with(['student', 'company.contactPersons'])->find($data['internship_id']);

        // Helper function to get tutor name from database
        $getTutorName = function($internship) {
            if (!$internship || !$internship->company || !$internship->company->contactPersons) {
                return '';
            }
            $tutor = $internship->company->contactPersons->first();
            return $tutor ? ($tutor->name . ' ' . $tutor->surname) : '';
        };

        if (!$internship) {
            // Get company address for error view
            $companyAddress = '';
            if ($internship && $internship->company) {
                $addressParts = array_filter([
                    $internship->company->street,
                    $internship->company->house_number,
                    $internship->company->postal_code,
                    $internship->company->city
                ]);
                $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');
            }

            return view('emails.internship-report-form', [
                'error' => 'Internship not found.',
                'success' => false,
                'submitUrl' => $submitUrl,
                'internship' => $internship ?? null,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname ?? '',
                'studentProgram' => $internship->student->study_field ?? '',
                'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
                'companyName' => $companyAddress ?: ($internship->company->name ?? ''),
                'tutorName' => $getTutorName($internship),
                'academyYear' => $internship->academy_year ?? '',
                'startDate' => $internship->start_date?->format('Y-m-d') ?? '',
                'endDate' => $internship->end_date?->format('Y-m-d') ?? ''
            ]);
        }

        // Get tutor name from database (same as in agreement generation)
        $tutorName = $getTutorName($internship);

        // Check if report has already been submitted
        $report = $internship->internship_report ?: [];
        if (isset($report['submitted_at'])) {
            $companyAddress = '';
            if ($internship->company) {
                $addressParts = array_filter([
                    $internship->company->street,
                    $internship->company->house_number,
                    $internship->company->postal_code,
                    $internship->company->city
                ]);
                $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');
            }

            return view('emails.internship-report-form', [
                'error' => 'Výkaz praxe už bol odoslaný pre túto prax.',
                'success' => false,
                'internship' => $internship,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'studentProgram' => $internship->student->study_field ?? '',
                'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
                'companyName' => $companyAddress ?: ($internship->company->name ?? ''),
                'tutorName' => $tutorName,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }

        // Validate report data (form mode)
        $reportData = $request->input('report', []);

        if (empty($reportData)) {
            // Get company address
            $companyAddress = '';
            if ($internship->company) {
                $addressParts = array_filter([
                    $internship->company->street,
                    $internship->company->house_number,
                    $internship->company->postal_code,
                    $internship->company->city
                ]);
                $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');
            }

            return view('emails.internship-report-form', [
                'error' => 'No report data provided.',
                'success' => false,
                'internship' => $internship,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'studentProgram' => $internship->student->study_field ?? '',
                'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
                'companyName' => $companyAddress ?: ($internship->company->name ?? ''),
                'tutorName' => $tutorName,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }

        // Define required criteria
        $requiredCriteria = [
            'organizovanie_a_planovanie_prace',
            'schopnost_pracovat_v_time',
            'schopnost_ucit_sa',
            'uroven_digitalnej_gramotnosti',
            'kultivovanost_prejavu',
            'pouzivanie_zauzivanych_vyrazov',
            'prezentovanie',
            'samostatnost',
            'adaptabilita',
            'flexibilita',
            'schopnost_improvizovat',
            'schopnost_prijimat_rozhodnutia',
            'schopnost_niest_zodpovednost',
            'dodrzovanie_etickych_zasad',
            'schopnost_jednania_s_ludmi'
        ];

        // Validate report data
        if (empty($reportData)) {
            return view('emails.internship-report-form', [
                'error' => 'No report data provided.',
                'success' => false,
                'internship' => $internship,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'companyName' => $internship->company->name,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'studentProgram' => $internship->student->study_field ?? '',
                'schoolName' => 'Univerzita Konštantína Filozofa v Nitre', // Default school name
                'tutorName' => $tutorName,
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }

        // Validate required fields
        $requiredFields = ['student_name', 'student_program', 'school_name', 'company_name', 'tutor_name', 'start_date', 'end_date', 'total_hours'];
        foreach ($requiredFields as $field) {
            if (!isset($reportData[$field]) || empty($reportData[$field])) {
                return view('emails.internship-report-form', [
                    'error' => 'Prosím vyplňte všetky povinné polia.',
                    'success' => false,
                    'internship' => $internship,
                    'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                    'companyName' => $internship->company->name,
                    'academyYear' => $internship->academy_year,
                    'startDate' => $internship->start_date?->format('Y-m-d'),
                    'endDate' => $internship->end_date?->format('Y-m-d'),
                    'studentProgram' => $internship->student->study_field ?? '',
                    'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
                    'tutorName' => $tutorName,
                    'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
                ]);
            }
        }

        // Validate activities
        if (!isset($reportData['activities']) || empty($reportData['activities']) || !is_array($reportData['activities'])) {
            // Get company address for error view
            $companyAddress = '';
            if ($internship && $internship->company) {
                $addressParts = array_filter([
                    $internship->company->street,
                    $internship->company->house_number,
                    $internship->company->postal_code,
                    $internship->company->city
                ]);
                $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');
            }
            
            return view('emails.internship-report-form', [
                'error' => 'Prosím pridajte aspoň jednu pracovnú činnosť.',
                'success' => false,
                'internship' => $internship,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'studentProgram' => $internship->student->study_field ?? '',
                'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
                'companyName' => $companyAddress ?: ($internship->company->name ?? ''),
                'tutorName' => $tutorName,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
            ]);
        }
        
        // Validate each activity has required fields (date, description, hours)
        foreach ($reportData['activities'] as $index => $activity) {
            if (!isset($activity['date']) || empty($activity['date']) ||
                !isset($activity['description']) || empty($activity['description']) ||
                !isset($activity['hours']) || empty($activity['hours'])) {
                
                // Get company address for error view
                $companyAddress = '';
                if ($internship && $internship->company) {
                    $addressParts = array_filter([
                        $internship->company->street,
                        $internship->company->house_number,
                        $internship->company->postal_code,
                        $internship->company->city
                    ]);
                    $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');
                }
                
                return view('emails.internship-report-form', [
                    'error' => 'Prosím vyplňte všetky polia pre pracovné činnosti (riadok ' . ($index + 1) . ').',
                    'success' => false,
                    'internship' => $internship,
                    'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                    'studentProgram' => $internship->student->study_field ?? '',
                    'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
                    'companyName' => $companyAddress ?: ($internship->company->name ?? ''),
                    'tutorName' => $tutorName,
                    'academyYear' => $internship->academy_year,
                    'startDate' => $internship->start_date?->format('Y-m-d'),
                    'endDate' => $internship->end_date?->format('Y-m-d'),
                    'submitUrl' => config('app.url') . '/internships/evaluation?token=' . urlencode($token)
                ]);
            }
        }

        // Validate evaluation criteria (if provided)
        if (isset($reportData['evaluation']) && is_array($reportData['evaluation'])) {
            $requiredCriteria = [
                'organizovanie_a_planovanie_prace',
                'schopnost_pracovat_v_time',
                'schopnost_ucit_sa',
                'uroven_digitalnej_gramotnosti',
                'kultivovanost_prejavu',
                'pouzivanie_zauzivanych_vyrazov',
                'prezentovanie',
                'samostatnost',
                'adaptabilita',
                'flexibilita',
                'schopnost_improvizovat',
                'schopnost_prijimat_rozhodnutia',
                'schopnost_niest_zodpovednost',
                'dodrzovanie_etickych_zasad',
                'schopnost_jednania_s_ludmi'
            ];

            // Auto-select n/a for unchecked criteria
            foreach ($requiredCriteria as $criterion) {
                if (!isset($reportData['evaluation'][$criterion]) || empty($reportData['evaluation'][$criterion])) {
                    $reportData['evaluation'][$criterion] = 'n/a';
                }
            }
        }

        // Add timestamp to report
        $reportData['submitted_at'] = Carbon::now()->toIso8601String();
        $reportData['submission_type'] = 'form';

        // Save report to internship
        $internship->internship_report = $reportData;
        
        // Also save evaluation separately if provided
        if (isset($reportData['evaluation'])) {
            $evaluation = $reportData['evaluation'];
            $evaluation['submitted_at'] = Carbon::now()->toIso8601String();
            $internship->evaluation = $evaluation;
        }
        
        $internship->save();

        return view('emails.internship-report-form', [
            'success' => true,
            'internship' => $internship,
            'message' => 'Výkaz praxe bol úspešne odoslaný.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error submitting evaluation: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        $token = $request->query('token', '');
        $submitUrl = $token ? config('app.url') . '/internships/evaluation?token=' . urlencode($token) : config('app.url') . '/internships/evaluation';
        
        // Try to load internship for error view
        $internship = null;
        $companyAddress = '';
        $errorTutorName = '';
        try {
            if ($token) {
                $data = Crypt::decrypt($token);
                $internship = Internship::with(['student', 'company.contactPersons'])->find($data['internship_id'] ?? null);
                if ($internship && $internship->company) {
                    $addressParts = array_filter([
                        $internship->company->street,
                        $internship->company->house_number,
                        $internship->company->postal_code,
                        $internship->company->city
                    ]);
                    $companyAddress = $internship->company->name . ($addressParts ? ', ' . implode(' ', $addressParts) : '');

                    // Get tutor name from contact persons
                    if ($internship->company->contactPersons) {
                        $tutor = $internship->company->contactPersons->first();
                        $errorTutorName = $tutor ? ($tutor->name . ' ' . $tutor->surname) : '';
                    }
                }
            }
        } catch (\Exception $loadEx) {
            \Log::error('Error loading internship in catch block: ' . $loadEx->getMessage());
        }
        
        $errorMessage = 'An error occurred while submitting the evaluation. Please try again.';
        if (config('app.debug')) {
            $errorMessage .= ' Error: ' . $e->getMessage();
        }
        
        return view('emails.internship-report-form', [
            'error' => $errorMessage,
            'success' => false,
            'submitUrl' => $submitUrl,
            'internship' => $internship,
            'studentName' => ($internship && $internship->student) ? ($internship->student->name . ' ' . $internship->student->surname) : '',
            'studentProgram' => ($internship && $internship->student) ? ($internship->student->study_field ?? '') : '',
            'schoolName' => 'Univerzita Konštantína Filozofa v Nitre',
            'companyName' => $companyAddress ?: (($internship && $internship->company) ? $internship->company->name : ''),
            'tutorName' => $errorTutorName,
            'academyYear' => $internship ? ($internship->academy_year ?? '') : '',
            'startDate' => $internship ? ($internship->start_date?->format('Y-m-d') ?? '') : '',
            'endDate' => $internship ? ($internship->end_date?->format('Y-m-d') ?? '') : ''
        ]);
    }
})->name('internships.evaluation.submit');
