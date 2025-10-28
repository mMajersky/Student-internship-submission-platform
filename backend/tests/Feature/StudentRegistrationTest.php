<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;

class StudentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful student registration with all required fields
     */
    public function test_student_can_register_with_valid_data(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            'alternative_email' => 'jozef.personal@gmail.com',
            'phone_number' => '+421901234567',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'role'],
            ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Jozef',
            'email' => 'jozef.novak@student.ukf.sk',
            'role' => 'student',
        ]);

        // Verify student record was created with all fields including study_field
        $this->assertDatabaseHas('students', [
            'name' => 'Jozef',
            'surname' => 'Novák',
            'student_email' => 'jozef.novak@student.ukf.sk',
            'alternative_email' => 'jozef.personal@gmail.com',
            'phone_number' => '+421901234567',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ]);

        // Verify the student is linked to the user
        $user = User::where('email', 'jozef.novak@student.ukf.sk')->first();
        $this->assertNotNull($user->student);
        $this->assertEquals('Informatika', $user->student->study_field);
    }

    /**
     * Test registration fails without surname for students
     */
    public function test_student_registration_requires_surname(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            // Missing surname
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['surname']);
    }

    /**
     * Test registration fails without study_field for students
     */
    public function test_student_registration_requires_study_field(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            'study_level' => 'Bc.',
            // Missing study_field
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['study_field']);
    }

    /**
     * Test registration fails with non-university email for students
     */
    public function test_student_registration_requires_university_email(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef@gmail.com', // Not a university email
            'password' => 'password123',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration accepts valid UKF student email domain
     */
    public function test_student_registration_accepts_student_ukf_domain(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Mária',
            'surname' => 'Kováčová',
            'email' => 'maria.kovacova@student.ukf.sk',
            'password' => 'password123',
            'study_level' => 'Mgr.',
            'study_field' => 'Ekonomika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('students', [
            'student_email' => 'maria.kovacova@student.ukf.sk',
            'study_field' => 'Ekonomika',
        ]);
    }

    /**
     * Test registration fails when alternative email is same as primary
     */
    public function test_student_registration_alternative_email_must_differ(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            'alternative_email' => 'jozef.novak@student.ukf.sk', // Same as email
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['alternative_email']);
    }

    /**
     * Test registration fails without required address fields
     */
    public function test_student_registration_requires_address_fields(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            // Missing all address fields
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

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
    public function test_student_registration_fails_with_duplicate_email(): void
    {
        // Create a user first
        User::create([
            'name' => 'Existing User',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk', // Duplicate
            'password' => 'password123',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test student registration with minimal optional fields
     */
    public function test_student_registration_works_without_optional_fields(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            // No alternative_email
            // No phone_number
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('students', [
            'student_email' => 'jozef.novak@student.ukf.sk',
            'alternative_email' => null,
            'phone_number' => null,
        ]);
    }

    /**
     * Test password is properly hashed
     */
    public function test_student_password_is_hashed(): void
    {
        $studentData = [
            'role' => 'student',
            'name' => 'Jozef',
            'surname' => 'Novák',
            'email' => 'jozef.novak@student.ukf.sk',
            'password' => 'password123',
            'study_level' => 'Bc.',
            'study_field' => 'Informatika',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Tr. A. Hlinku',
            'house_number' => '2',
        ];

        $response = $this->postJson('/api/auth/register', $studentData);

        $response->assertStatus(201);

        $user = User::where('email', 'jozef.novak@student.ukf.sk')->first();
        
        // Password should be hashed, not plain text
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(\Hash::check('password123', $user->password));
    }
}
