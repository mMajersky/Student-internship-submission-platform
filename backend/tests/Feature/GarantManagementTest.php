<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Garant;
use Laravel\Passport\Passport;

class GarantManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $garantUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->garantUser = User::create([
            'name' => 'Garant',
            'email' => 'garant@test.com',
            'password' => bcrypt('password'),
            'role' => 'garant',
        ]);

        $this->studentUser = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
    }

    public function test_admin_can_create_garant(): void
    {
        Passport::actingAs($this->adminUser);

        $garantData = [
            'name' => 'Peter',
            'surname' => 'Novák',
            'email' => 'p.novak@ukf.sk',
            'password' => 'password123',
            'faculty' => 'Fakulta prírodných vied',
        ];

        $response = $this->postJson('/api/garants', $garantData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'surname', 'faculty', 'user_id', 'email'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'p.novak@ukf.sk',
            'role' => 'garant',
        ]);

        $this->assertDatabaseHas('garants', [
            'name' => 'Peter',
            'surname' => 'Novák',
            'faculty' => 'Fakulta prírodných vied',
        ]);
    }

    public function test_admin_can_list_all_garants(): void
    {
        Passport::actingAs($this->adminUser);

        // Create test garants
        $garant1 = Garant::create([
            'name' => 'Peter',
            'surname' => 'Novák',
            'faculty' => 'FPV',
            'user_id' => $this->garantUser->id,
        ]);

        $garant2 = Garant::create([
            'name' => 'Jana',
            'surname' => 'Kováčová',
            'faculty' => 'FF',
            'user_id' => null,
        ]);

        $response = $this->getJson('/api/garants');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Peter'])
            ->assertJsonFragment(['name' => 'Jana']);
    }

    public function test_admin_can_get_single_garant(): void
    {
        Passport::actingAs($this->adminUser);

        $garant = Garant::create([
            'name' => 'Peter',
            'surname' => 'Novák',
            'faculty' => 'FPV',
            'user_id' => $this->garantUser->id,
        ]);

        $response = $this->getJson("/api/garants/{$garant->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $garant->id,
                    'name' => 'Peter',
                    'surname' => 'Novák',
                    'faculty' => 'FPV',
                ]
            ]);
    }

    public function test_admin_can_update_garant(): void
    {
        Passport::actingAs($this->adminUser);

        $garant = Garant::create([
            'name' => 'Peter',
            'surname' => 'Novák',
            'faculty' => 'FPV',
            'user_id' => $this->garantUser->id,
        ]);

        $updateData = [
            'name' => 'Pavol',
            'surname' => 'Nový',
            'faculty' => 'Fakulta filozofická',
        ];

        $response = $this->putJson("/api/garants/{$garant->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('garants', [
            'id' => $garant->id,
            'name' => 'Pavol',
            'surname' => 'Nový',
            'faculty' => 'Fakulta filozofická',
        ]);
    }

    public function test_admin_can_delete_garant(): void
    {
        Passport::actingAs($this->adminUser);

        $user = User::create([
            'name' => 'ToDelete',
            'email' => 'delete@test.com',
            'password' => bcrypt('password'),
            'role' => 'garant',
        ]);

        $garant = Garant::create([
            'name' => 'ToDelete',
            'surname' => 'Garant',
            'faculty' => 'FPV',
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/garants/{$garant->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('garants', [
            'id' => $garant->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_student_cannot_create_garant(): void
    {
        Passport::actingAs($this->studentUser);

        $garantData = [
            'name' => 'Peter',
            'surname' => 'Novák',
            'email' => 'p.novak@ukf.sk',
            'password' => 'password123',
            'faculty' => 'FPV',
        ];

        $response = $this->postJson('/api/garants', $garantData);

        $response->assertStatus(403);
    }

    public function test_create_garant_validates_required_fields(): void
    {
        Passport::actingAs($this->adminUser);

        $response = $this->postJson('/api/garants', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'surname', 'email', 'password']);
    }

    public function test_create_garant_validates_email_uniqueness(): void
    {
        Passport::actingAs($this->adminUser);

        $garantData = [
            'name' => 'Peter',
            'surname' => 'Novák',
            'email' => 'admin@test.com', // Email už existuje
            'password' => 'password123',
            'faculty' => 'FPV',
        ];

        $response = $this->postJson('/api/garants', $garantData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_garant_is_linked_to_user(): void
    {
        Passport::actingAs($this->adminUser);

        $garantData = [
            'name' => 'Peter',
            'surname' => 'Novák',
            'email' => 'p.novak@ukf.sk',
            'password' => 'password123',
            'faculty' => 'FPV',
        ];

        $response = $this->postJson('/api/garants', $garantData);

        $response->assertStatus(201);

        $user = User::where('email', 'p.novak@ukf.sk')->first();
        $this->assertNotNull($user);
        $this->assertEquals('garant', $user->role);

        $garant = Garant::where('name', 'Peter')->first();
        $this->assertNotNull($garant);
        $this->assertEquals($user->id, $garant->user_id);

        // Test relationship
        $this->assertNotNull($garant->user);
        $this->assertEquals($user->id, $garant->user->id);
    }
}
