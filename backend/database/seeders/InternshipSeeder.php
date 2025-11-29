<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Internship;
use App\Models\Student;
use App\Models\Company;
use App\Models\Garant;
use App\Models\ContactPerson;
use Carbon\Carbon;

class InternshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- PRÍPAD 1: Prax čerstvo vytvorená študentom (bez garanta) ---

        // Získame prvého študenta a firmu. UISTITE SA, ŽE EXISTUJÚ!
        $student1 = Student::find(1); // Alebo Student::first()
        $company1 = Company::find(1); // Alebo Company::first()

        if (!$student1 || !$company1) {
            $this->command->error('Pre vytvorenie prvej praxe musí existovať aspoň jeden študent a firma!');
            return;
        }

        $contactPerson1 = ContactPerson::where('company_id', $company1->id)->first();
        if (!$contactPerson1) {
            $this->command->error("Pre firmu s ID {$company1->id} neexistuje kontaktná osoba!");
            return;
        }

        $internship1 = Internship::create([
            'student_id' => $student1->id,
            'company_id' => $company1->id,
            'garant_id' => null, // Garant ešte nie je priradený
            'status' => 'vytvorena', // Stav zodpovedá tomu, že prax je len vytvorená
            'academy_year' => '2025/2026',
            'start_date' => Carbon::now()->addMonths(2),
            'end_date' => Carbon::now()->addMonths(5),
            'confirmed_date' => Carbon::now(), // Dátum potvrdenia študentom
            'approved_date' => null, // Dátum schválenia je NULL, lebo nebola schválená
        ]);

        // Priradíme kontaktnú osobu
        $internship1->contactPersons()->attach($contactPerson1->id);
        $this->command->info("Vytvorená prax (bez garanta) pre študenta {$student1->name} bola úspešne založená.");


        // --- PRÍPAD 2: Prax schválená garantom ---

        // Podmienky: Musí existovať druhý študent, druhá firma a garant (v tejto DB bude len jeden študent a jedna firma, takže druhá prax sa nevytvorí)
        // Získame ďalšieho študenta, firmu a garanta. UISTITE SA, ŽE EXISTUJÚ!
        $student2 = Student::find(2);
        $company2 = Company::find(2);
        $garant = Garant::first();

        if ($student2 && $company2 && $garant) {
            $contactPerson2 = ContactPerson::where('company_id', $company2->id)->first();
            if ($contactPerson2) {
                $internship2 = Internship::create([
                    'student_id' => $student2->id,
                    'company_id' => $company2->id,
                    'garant_id' => $garant->id, // Garant je priradený
                    'status' => 'schvalena', // Stav je "schvalena"
                    'academy_year' => '2025/2026',
                    'start_date' => Carbon::now()->addMonth(),
                    'end_date' => Carbon::now()->addMonths(4),
                    'confirmed_date' => Carbon::now()->subDays(5), // Potvrdené pred pár dňami
                    'approved_date' => Carbon::now(), // Schválené dnes
                ]);

                // Priradíme kontaktnú osobu
                $internship2->contactPersons()->attach($contactPerson2->id);
                $this->command->info("Schválená prax (s garantom) pre študenta {$student2->name} bola úspešne založená.");
            } else {
                $this->command->warn("Pre firmu s ID {$company2->id} neexistuje kontaktná osoba! Druhá prax nebola vytvorená.");
            }
        } else {
            $this->command->info('Druhá prax sa nevytvorila (chýbajú entity druhý študent, druhá firma alebo garant).');
        }
    }
}
