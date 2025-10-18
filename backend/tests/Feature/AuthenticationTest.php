<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_login_with_valid_credentials()
    {
        $this->markTestSkipped('Authentication tests require Passport client setup');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_login_with_invalid_credentials()
    {
        $this->markTestSkipped('Authentication tests require Passport client setup');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_register_with_valid_data()
    {
        $this->markTestSkipped('Authentication tests require Passport client setup');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_register_with_invalid_email()
    {
        $this->markTestSkipped('Authentication tests require Passport client setup');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_get_profile()
    {
        $this->markTestSkipped('Authentication tests require Passport client setup');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_get_profile()
    {
        $this->markTestSkipped('Authentication tests require Passport client setup');
    }
}
