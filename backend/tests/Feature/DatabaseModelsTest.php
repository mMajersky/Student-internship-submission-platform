<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Models\ContactPerson;
use App\Models\Internship;
use App\Models\Garant;
use App\Models\Document;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DatabaseModelsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_model_relationships_work()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter.hudec@ukf.sk',
            'user_id' => $user->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'city' => 'Nitra',
            'street' => 'Hlavná',
            'house_number' => '15A',
        ]);

        // Test user relationship
        $this->assertEquals($user->id, $student->user->id);
        $this->assertEquals($student->id, $user->student->id);

        // Test full name getter
        $this->assertEquals('Peter Hudec', $student->full_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_model_relationships_work()
    {
        $user = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        $company = Company::create([
            'name' => 'Test Company s.r.o.',
            'user_id' => $user->id,
            'state' => 'Slovensko',
            'city' => 'Bratislava',
            'street' => 'Vazovova',
            'house_number' => '10',
        ]);

        // Test user relationship
        $this->assertEquals($user->id, $company->user->id);

        // Test contact persons relationship
        $contactPerson = ContactPerson::create([
            'name' => 'Jana',
            'surname' => 'Veselá',
            'email' => 'jana@test.com',
            'company_id' => $company->id,
        ]);

        $this->assertCount(1, $company->contactPersons);
        $this->assertEquals($contactPerson->id, $company->contactPersons->first()->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_model_relationships_work()
    {
        // Create users
        $studentUser = User::create([
            'name' => 'Student',
            'surname' => 'User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $companyUser = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        // Create related models
        $student = Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter@ukf.sk',
            'user_id' => $studentUser->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'city' => 'Nitra',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'user_id' => $companyUser->id,
            'state' => 'Slovensko',
            'city' => 'Bratislava',
        ]);

        $garant = Garant::create([
            'name' => 'Garant',
            'surname' => 'User',
            'faculty' => 'Fakulta informatiky',
            'user_id' => null,
        ]);

        // Create internship
        $internship = Internship::create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'garant_id' => $garant->id,
            'status' => 'prebieha',
            'academy_year' => '2024/2025',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
        ]);

        // Test relationships
        $this->assertEquals($student->id, $internship->student->id);
        $this->assertEquals($company->id, $internship->company->id);
        $this->assertEquals($garant->id, $internship->garant->id);

        // Test reverse relationships
        $this->assertCount(1, $student->internships);
        $this->assertCount(1, $company->internships);
        $this->assertCount(1, $garant->internships);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_contact_person_pivot_relationship_works()
    {
        // Create basic models
        $studentUser = User::create([
            'name' => 'Student',
            'surname' => 'User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $companyUser = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        $student = Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter@ukf.sk',
            'user_id' => $studentUser->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'city' => 'Nitra',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'user_id' => $companyUser->id,
            'state' => 'Slovensko',
            'city' => 'Bratislava',
        ]);

        $contactPerson = ContactPerson::create([
            'name' => 'Jana',
            'surname' => 'Veselá',
            'email' => 'jana@test.com',
            'company_id' => $company->id,
        ]);

        $internship = Internship::create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'status' => 'prebieha',
            'academy_year' => '2024/2025',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
        ]);

        // Attach contact person to internship
        $internship->contactPersons()->attach($contactPerson->id);

        // Test pivot relationship
        $this->assertCount(1, $internship->contactPersons);
        $this->assertEquals($contactPerson->id, $internship->contactPersons->first()->id);

        $this->assertCount(1, $contactPerson->internships);
        $this->assertEquals($internship->id, $contactPerson->internships->first()->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function document_model_relationship_works()
    {
        // Create internship
        $studentUser = User::create([
            'name' => 'Student',
            'surname' => 'User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $companyUser = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        $student = Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter@ukf.sk',
            'user_id' => $studentUser->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'city' => 'Nitra',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'user_id' => $companyUser->id,
            'state' => 'Slovensko',
            'city' => 'Bratislava',
        ]);

        $internship = Internship::create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'status' => 'prebieha',
            'academy_year' => '2024/2025',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
        ]);

        // Create document
        $document = Document::create([
            'internship_id' => $internship->id,
            'type' => 'contract',
            'status' => 'uploaded',
            'file_path' => '/documents/contract.pdf',
            'name' => 'Internship Contract',
        ]);

        // Test relationship
        $this->assertEquals($internship->id, $document->internship->id);
        $this->assertCount(1, $internship->documents);
        $this->assertEquals($document->id, $internship->documents->first()->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function notification_model_relationship_works()
    {
        $user = User::create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'is_read' => false,
        ]);

        // Test relationship
        $this->assertEquals($user->id, $notification->user->id);

        // Test scope
        $unreadNotifications = Notification::unread()->get();
        $this->assertCount(1, $unreadNotifications);
        $this->assertEquals($notification->id, $unreadNotifications->first()->id);

        // Test mark as read
        $notification->markAsRead();
        $this->assertTrue($notification->is_read);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function garant_model_relationships_work()
    {
        $user = User::create([
            'name' => 'Garant',
            'surname' => 'User',
            'email' => 'garant@example.com',
            'password' => Hash::make('password'),
            'role' => 'garant',
        ]);

        $garant = Garant::create([
            'name' => 'Garant',
            'surname' => 'User',
            'faculty' => 'Fakulta informatiky',
            'user_id' => $user->id,
        ]);

        // Test user relationship
        $this->assertEquals($user->id, $garant->user->id);

        // Test full name getter
        $this->assertEquals('Garant User', $garant->full_name);
    }
}
