<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Token;
use Tests\TestCase;

class AuthRefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Client', 'users');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_refresh_token(): void
    {
        $user = User::create([
            'name' => 'Refresh User',
            'email' => 'refresh@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'refresh@test.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200);
        $originalToken = $loginResponse->json('token');

        // Refresh token
        $refreshResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$originalToken,
            'Accept' => 'application/json',
        ])->postJson('/api/auth/refresh');

        $refreshResponse->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
                'user' => ['id', 'name', 'email', 'role', 'role_display_name', 'permissions'],
            ]);

        $newToken = $refreshResponse->json('token');
        $this->assertNotEquals($originalToken, $newToken, 'New token should be different from original');

        // Verify old token is revoked
        // Get all tokens for user, sorted by created_at desc
        $allTokens = Token::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Should have at least 2 tokens (original + new)
        $this->assertGreaterThanOrEqual(2, $allTokens->count(), 'Should have at least 2 tokens');
        
        // The second token (index 1) should be the old one and should be revoked
        if ($allTokens->count() >= 2) {
            $oldTokenRecord = $allTokens->get(1);
            $this->assertTrue($oldTokenRecord->revoked, 'Old token should be revoked');
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function refresh_requires_authentication(): void
    {
        $response = $this->postJson('/api/auth/refresh');

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function refresh_returns_user_data(): void
    {
        $user = User::create([
            'name' => 'Refresh User',
            'email' => 'refresh2@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'refresh2@test.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        $refreshResponse = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->postJson('/api/auth/refresh');

        $refreshResponse->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => 'student',
                ],
            ]);
    }
}

