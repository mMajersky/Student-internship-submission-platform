<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Token expiration time in minutes
     */
    protected const TOKEN_EXPIRATION_MINUTES = 60;

    /**
     * Send password reset email to user
     */
    protected function sendPasswordResetEmail(User $user, string $token): void
    {
        $resetUrl = url('/password/reset') . '?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]);

        EmailService::send(PasswordResetMail::class, $user->email, [
            'resetUrl' => $resetUrl,
            'expirationMinutes' => self::TOKEN_EXPIRATION_MINUTES,
            'userName' => $user->name ?? $user->email,
        ]);
    }

    /**
     * Generate and store a password reset token
     */
    protected function createToken(string $email): string
    {
        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Generate a new token
        $token = Str::random(64);

        // Store the token (hashed)
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    /**
     * Validate a password reset token
     */
    protected function validateToken(string $email, string $token): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return false;
        }

        // Check if token has expired
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(self::TOKEN_EXPIRATION_MINUTES)->isPast()) {
            // Delete expired token
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        // Verify the token hash
        return Hash::check($token, $record->token);
    }

    /**
     * Delete a password reset token
     */
    protected function deleteToken(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }

    /**
     * Handle the forgot password request via API (for Vue frontend)
     */
    public function sendResetLinkApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        // Only send email if user exists, but always return success
        if ($user) {
            $token = $this->createToken($email);
            $this->sendPasswordResetEmail($user, $token);
        }

        // Always return success to prevent email enumeration
        return response()->json([
            'success' => true,
            'message' => 'Ak je táto e-mailová adresa registrovaná, obdržíte odkaz na obnovenie hesla.',
        ]);
    }

    /**
     * Show the reset password form
     */
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return view('auth.reset-password', [
                'error' => 'Neplatný odkaz na obnovenie hesla.',
                'showRequestNewLink' => true,
            ]);
        }

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return view('auth.reset-password', [
                'error' => 'Neplatný odkaz na obnovenie hesla.',
                'showRequestNewLink' => true,
            ]);
        }

        // Validate token
        if (!$this->validateToken($email, $token)) {
            return view('auth.reset-password', [
                'error' => 'Odkaz na obnovenie hesla je neplatný alebo expiroval. Prosím, požiadajte o nový.',
                'showRequestNewLink' => true,
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle the password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $token = $request->input('token');
        $email = $request->input('email');
        $password = $request->input('password');

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return view('auth.reset-password', [
                'error' => 'Používateľ s touto e-mailovou adresou neexistuje.',
                'showRequestNewLink' => true,
            ]);
        }

        // Validate token
        if (!$this->validateToken($email, $token)) {
            return view('auth.reset-password', [
                'error' => 'Odkaz na obnovenie hesla je neplatný alebo expiroval.',
                'showRequestNewLink' => true,
            ]);
        }

        // Update the password
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Delete the used token
        $this->deleteToken($email);

        return view('auth.reset-password', [
            'success' => true,
            'message' => 'Vaše heslo bolo úspešne zmenené. Teraz sa môžete prihlásiť.',
        ]);
    }
}
