<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Models\ContactPerson;
use App\Models\Internship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

class PdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $student;
    protected $company;
    protected $contactPerson;
    protected $internship;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin',
            'surname' => 'User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create student user and student
        $studentUser = User::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'email' => 'peter.hudec@ukf.sk',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'name' => 'Peter',
            'surname' => 'Hudec',
            'student_email' => 'peter.hudec@ukf.sk',
            'alternative_email' => 'hudec.peter@gmail.com',
            'phone_number' => '+421 900 123 456',
            'user_id' => $studentUser->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Hlavná',
            'house_number' => '15A',
        ]);

        // Create company user and company
        $companyUser = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@test.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        $this->company = Company::create([
            'name' => 'Test Company s.r.o.',
            'user_id' => $companyUser->id,
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '81101',
            'street' => 'Vazovova',
            'house_number' => '10',
        ]);

        // Create contact person
        $this->contactPerson = ContactPerson::create([
            'name' => 'Jana',
            'surname' => 'Veselá',
            'position' => 'HR Manažérka',
            'email' => 'jana.vesela@test.com',
            'phone_number' => '+421 911 123 456',
            'company_id' => $this->company->id,
        ]);

        // Create internship
        $this->internship = Internship::create([
            'student_id' => $this->student->id,
            'company_id' => $this->company->id,
            'garant_id' => null,
            'status' => 'prebieha',
            'academy_year' => '2024/2025',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
            'confirmed_date' => now()->subWeeks(2),
            'approved_date' => now()->subWeek(),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_generate_internship_pdf()
    {
        $this->markTestSkipped('PDF generation requires DomPDF package installation');
        
        Passport::actingAs($this->adminUser);

        $response = $this->getJson("/api/vykaz-generate/{$this->internship->id}");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        
        // Check that the filename contains student surname
        $this->assertStringContainsString('Dohoda_o_odbornej_praxi_Hudec.pdf', $response->headers->get('content-disposition'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_generate_pdf()
    {
        $this->markTestSkipped('PDF generation requires DomPDF package installation');
        
        $response = $this->getJson("/api/vykaz-generate/{$this->internship->id}");

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pdf_generation_includes_correct_student_data()
    {
        $this->markTestSkipped('PDF generation requires DomPDF package installation');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pdf_generation_includes_correct_company_data()
    {
        $this->markTestSkipped('PDF generation requires DomPDF package installation');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pdf_generation_includes_contact_person_data()
    {
        $this->markTestSkipped('PDF generation requires DomPDF package installation');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pdf_generation_handles_missing_contact_person()
    {
        $this->markTestSkipped('PDF generation requires DomPDF package installation');
    }
}
