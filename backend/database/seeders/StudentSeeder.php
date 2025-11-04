<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User; // <-- DÔLEŽITÉ: Importujeme model User

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DYNAMICKY NÁJDEME POUŽÍVATEĽA S ROLOU 'student'
        // Predpokladáme, že v UserSeederi vytvárate používateľa s emailom 'student@example.com'
        $studentUser = User::where('email', 'student@example.com')->first();

        // 2. Bezpečnostná kontrola, ak by používateľ neexistoval
        if (!$studentUser) {
            // Vypíšeme chybu do konzoly a seeder sa nespustí
            $this->command->error('Student user with email student@example.com not found. Please run UserSeeder first.');
            return;
        }

        // 3. VYTVORÍME ŠTUDENTA A PREPOJÍME HO SO SPRÁVNYM, DYNAMICKY NÁJDENÝM POUŽÍVATEĽOM
        // firstOrCreate() zabráni vytváraniu duplicitných študentov, ak seeder spustíte viackrát
        Student::firstOrCreate(
            ['student_email' => 'peter.hudec@ukf.sk'], // Podmienka, podľa ktorej hľadáme
            [
                'name' => 'Peter',
                'surname' => 'Hudec',
                'alternative_email' => 'hudec.peter@gmail.com',
                'phone_number' => '+421 900 123 456',
                'user_id' => $studentUser->id, // <-- TU JE KĽÚČOVÁ ZMENA!
                'study_level' => 'Bc.',
                'study_field' => 'Informatika',
                'state' => 'Slovensko',
                'region' => 'Nitriansky kraj',
                'city' => 'Nitra',
                'postal_code' => '949 01',
                'street' => 'Hlavná',
                'house_number' => '15A',
            ]
        );
    }
}