<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    /**
     * Get user profile settings
     */
    public function getSettings()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'email_notifications' => $user->email_notifications,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching user settings: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching settings.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update email notification preference
     */
    public function updateEmailNotifications(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'email_notifications' => ['required', 'boolean']
            ]);

            $user->update([
                'email_notifications' => $validated['email_notifications']
            ]);

            return response()->json([
                'message' => 'Nastavenie notifikácií bolo úspešne aktualizované.',
                'email_notifications' => $user->email_notifications
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating email notifications: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating settings.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update user profile (name, email)
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Profil bol úspešne aktualizovaný.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating profile.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', Password::min(8), 'confirmed'],
            ]);

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Aktuálne heslo nie je správne.'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return response()->json([
                'message' => 'Heslo bolo úspešne zmenené.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error changing password: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while changing password.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

