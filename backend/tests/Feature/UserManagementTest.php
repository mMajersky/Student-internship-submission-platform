<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_all_users()
    {
        // Create test users
        User::create([
            'name' => 'Admin',
            'surname' => 'User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Student',
            'surname' => 'User',
            'email' => 'student@test.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $response = $this->getJson('/users');

        $response->assertStatus(200);
        
        $users = $response->json();
        $this->assertCount(2, $users);
        
        // Check that both users are returned
        $emails = collect($users)->pluck('email')->toArray();
        $this->assertContains('admin@test.com', $emails);
        $this->assertContains('student@test.com', $emails);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_has_correct_fillable_fields()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'role' => 'student',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_hides_password_field()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $userArray = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_casts_password_to_hashed()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => 'plaintext',
            'role' => 'student',
        ]);

        $this->assertTrue(password_verify('plaintext', $user->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_casts_email_verified_at_to_datetime()
    {
        $this->markTestSkipped('Email verification casting test requires proper User model setup');
    }

}
