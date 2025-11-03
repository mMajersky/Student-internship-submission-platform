<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class AuthRememberMeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Client', 'users');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_with_remember_me_returns_remember_flag(): void
    {
        $user = User::create([
            'name' => 'Remember User',
            'email' => 'remember@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'remember@test.com',
            'password' => 'password123',
            'remember_me' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'remember_me' => true,
            ])
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
                'remember_me',
                'user',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_without_remember_me_returns_false(): void
    {
        $user = User::create([
            'name' => 'No Remember User',
            'email' => 'noremember@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'noremember@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'remember_me' => false,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_with_remember_me_has_longer_expiration(): void
    {
        $user = User::create([
            'name' => 'Expiration User',
            'email' => 'expiration@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Without remember me
        $response1 = $this->postJson('/api/auth/login', [
            'email' => 'expiration@test.com',
            'password' => 'password123',
            'remember_me' => false,
        ]);

        $response1->assertStatus(200);
        $expiresIn1 = $response1->json('expires_in');
        $this->assertEquals(3600, $expiresIn1, 'Regular session should expire in 1 hour');

        // With remember me
        $response2 = $this->postJson('/api/auth/login', [
            'email' => 'expiration@test.com',
            'password' => 'password123',
            'remember_me' => true,
        ]);

        $response2->assertStatus(200);
        $expiresIn2 = $response2->json('expires_in');
        $this->assertEquals(15552000, $expiresIn2, 'Remember me should expire in 6 months (180 days)');
    }
}

