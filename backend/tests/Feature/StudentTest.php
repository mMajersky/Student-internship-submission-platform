<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentUser = User::create([
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
            'alternative_email' => 'hudec.peter@gmail.com',
            'phone_number' => '+421 900 123 456',
            'user_id' => $this->studentUser->id,
            'study_level' => 'Bc.',
            'state' => 'Slovensko',
            'region' => 'Nitriansky kraj',
            'city' => 'Nitra',
            'postal_code' => '94901',
            'street' => 'Hlavná',
            'house_number' => '15A',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_has_all_required_fields()
    {
        $this->assertEquals('Peter', $this->student->name);
        $this->assertEquals('Hudec', $this->student->surname);
        $this->assertEquals('peter.hudec@ukf.sk', $this->student->student_email);
        $this->assertEquals('hudec.peter@gmail.com', $this->student->alternative_email);
        $this->assertEquals('+421 900 123 456', $this->student->phone_number);
        $this->assertEquals('Bc.', $this->student->study_level);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_has_full_name()
    {
        $this->assertEquals('Peter Hudec', $this->student->full_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_belongs_to_user()
    {
        $this->assertEquals($this->studentUser->id, $this->student->user->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_address_fields_are_stored()
    {
        $this->assertEquals('Slovensko', $this->student->state);
        $this->assertEquals('Nitriansky kraj', $this->student->region);
        $this->assertEquals('Nitra', $this->student->city);
        $this->assertEquals('94901', $this->student->postal_code);
        $this->assertEquals('Hlavná', $this->student->street);
        $this->assertEquals('15A', $this->student->house_number);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_emails_must_be_valid()
    {
        $this->assertStringContainsString('@', $this->student->student_email);
        $this->assertStringContainsString('@', $this->student->alternative_email);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_user_has_student_role()
    {
        $this->assertTrue($this->studentUser->isStudent());
        $this->assertFalse($this->studentUser->isCompany());
        $this->assertFalse($this->studentUser->isAdmin());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_can_have_internships()
    {
        $this->assertIsIterable($this->student->internships);
    }
}
