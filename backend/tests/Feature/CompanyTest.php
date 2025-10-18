<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\ContactPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $companyUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyUser = User::create([
            'name' => 'Company',
            'surname' => 'User',
            'email' => 'company@example.com',
            'password' => Hash::make('password'),
            'role' => 'company',
        ]);

        $this->company = Company::create([
            'name' => 'Test Company s.r.o.',
            'user_id' => $this->companyUser->id,
            'state' => 'Slovensko',
            'region' => 'Bratislavský kraj',
            'city' => 'Bratislava',
            'postal_code' => '81101',
            'street' => 'Vazovova',
            'house_number' => '10',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_has_all_required_fields()
    {
        $this->assertEquals('Test Company s.r.o.', $this->company->name);
        $this->assertEquals('Slovensko', $this->company->state);
        $this->assertEquals('Bratislavský kraj', $this->company->region);
        $this->assertEquals('Bratislava', $this->company->city);
        $this->assertEquals('81101', $this->company->postal_code);
        $this->assertEquals('Vazovova', $this->company->street);
        $this->assertEquals('10', $this->company->house_number);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_belongs_to_user()
    {
        $this->assertEquals($this->companyUser->id, $this->company->user->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_can_have_multiple_contact_persons()
    {
        $contact1 = ContactPerson::create([
            'name' => 'Jana',
            'surname' => 'Veselá',
            'email' => 'jana@example.com',
            'company_id' => $this->company->id,
        ]);

        $contact2 = ContactPerson::create([
            'name' => 'Milan',
            'surname' => 'Novák',
            'email' => 'milan@example.com',
            'company_id' => $this->company->id,
        ]);

        $this->assertCount(2, $this->company->contactPersons);
        $this->assertTrue($this->company->contactPersons->contains($contact1));
        $this->assertTrue($this->company->contactPersons->contains($contact2));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_address_fields_are_optional()
    {
        $company = Company::create([
            'name' => 'Simple Company',
            'user_id' => $this->companyUser->id,
        ]);

        $this->assertEquals('Simple Company', $company->name);
        $this->assertNull($company->state);
        $this->assertNull($company->city);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_user_has_company_role()
    {
        $this->assertTrue($this->companyUser->isCompany());
        $this->assertFalse($this->companyUser->isStudent());
        $this->assertFalse($this->companyUser->isAdmin());
    }
}
