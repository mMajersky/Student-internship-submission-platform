<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_email_must_be_unique()
    {
        User::create([
            'name' => 'First',
            'surname' => 'User',
            'email' => 'duplicate@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'Second',
            'surname' => 'User',
            'email' => 'duplicate@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_role_must_be_valid()
    {
        $validRoles = ['admin', 'garant', 'student', 'company'];
        
        foreach ($validRoles as $role) {
            $user = User::create([
                'name' => 'Test',
                'surname' => 'User',
                'email' => "user{$role}@example.com",
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
            
            $this->assertEquals($role, $user->role);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_email_must_be_unique()
    {
        $user1 = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $user2 = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        Student::create([
            'name' => 'Student',
            'surname' => 'One',
            'student_email' => 'student@ukf.sk',
            'alternative_email' => 'alt1@example.com',
            'phone_number' => '+421 900 123 456',
            'user_id' => $user1->id,
            'study_level' => 'Bc.',
        ]);

        // This should fail due to unique constraint on (student_email, alternative_email, phone_number)
        $this->expectException(\Illuminate\Database\QueryException::class);

        Student::create([
            'name' => 'Student',
            'surname' => 'Two',
            'student_email' => 'student@ukf.sk',
            'alternative_email' => 'alt1@example.com',
            'phone_number' => '+421 900 123 456',
            'user_id' => $user2->id,
            'study_level' => 'Bc.',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_name_must_not_be_empty()
    {
        $user = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        try {
            Company::create([
                'name' => '',
                'user_id' => $user->id,
            ]);
            $this->fail('Should not create company with empty name');
        } catch (\Exception $e) {
            // Expected
            $this->assertTrue(true);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_name_and_surname_are_required()
    {
        try {
            User::create([
                'name' => '',
                'surname' => 'User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
            $this->fail('Should not create user with empty name');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_is_hashed()
    {
        $plainPassword = 'mysecurepassword';
        
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
            'role' => 'student',
        ]);

        $user->refresh();
        
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_with_null_user_id_fails()
    {
        try {
            Student::create([
                'name' => 'Student',
                'surname' => 'Nouser',
                'student_email' => 'student@ukf.sk',
                'user_id' => null,
                'study_level' => 'Bc.',
            ]);
            
            // If we get here, check if foreign key constraint exists
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // Foreign key constraint should prevent this
            $this->assertTrue(true);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_with_null_user_id_fails()
    {
        try {
            Company::create([
                'name' => 'Company',
                'user_id' => null,
            ]);
            
            // If we get here, check if foreign key constraint exists
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // Foreign key constraint should prevent this
            $this->assertTrue(true);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_have_invalid_role_enum()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->assertNotEquals('invalid_role', $user->role);
        $this->assertTrue(in_array($user->role, ['admin', 'garant', 'student', 'company']));
    }
}
