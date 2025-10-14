<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Student;
use App\Models\Company;

class InternshipPdfController extends Controller
{
    public function generate($studentId)
    {
        // Načítať študenta aj s adresou
        $student = Student::with('address')->findOrFail($studentId);

        // Prednastavené ID spoločnosti (nastav podľa potreby)
        $companyId = 1; // ← sem daj ID spoločnosti z DB

        $company = Company::with('address')->findOrFail($companyId);

        $data = [
            'student_name' => $student->name . ' ' . $student->surname,
            'student_address' => sprintf(
                '%s %s, %s %s, %s',
                $student->address->street ?? '',
                $student->address->house_number ?? '',
                $student->address->postal_code ?? '',
                $student->address->city ?? '',
                $student->address->state ?? ''
            ),
            'study_program' => 'Aplikovaná informatika',
            'student_contact' => $student->student_email . ', ' . $student->phone_number,
            'company_name' => $company->name,
            'company_address' => sprintf(
                '%s %s, %s %s, %s',
                $company->address->street ?? '',
                $company->address->house_number ?? '',
                $company->address->postal_code ?? '',
                $company->address->city ?? '',
                $company->address->state ?? ''
            ),
            'company_contact' => $company->statutary,
            'position' => 'IT Intern',
            'start_date' => '2025-02-01',
            'end_date' => '2025-05-31',
            'generation_date' => now()->format('d.m.Y'),
        ];

        $pdf = Pdf::loadView('pdf.internship_agreement', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download('Dohoda_o_odbornej_praxi_' . $student->surname . '.pdf');
    }
}
