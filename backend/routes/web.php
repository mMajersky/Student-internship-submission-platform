<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Internship;
use App\Http\Controllers\EmailController;
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

        $internship = Internship::find($data['internship_id']);

        if (!$internship) {
            return view('emails.company-action', [
                'error' => 'Internship not found.',
                'success' => false
            ]);
        }

        // Check if internship is still in "Potvrdená" status (pending company action)
        if ($internship->status !== \App\Models\Internship::STATUS_POTVRDENA) {
            // Load relationships for the resolved view
            $internship->load(['student', 'company', 'garant.user']);
            return view('emails.internship-resolved', [
                'internship' => $internship
            ]);
        }

        // Update status based on action
        $newStatus = $data['action'] === 'confirm'
            ? \App\Models\Internship::STATUS_SCHVALENA // Company confirmed/approved
            : \App\Models\Internship::STATUS_ZAMIETNUTA; // Company rejected

        $internship->update(['status' => $newStatus]);

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
