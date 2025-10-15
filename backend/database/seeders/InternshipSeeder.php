<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Company;
use App\Models\Garant;
use Carbon\Carbon;

class InternshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Získame existujúceho študenta a spoločnosť, alebo vytvoríme nové záznamy
        $student = Student::first();
        $company = Company::first();
        $garant = Garant::first();

        // If no records exist, create them
        if (!$student) {
            // Create a test student if none exists
            $address = \App\Models\Address::create([
                'state' => 'Slovensko',
                'region' => 'Nitriansky kraj',
                'city' => 'Nitra',
                'postal_code' => '94901',
                'street' => 'Trieda A. Hlinku',
                'house_number' => '1',
            ]);

            $studentUser = \App\Models\User::create([
                'name' => 'Test Student',
                'email' => 'student@test.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => \App\Models\Role::where('name', 'STUDENT')->first()->id,
            ]);

            $student = Student::create([
                'name' => 'Peter',
                'surname' => 'Hudec',
                'student_email' => 'peter.hudec@ukf.sk',
                'alternative_email' => 'hudec.peter@gmail.com',
                'address_id' => $address->id,
                'phone_number' => '+421 900 123 456',
                'user_id' => $studentUser->id,
            ]);
        }

        if (!$company) {
            // Create a test company if none exists
            $address = \App\Models\Address::create([
                'state' => 'Slovensko',
                'region' => 'Bratislavský kraj',
                'city' => 'Bratislava',
                'postal_code' => '81101',
                'street' => 'Vazovova',
                'house_number' => '10',
            ]);

            $companyUser = \App\Models\User::firstOrCreate(
                ['email' => 'company-internship@test.com'],
                [
                    'name' => 'Test Company Internship',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role_id' => \App\Models\Role::where('name', 'COMPANY')->first()->id,
                ]
            );

            $company = Company::create([
                'name' => 'Test Company s.r.o.',
                'statutary' => 'Ing. Jozef Mrkvička',
                'address_id' => $address->id,
                'user_id' => $companyUser->id,
            ]);
        }

        DB::table('internships')->insert([
            [
                'student_id' => $student->id,
                'company_id' => $company->id,
                'garant_id' => $garant ? $garant->id : null,
                'status' => 'prebieha', // napr. 'prebieha', 'ukončená', 'čaká na schválenie'
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonths(2),
                'confirmed_date' => Carbon::now()->subWeeks(2),
                'approved_date' => Carbon::now()->subWeek(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'student_id' => $student->id,
                'company_id' => $company->id,
                'garant_id' => null,
                'status' => 'ukončená',
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->subMonths(3),
                'confirmed_date' => Carbon::now()->subMonths(5),
                'approved_date' => Carbon::now()->subMonths(5),
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(3),
            ]
        ]);
    }
}
