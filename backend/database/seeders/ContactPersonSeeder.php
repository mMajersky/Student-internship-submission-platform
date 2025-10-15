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
        // Získame existujúcu spoločnosť, ku ktorej priradíme kontaktnú osobu
        $company = Company::where('name', 'Example Company s.r.o.')->first();

        if ($company) {
            DB::table('contact_persons')->insert([
                [
                    'name' => 'Jana',
                    'surname' => 'Veselá',
                    'position' => 'HR Manažérka',
                    'email' => 'jana.vesela@example.com',
                    'phone_number' => '+421 911 123 456',
                    'company_id' => $company->id,
                ],
                [
                    'name' => 'Milan',
                    'surname' => 'Novák',
                    'position' => 'CEO',
                    'email' => 'milan.novak@example.com',
                    'phone_number' => '+421 905 654 321',
                    'company_id' => $company->id,
                ]
            ]);
        }
    }
}