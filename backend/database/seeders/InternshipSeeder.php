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
        // Získame existujúceho študenta a spoločnosť, aby sme mohli vytvoriť platný vzťah
        $student = Student::first();
        $company = Company::first();
        // Garant je voliteľný, môžeme ho nastaviť na null alebo získať existujúceho
        $garant = Garant::first();

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