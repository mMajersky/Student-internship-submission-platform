<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Garant;
use App\Models\ContactPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class InternshipTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $company;
    protected $internship;

    protected function setUp(): void
    {
        parent::setUp();

        // Create student
        $studentUser = User::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'email' => 'peter@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter.hudec@ukf.sk',
            'user_id' => $studentUser->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'city' => 'Nitra',
        ]);

        // Create company
        $companyUser = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        $this->company = Company::create([
            'name' => 'Test Company',
            'user_id' => $companyUser->id,
            'state' => 'Slovensko',
            'city' => 'Bratislava',
        ]);

        // Create internship
        $this->internship = Internship::create([
            'student_id' => $this->student->id,
            'company_id' => $this->company->id,
            'status' => 'prebieha',
            'academy_year' => '2024/2025',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_has_correct_relationships()
    {
        $this->assertEquals($this->student->id, $this->internship->student->id);
        $this->assertEquals($this->company->id, $this->internship->company->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_status_values_are_valid()
    {
        $validStatuses = ['prebieha', 'ukončená', 'zamietnutá', 'vytvorená', 'potvrdená', 'schválená'];
        
        foreach ($validStatuses as $status) {
            $internship = Internship::create([
                'student_id' => $this->student->id,
                'company_id' => $this->company->id,
                'status' => $status,
                'academy_year' => '2024/2025',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]);
            
            $this->assertEquals($status, $internship->status);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_can_have_contact_persons()
    {
        $contactPerson = ContactPerson::create([
            'name' => 'Jana',
            'surname' => 'Veselá',
            'email' => 'jana@example.com',
            'company_id' => $this->company->id,
        ]);

        $this->internship->contactPersons()->attach($contactPerson->id);

        $this->assertCount(1, $this->internship->contactPersons);
        $this->assertEquals($contactPerson->id, $this->internship->contactPersons->first()->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_academy_year_is_stored()
    {
        $internship = Internship::create([
            'student_id' => $this->student->id,
            'company_id' => $this->company->id,
            'status' => 'prebieha',
            'academy_year' => '2023/2024',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $this->assertEquals('2023/2024', $internship->academy_year);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function internship_dates_are_stored_correctly()
    {
        $startDate = now()->subMonth();
        $endDate = now()->addMonths(2);

        $internship = Internship::create([
            'student_id' => $this->student->id,
            'company_id' => $this->company->id,
            'status' => 'prebieha',
            'academy_year' => '2024/2025',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->assertTrue($internship->start_date->isSameDay($startDate));
        $this->assertTrue($internship->end_date->isSameDay($endDate));
    }
}
