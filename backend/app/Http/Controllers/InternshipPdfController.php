<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InternshipPdfController extends Controller
{
    public function generate(Internship $internship)
    {
        // 1. EFEKTÍVNE NAČÍTANIE POTREBNÝCH DÁT
        // Načítame stáž s jej študentom, firmou a kontaktnými osobami
        // NEPOTREBUJEME '.address', lebo adresa je súčasťou študenta a firmy
        $internship->load('student', 'company.contactPersons');

        // 2. PRÍPRAVA DÁT PRE ŠABLÓNU
        $student = $internship->student;
        $company = $internship->company;
        
        // Bezpečnostná kontrola, ak by náhodou chýbali dáta
        if (!$student || !$company) {
            abort(500, 'K stáži chýba priradený študent alebo firma.');
        }

        // Z firmy vyberieme prvú kontaktnú osobu ako tútora
        $tutor = $company->contactPersons->first();

        $data = [
            // --- Dáta o študentovi ---
            'student_name' => $student->name . ' ' . $student->surname,
            // Adresu formátujeme priamo z objektu $student
            'student_address' => $this->formatAddress($student),
            'student_contact' => $student->student_email,
            'study_program' => 'Aplikovaná informatika',

            // --- Dáta o firme ---
            'company_name' => $company->name,
            // Adresu formátujeme priamo z objektu $company
            'company_address' => $this->formatAddress($company),
            'company_contact' => $tutor ? $tutor->name . ' ' . $tutor->surname : '......................',

            // --- Dáta o tútorovi z contact_persons ---
            'tutor_name' => $tutor ? $tutor->name . ' ' . $tutor->surname : '.....................................',

            // --- DÁTUMY STÁŽE Z DATABÁZY ---
            'start_date' => Carbon::parse($internship->start_date)->format('d.m.Y'),
            'end_date' => Carbon::parse($internship->end_date)->format('d.m.Y'),
            
            // --- Ostatné dáta ---
            'generation_date' => now()->format('d.m.Y'),
        ];

        // 3. VYGENEROVANIE PDF
        $pdf = Pdf::loadView('pdf.internship_agreement', $data);
        $pdf->setPaper('A4', 'portrait');
        if (method_exists($pdf, 'setOptions')) {
            $pdf->setOptions(['isHtml5ParserEnabled' => true]);
        }

        return $pdf->download('Dohoda_o_odbornej_praxi_' . $student->surname . '.pdf');
    }

    /**
     * Pomocná metóda na sformátovanie adresy do jedného reťazca.
     * Prijíma celý objekt (Student alebo Company).
     */
    private function formatAddress($entity)
    {
        if (!$entity) {
            return '..............................................................';
        }
        // Používame priame vlastnosti objektu
        return sprintf(
            '%s %s, %s %s, %s',
            $entity->street ?? '',
            $entity->house_number ?? '',
            $entity->postal_code ?? '',
            $entity->city ?? '',
            $entity->state ?? ''
        );
    }
}
