<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Student;

class InternshipPdfController extends Controller
{
    public function generate($id)
    {
        $student = Student::with('address')->findOrFail($id);

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
            'company_name' => 'Sample Company Ltd.',
            'company_address' => 'Business Park 5, Bratislava',
            'company_contact' => 'Ing. Peter Novák, +421 900 123 456',
            'position' => 'IT Intern',
            'start_date' => '2025-02-01',
            'end_date' => '2025-05-31',
            'generation_date' => now()->format('d.m.Y'),
        ];

        $pdf = Pdf::loadView('pdf.internship_agreement', $data)->setPaper('A4', 'portrait');

        return $pdf->download('Dohoda_o_odbornej_praxi_' . $student->surname . '.pdf');
    }
    public function generateEmpty()
{
    $data = [
        'student_name' => '',
        'student_address' => '',
        'study_program' => '',
        'student_contact' => '',
        'company_name' => '',
        'company_address' => '',
        'company_contact' => '',
        'position' => '',
        'start_date' => '',
        'end_date' => '',
        'generation_date' => now()->format('d.m.Y'),
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.internship_agreement', $data)
        ->setPaper('A4', 'portrait');

    return $pdf->download('Dohoda_o_odbornej_praxi_test.pdf');
}

}
