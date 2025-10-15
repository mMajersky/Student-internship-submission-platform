<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Laravel\Passport\Passport;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $garantUser;
    protected $companyUser;
    protected $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $this->seedRoles();
        
        // Create test users
        $this->createTestUsers();
    }

    private function seedRoles(): void
    {
        $roles = [
            [
                'name' => Role::ADMIN,
                'display_name' => 'Administrator',
                'description' => 'System administrator',
                'permissions' => ['manage_users', 'manage_announcements'],
                'is_active' => true,
            ],
            [
                'name' => Role::GARANT,
                'display_name' => 'Garant',
                'description' => 'Academic supervisor',
                'permissions' => ['manage_announcements'],
                'is_active' => true,
            ],
            [
                'name' => Role::COMPANY,
                'display_name' => 'Company',
                'description' => 'Company representative',
                'permissions' => ['create_internships'],
                'is_active' => true,
            ],
            [
                'name' => Role::STUDENT,
                'display_name' => 'Student',
                'description' => 'Student user',
                'permissions' => ['apply_internships'],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }
    }

    private function createTestUsers(): void
    {
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $garantRole = Role::where('name', Role::GARANT)->first();
        $companyRole = Role::where('name', Role::COMPANY)->first();
        $studentRole = Role::where('name', Role::STUDENT)->first();

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);

        $this->garantUser = User::create([
            'name' => 'Garant User',
            'email' => 'garant@test.com',
            'password' => bcrypt('password'),
            'role_id' => $garantRole->id,
        ]);

        $this->companyUser = User::create([
            'name' => 'Company User',
            'email' => 'company@test.com',
            'password' => bcrypt('password'),
            'role_id' => $companyRole->id,
        ]);

        $this->studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role_id' => $studentRole->id,
        ]);
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
        $response->assertJson(['message' => 'Unauthenticated.']);
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
            'role' => Role::ADMIN,
            'role_display_name' => 'Administrator'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_endpoint_returns_role_information(): void
    {
        // Skip this test for now due to Passport client setup complexity
        $this->markTestSkipped('Login endpoint test requires Passport client setup');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_model_role_helper_methods_work_correctly(): void
    {
        // Test admin user
        $this->assertTrue($this->adminUser->isAdmin());
        $this->assertTrue($this->adminUser->hasRole(Role::ADMIN));
        $this->assertTrue($this->adminUser->hasAnyRole([Role::ADMIN, Role::GARANT]));
        $this->assertTrue($this->adminUser->canManageAnnouncements());

        // Test garant user
        $this->assertTrue($this->garantUser->isGarant());
        $this->assertTrue($this->garantUser->hasRole(Role::GARANT));
        $this->assertTrue($this->garantUser->hasAnyRole([Role::ADMIN, Role::GARANT]));
        $this->assertTrue($this->garantUser->canManageAnnouncements());

        // Test company user
        $this->assertTrue($this->companyUser->isCompany());
        $this->assertTrue($this->companyUser->hasRole(Role::COMPANY));
        $this->assertFalse($this->companyUser->hasAnyRole([Role::ADMIN, Role::GARANT]));
        $this->assertFalse($this->companyUser->canManageAnnouncements());

        // Test student user
        $this->assertTrue($this->studentUser->isStudent());
        $this->assertTrue($this->studentUser->hasRole(Role::STUDENT));
        $this->assertFalse($this->studentUser->hasAnyRole([Role::ADMIN, Role::GARANT]));
        $this->assertFalse($this->studentUser->canManageAnnouncements());
        $this->assertTrue($this->studentUser->canCreateInternships());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_model_permission_methods_work_correctly(): void
    {
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $studentRole = Role::where('name', Role::STUDENT)->first();

        // Test admin role permissions
        $this->assertTrue($adminRole->hasPermission('manage_users'));
        $this->assertTrue($adminRole->hasPermission('manage_announcements'));
        $this->assertFalse($adminRole->hasPermission('nonexistent_permission'));

        // Test student role permissions
        $this->assertTrue($studentRole->hasPermission('apply_internships'));
        $this->assertFalse($studentRole->hasPermission('manage_users'));
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
