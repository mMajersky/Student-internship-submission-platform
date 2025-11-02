<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public static function send(string $mailableClass, string $to, array $data = []): bool
    {
        try {
            Mail::to($to)->send(new $mailableClass($data));
            return true;
        } catch (\Throwable $e) {
            \Log::error("Email sending failed: {$e->getMessage()}");
            return false;
        }
    }

}
