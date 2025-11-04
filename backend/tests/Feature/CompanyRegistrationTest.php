<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class CompanyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful company registration with all required fields
     */
    public function test_company_can_register_with_valid_data(): void
    {
        $companyData = [
            'role' => 'company',
            'name' => 'Testovacia Firma',
            'email' => 'firma@test.sk',
            'password' => 'password123',
            'company_name' => 'Tech Solutions s.r.o.',
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '82108',
            'street' => 'Hlavná',
            'house_number' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $companyData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'role'],
            ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Testovacia Firma',
            'email' => 'firma@test.sk',
            'role' => 'company',
        ]);

        // Verify company record was created
        $this->assertDatabaseHas('companies', [
            'name' => 'Tech Solutions s.r.o.',
            'state' => 'Slovensko',
            'city' => 'Bratislava',
            'postal_code' => '82108',
        ]);

        // Verify the company is linked to the user
        $user = User::where('email', 'firma@test.sk')->first();
        $this->assertNotNull($user->company);
        $this->assertEquals('Tech Solutions s.r.o.', $user->company->name);
    }

    /**
     * Test registration fails without company name
     */
    public function test_company_registration_requires_company_name(): void
    {
        $companyData = [
            'role' => 'company',
            'name' => 'Testovacia Firma',
            'email' => 'firma@test.sk',
            'password' => 'password123',
            // Missing company_name
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '82108',
            'street' => 'Hlavná',
            'house_number' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $companyData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_name']);
    }

    /**
     * Test registration fails without required address fields
     */
    public function test_company_registration_requires_address_fields(): void
    {
        $companyData = [
            'role' => 'company',
            'name' => 'Testovacia Firma',
            'email' => 'firma@test.sk',
            'password' => 'password123',
            'company_name' => 'Tech Solutions s.r.o.',
            // Missing all address fields
        ];

        $response = $this->postJson('/api/auth/register', $companyData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'state',
                'region',
                'city',
                'postal_code',
                'street',
                'house_number',
            ]);
    }

    /**
     * Test registration fails with duplicate email
     */
    public function test_company_registration_fails_with_duplicate_email(): void
    {
        // Create a user first
        User::create([
            'name' => 'Existing User',
            'email' => 'firma@test.sk',
            'password' => bcrypt('password'),
            'role' => 'company',
        ]);

        $companyData = [
            'role' => 'company',
            'name' => 'Testovacia Firma',
            'email' => 'firma@test.sk', // Duplicate
            'password' => 'password123',
            'company_name' => 'Tech Solutions s.r.o.',
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '82108',
            'street' => 'Hlavná',
            'house_number' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $companyData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test password is properly hashed
     */
    public function test_company_password_is_hashed(): void
    {
        $companyData = [
            'role' => 'company',
            'name' => 'Testovacia Firma',
            'email' => 'firma@test.sk',
            'password' => 'password123',
            'company_name' => 'Tech Solutions s.r.o.',
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '82108',
            'street' => 'Hlavná',
            'house_number' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $companyData);

        $response->assertStatus(201);

        $user = User::where('email', 'firma@test.sk')->first();

        // Password should be hashed, not plain text
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(\Hash::check('password123', $user->password));
    }
}
