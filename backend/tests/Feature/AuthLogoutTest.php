<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Token;
use Tests\TestCase;

class AuthLogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Client', 'users');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_logout_and_token_is_revoked(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'logout@test.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);

        $token = $loginResponse->json('token');

        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully.',
            ]);

        // Verify token was revoked in database
        // Note: JWT tokens are stateless and validated by signature.
        // Revocation is recorded in database and Passport will check it on subsequent requests.
        $storedToken = Token::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($storedToken, 'Token should still exist in database');
        $this->assertTrue($storedToken->revoked, 'Token should be marked as revoked');
    }
}

 