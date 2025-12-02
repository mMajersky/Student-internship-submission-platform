@extends('emails.base')

@section('content')
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>

    <!-- SLOVAK VERSION -->
    <h1>Nový dokument na validáciu</h1>
    <h3>Dobrý deň,</h3>

    <p>Študent nahral nový dokument v platforme <strong>SISP</strong>, ktorý čaká na vašu validáciu.</p>

    <h4>Informácie:</h4>
    <ul>
        <li><strong>Študent:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Firma:</strong> {{ $companyName ?? 'N/A' }}</li>
        <li><strong>Akademický rok:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Typ dokumentu:</strong> {{ $documentType ?? 'N/A' }}</li>
    </ul>

    <div style="background-color: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #ffc107;">
        <p style="margin: 0; color: #856404;">
            <strong>Akcia potrebná:</strong> Prosíme, prihláste sa do systému SISP a skontrolujte nahraný dokument. Následne ho môžete schváliť alebo zamietnuť s uvedením dôvodu.
        </p>
    </div>

    <p style="margin-top: 20px;">Ďakujeme za využitie platformy SISP.</p>

    <p style="margin-top: 30px;">S pozdravom,<br>Tím SISP</p>

    <!-- ENGLISH VERSION -->
    <hr style="border: none; border-top: 1px solid #ddd; margin: 40px 0;">

    <h1>New Document for Validation</h1>
    <h3>Dear Sir/Madam,</h3>

    <p>A student has uploaded a new document in the <strong>SISP</strong> platform that awaits your validation.</p>

    <h4>Information:</h4>
    <ul>
        <li><strong>Student:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Company:</strong> {{ $companyName ?? 'N/A' }}</li>
        <li><strong>Academic Year:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Document Type:</strong> 
            @php
                $documentTypeEn = match($documentType ?? '') {
                    'Podpísaná dohoda' => 'Signed Agreement',
                    'Sken výkazu praxe' => 'Internship Report Scan',
                    default => $documentType ?? 'N/A'
                };
            @endphp
            {{ $documentTypeEn }}
        </li>
    </ul>

    <div style="background-color: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0; border-left: 4px solid #ffc107;">
        <p style="margin: 0; color: #856404;">
            <strong>Action Required:</strong> Please log in to the SISP system and review the uploaded document. You can then approve or reject it with a reason.
        </p>
    </div>

    <p style="margin-top: 20px;">Thank you for using the SISP platform.</p>

    <p style="margin-top: 30px;">Regards,<br>SISP Team</p>
@endsection

