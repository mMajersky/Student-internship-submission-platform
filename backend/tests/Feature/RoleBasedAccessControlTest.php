<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Laravel\Passport\ClientRepository;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $garantUser;
    protected User $companyUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->garantUser = User::create([
            'name' => 'Garant User',
            'email' => 'garant@test.com',
            'password' => Hash::make('password123'),
            'role' => 'garant',
        ]);

        $this->companyUser = User::create([
            'name' => 'Company User',
            'email' => 'company@test.com',
            'password' => Hash::make('password123'),
            'role' => 'company',
        ]);

        $this->studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        Announcement::create([
            'content' => '<p>Initial announcement</p>',
            'is_published' => true,
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Client', 'users');
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_admin_and_garant_routes(): void
    {
        Passport::actingAs($this->adminUser);

        // Test announcements route (admin + garant)
        $response = $this->getJson('/api/announcement');
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function garant_can_access_admin_and_garant_routes(): void
    {
        Passport::actingAs($this->garantUser);

        // Test announcements route (admin + garant)
        $response = $this->getJson('/api/announcement');
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_cannot_access_admin_and_garant_routes(): void
    {
        Passport::actingAs($this->companyUser);

        // Test announcements route (admin + garant only)
        $response = $this->getJson('/api/announcement');
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Forbidden',
            'message' => 'You do not have permission to access this resource.'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_cannot_access_admin_and_garant_routes(): void
    {
        Passport::actingAs($this->studentUser);

        // Test announcements route (admin + garant only)
        $response = $this->getJson('/api/announcement');
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Forbidden',
            'message' => 'You do not have permission to access this resource.'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_access_protected_routes(): void
    {
        // Test announcements route without authentication
        $response = $this->getJson('/api/announcement');
        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_endpoint_returns_role_information(): void
    {
        Passport::actingAs($this->adminUser);

        $response = $this->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'role',
            'role_display_name',
            'permissions'
        ]);
        $response->assertJson([
            'role' => 'admin',
            'role_display_name' => 'Admin'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_endpoint_returns_role_information(): void
    {
        // Skip this test for now due to Passport client setup complexity
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'role', 'role_display_name', 'permissions'],
            ])
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonPath('user.role_display_name', 'Admin')
            ->assertJsonPath('user.permissions', ['manage_users', 'manage_internships', 'manage_announcements']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_role_helper_methods_work_correctly(): void
    {
        // Test admin user
        $this->assertTrue($this->adminUser->isAdmin());
        $this->assertTrue($this->adminUser->hasRole('admin'));
        $this->assertTrue($this->adminUser->hasAnyRole(['admin', 'garant']));
        $this->assertTrue($this->adminUser->canManageAnnouncements());

        // Test garant user
        $this->assertTrue($this->garantUser->isGarant());
        $this->assertTrue($this->garantUser->hasRole('garant'));
        $this->assertTrue($this->garantUser->hasAnyRole(['admin', 'garant']));
        $this->assertTrue($this->garantUser->canManageAnnouncements());

        // Test company user
        $this->assertTrue($this->companyUser->isCompany());
        $this->assertTrue($this->companyUser->hasRole('company'));
        $this->assertFalse($this->companyUser->hasAnyRole(['admin', 'garant']));
        $this->assertFalse($this->companyUser->canManageAnnouncements());

        // Test student user
        $this->assertTrue($this->studentUser->isStudent());
        $this->assertTrue($this->studentUser->hasRole('student'));
        $this->assertFalse($this->studentUser->hasAnyRole(['admin', 'garant']));
        $this->assertFalse($this->studentUser->canManageAnnouncements());
        $this->assertTrue($this->studentUser->canCreateInternships());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_returns_expected_permissions_for_each_role(): void
    {
        $roles = [
            'admin' => [
                'email' => 'admin@test.com',
                'permissions' => ['manage_users', 'manage_internships', 'manage_announcements'],
            ],
            'garant' => [
                'email' => 'garant@test.com',
                'permissions' => ['manage_internships', 'manage_announcements'],
            ],
            'company' => [
                'email' => 'company@test.com',
                'permissions' => ['review_interns'],
            ],
            'student' => [
                'email' => 'student@test.com',
                'permissions' => ['create_internships'],
            ],
        ];

        foreach ($roles as $role => $config) {
            $response = $this->postJson('/api/auth/login', [
                'email' => $config['email'],
                'password' => 'password123',
            ]);

            $response->assertStatus(200)
                ->assertJsonPath('user.role', $role)
                ->assertJsonPath('user.permissions', $config['permissions']);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function public_announcements_endpoint_is_accessible_to_everyone(): void
    {
        // Test without authentication
        $response = $this->getJson('/api/announcements/published');
        $response->assertStatus(200);

        // Test with authentication
        Passport::actingAs($this->studentUser);
        $response = $this->getJson('/api/announcements/published');
        $response->assertStatus(200);
    }
}
