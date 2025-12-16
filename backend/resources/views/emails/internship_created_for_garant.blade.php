@extends('emails.base')

@section('content')
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>

    <!-- SLOVAK VERSION -->
    <h1>Nová prax v systéme</h1>
    <h3>Dobrý deň,</h3>

    <p>V systéme <strong>SISP</strong> bola evidovaná nová prax.</p>

    <h4>Informácie o praxe:</h4>
    <ul>
        <li><strong>ID praxe:</strong> {{ $internshipId ?? 'N/A' }}</li>
        <li><strong>Študent:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Firma:</strong> {{ $companyName ?? 'N/A' }}</li>
        <li><strong>Akademický rok:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Dátum začiatku:</strong> {{ $startDate ?? 'N/A' }}</li>
        <li><strong>Dátum konca:</strong> {{ $endDate ?? 'N/A' }}</li>
        <li><strong>Stav:</strong> {{ $status ?? 'N/A' }}</li>
    </ul>

    <p style="margin-top: 20px;">Prosíme, skontrolujte prax v systéme a prijímte potrebné kroky.</p>

    <p style="margin-top: 30px;">Ďakujeme za využitie platformy SISP.</p>

    <p style="margin-top: 30px;">S pozdravom,<br>Tím SISP</p>

    <!-- ENGLISH VERSION -->
    <hr style="border: none; border-top: 1px solid #ddd; margin: 40px 0;">

    <h1>New Internship in System</h1>
    <h3>Dear Sir/Madam,</h3>

    <p>A new internship has been registered in the <strong>SISP</strong> system.</p>

    <h4>Internship Information:</h4>
    <ul>
        <li><strong>Internship ID:</strong> {{ $internshipId ?? 'N/A' }}</li>
        <li><strong>Student:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Company:</strong> {{ $companyName ?? 'N/A' }}</li>
        <li><strong>Academic Year:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Start Date:</strong> {{ $startDate ?? 'N/A' }}</li>
        <li><strong>End Date:</strong> {{ $endDate ?? 'N/A' }}</li>
        @php
            $statusEn = match($status ?? '') {
                'created' => 'Created',
                'approved by garant' => 'Approved by garant',
                'rejected by garant' => 'Rejected by garant',
                'defended by student' => 'Defended by student',
                'not defended by student' => 'Not defended by student',
                'confirmed by company' => 'Confirmed by company',
                'not confirmed by company' => 'Not confirmed by company',
                default => $status ?? 'N/A'
            };
        @endphp
        <li><strong>Status:</strong> {{ $statusEn }}</li>
    </ul>

    <p style="margin-top: 20px;">Please review the internship in the system and take necessary actions.</p>

    <p style="margin-top: 30px;">Thank you for using the SISP platform.</p>

    <p style="margin-top: 30px;">Regards,<br>SISP Team</p>
@endsection

