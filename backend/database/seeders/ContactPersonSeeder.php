<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class ContactPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the company to which contact persons will be assigned
        $company = Company::where('name', 'Example Company s.r.o.')->first();

        if ($company) {
            // Define contact persons data
            $contactPersons = [
                [
                    'email' => 'jana.vesela@example.com',
                    'name' => 'Jana',
                    'surname' => 'Veselá',
                    'position' => 'HR Manažérka',
                    'phone_number' => '+421 911 123 456',
                    'company_id' => $company->id,
                ],
                [
                    'email' => 'milan.novak@example.com',
                    'name' => 'Milan',
                    'surname' => 'Novák',
                    'position' => 'CEO',
                    'phone_number' => '+421 905 654 321',
                    'company_id' => $company->id,
                ]
            ];

            // Create contact persons if they do not exist
            foreach ($contactPersons as $personData) {
                \App\Models\ContactPerson::firstOrCreate(
                    ['email' => $personData['email']],
                    $personData
                );
            }
        }
    }
}