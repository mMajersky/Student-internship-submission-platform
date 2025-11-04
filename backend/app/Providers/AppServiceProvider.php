<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Note: Personal access tokens (used by createToken()) ignore tokensExpireIn
        // They use personalAccessTokensExpireIn instead
        
        // OAuth access tokens expire in 1 hour (shorter for better security)
        // These are used by password grant client (not currently used)
        Passport::tokensExpireIn(now()->addHour());
        
        // Refresh tokens expire in 30 days (longer for better UX)
        Passport::refreshTokensExpireIn(now()->addDays(30));
        
        // Personal access tokens expire based on remember_me:
        // - Default: 1 hour (regular sessions)
        // - Remember me: 6 months (set dynamically in login method)
        // However, Passport doesn't support dynamic expiration per token,
        // so we'll use 1 hour default and handle remember_me via frontend storage
        Passport::personalAccessTokensExpireIn(now()->addHour());
    }
}
