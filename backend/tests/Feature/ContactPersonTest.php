<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\ContactPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ContactPersonTest extends TestCase
{
    use RefreshDatabase;

    protected $contactPerson;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->contactPerson = ContactPerson::create([
            'name' => 'Jana',
            'surname' => 'Veselá',
            'email' => 'jana@example.com',
            'phone_number' => '+421 911 123 456',
            'position' => 'HR Manažérka',
            'company_id' => $this->company->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_person_has_all_fields()
    {
        $this->assertEquals('Jana', $this->contactPerson->name);
        $this->assertEquals('Veselá', $this->contactPerson->surname);
        $this->assertEquals('jana@example.com', $this->contactPerson->email);
        $this->assertEquals('+421 911 123 456', $this->contactPerson->phone_number);
        $this->assertEquals('HR Manažérka', $this->contactPerson->position);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_person_has_full_name()
    {
        $this->assertEquals('Jana Veselá', $this->contactPerson->full_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_person_belongs_to_company()
    {
        $this->assertEquals($this->company->id, $this->contactPerson->company->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_person_email_is_valid()
    {
        $this->assertStringContainsString('@', $this->contactPerson->email);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_person_can_have_internships()
    {
        $this->assertIsIterable($this->contactPerson->internships);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_person_optional_fields()
    {
        $contact = ContactPerson::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john@example.com',
            'company_id' => $this->company->id,
        ]);

        $this->assertNull($contact->phone_number);
        $this->assertNull($contact->position);
    }
}
