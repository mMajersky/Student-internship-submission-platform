<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Garant;
use App\Models\Company;
use App\Models\ContactPerson;
use App\Models\User;

class DemoEntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --- 1. VYTVORENIE GARANTA ---
        $garantUser = User::where('role', 'garant')->first();

        if ($garantUser) {
            // Vytvorím záznam v tabuľke 'garants' a prepojím ho s používateľom
            Garant::firstOrCreate(
                ['user_id' => $garantUser->id], // Podmienka, aby sa nevytváral duplicitne
                [
                    'name' => $garantUser->name,
                    'surname' => $garantUser->surname,
                    'faculty' => 'Fakulta prírodných vied a informatiky',
                ]
            );
            $this->command->info("Garant '{$garantUser->name} {$garantUser->surname}' bol úspešne vytvorený/nájdený.");
        } else {
            $this->command->error('Nebol nájdený žiadny používateľ s rolou garanta.');
        }


        // --- 2. VYTVORENIE FIRMY ---
        $companyUser = User::where('role', 'company')->first();

        if ($companyUser) {
            // Vytvorím záznam pre firmu a prepojím ho s používateľom
            $company = Company::firstOrCreate(
                ['user_id' => $companyUser->id], // Podmienka
                [
                    'name' => 'Innovatech Solutions s.r.o.',
                    'state' => 'Slovensko',
                    'region' => 'Bratislavský kraj',
                    'city' => 'Bratislava',
                    'postal_code' => '821 09',
                    'street' => 'Prievozská',
                    'house_number' => '4D',
                ]
            );
            $this->command->info("Firma '{$company->name}' bola úspešne vytvorená/nájdená.");

            // --- 3. VYTVORENIE KONTAKTNEJ OSOBY PRE TÚTO FIRMU ---
            ContactPerson::firstOrCreate(
                ['email' => 'kontakt@innovatech.sk'], // Unikátny email ako podmienka
                [
                    'name' => 'Martina',
                    'surname' => 'Projektová',
                    'position' => 'HR Manažér',
                    'phone_number' => '+421 955 123 789',
                    'company_id' => $company->id, // Priradím ID práve vytvorenej firmy
                ]
            );
            $this->command->info("Kontaktná osoba 'Martina Projektová' pre firmu {$company->name} bola vytvorená/nájdená.");

        } else {
            $this->command->error('Nebol nájdený žiadny používateľ s rolou company.');
        }
    }
}