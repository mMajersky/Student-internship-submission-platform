<?php

namespace App\Http\Controllers;

// Potrebujeme naimportovať model Internship
use App\Models\Internship;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InternshipPdfController extends Controller
{
    /**
     * Generuje PDF dohodu pre konkrétnu stáž.
     *
     * @param  \App\Models\Internship  $internship Automaticky načítaný model z URL
     * @return \Illuminate\Http\Response
     */
    public function generate(Internship $internship)
    {
        // 1. EFEKTÍVNE NAČÍTANIE VŠETKÝCH POTREBNÝCH DÁT
        // Načítame stáž a k nej pripojené modely: študenta s jeho adresou,
        // firmu s jej adresou a zároveň aj kontaktné osoby firmy.
        $internship->load('student.address', 'company.address', 'company.contactPersons');

        // 2. PRÍPRAVA DÁT PRE ŠABLÓNU (všetko je teraz dynamické)
        
        // Získame študenta a firmu priamo z modelu stáže
        $student = $internship->student;
        $company = $internship->company;
        
        // Z firmy vyberieme prvú kontaktnú osobu ako tútora
        $tutor = $company->contactPersons->first(); // <-- tu padá, lebo $company je null


        $data = [
            // --- Dáta o študentovi ---
            'student_name' => $student->name . ' ' . $student->surname,
            'student_address' => $this->formatAddress($student->address),
            'student_contact' => $student->student_email,
            'study_program' => 'Aplikovaná informatika', // Toto môžete neskôr pridať k modelu študenta

            // --- Dáta o firme ---
            'company_name' => $company->name,
            'company_address' => $this->formatAddress($company->address),
            'company_contact' => $company->statutary,
            'company_city' => $company->address->city ?? '',

            // --- Dáta o tútorovi z contact_persons ---
            'tutor_name' => $tutor ? $tutor->name . ' ' . $tutor->surname : '.....................................',

            // --- DÁTUMY STÁŽE Z DATABÁZY ---
            'start_date' => Carbon::parse($internship->start_date)->format('d.m.Y'),
            'end_date' => Carbon::parse($internship->end_date)->format('d.m.Y'),
            
            // --- Ostatné dáta ---
            'generation_date' => now()->format('d.m.Y'),
            'sign_date' => '....................'
        ];

        // 3. VYGENEROVANIE PDF
        // Uistite sa, že cesta 'pdf.internship_agreement' sedí (resources/views/pdf/internship_agreement.blade.php)
        $pdf = Pdf::loadView('pdf.internship_agreement', $data)
            ->setPaper('A4', 'portrait');

        // Umožní Unicode znaky (dôležité pre slovenčinu)
        $pdf->setOption('isHtml5ParserEnabled', true);

        return $pdf->download('Dohoda_o_odbornej_praxi_' . $student->surname . '.pdf');
    }

    /**
     * Pomocná metóda na sformátovanie adresy do jedného reťazca.
     */
    private function formatAddress($address)
    {
        if (!$address) {
            return '..............................................................';
        }
        return sprintf(
            '%s %s, %s %s, %s',
            $address->street ?? '',
            $address->house_number ?? '',
            $address->postal_code ?? '',
            $address->city ?? '',
            $address->state ?? ''
        );
    }
}