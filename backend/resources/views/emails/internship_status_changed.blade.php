@extends('emails.base')

@section('content')
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>

    <!-- SLOVAK VERSION -->
    <h1>Zmena stavu praxe</h1>
    <h3>Dobrý deň,</h3>

    <p>Bol zmenený stav praxe v platforme <strong>SISP</strong>.</p>

    <h4>Informácie o praxe:</h4>
    <ul>
        <li><strong>ID praxe:</strong> {{ $internshipId ?? 'N/A' }}</li>
        <li><strong>Študent:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Firma:</strong> {{ $companyName ?? 'N/A' }}</li>
        <li><strong>Akademický rok:</strong> {{ $academyYear ?? 'N/A' }}</li>
    </ul>

    <h4>Zmena stavu:</h4>
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0;">
        <p style="margin: 0; font-size: 16px;">
            <span style="color: #666;">{{ $oldStatus ?? 'N/A' }}</span>
            <span style="margin: 0 10px; font-weight: bold;">→</span>
            <span style="color: #28a745; font-weight: bold;">{{ $newStatus ?? 'N/A' }}</span>
        </p>
    </div>

    <p style="margin-top: 20px;">Ďakujeme za využitie platformy SISP.</p>

    <p style="margin-top: 30px;">S pozdravom,<br>Tím SISP</p>

    <!-- ENGLISH VERSION -->
    <hr style="border: none; border-top: 1px solid #ddd; margin: 40px 0;">

    <h1>Internship Status Changed</h1>
    <h3>Dear Sir/Madam,</h3>

    <p>The status of an internship has been changed in the <strong>SISP</strong> platform.</p>

    <h4>Internship Information:</h4>
    <ul>
        <li><strong>Internship ID:</strong> {{ $internshipId ?? 'N/A' }}</li>
        <li><strong>Student:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Company:</strong> {{ $companyName ?? 'N/A' }}</li>
        <li><strong>Academic Year:</strong> {{ $academyYear ?? 'N/A' }}</li>
    </ul>

    <h4>Status Change:</h4>
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0;">
        <p style="margin: 0; font-size: 16px;">
            <span style="color: #666;">
                @php
                    $oldStatusEn = match($oldStatus ?? '') {
                        'created' => 'Created',
                        'approved by garant' => 'Approved by garant',
                        'rejected by garant' => 'Rejected by garant',
                        'defended by student' => 'Defended by student',
                        'not defended by student' => 'Not defended by student',
                        'confirmed by company' => 'Confirmed by company',
                        'not confirmed by company' => 'Not confirmed by company',
                        default => $oldStatus ?? 'N/A'
                    };
                @endphp
                {{ $oldStatusEn }}
            </span>
            <span style="margin: 0 10px; font-weight: bold;">→</span>
            <span style="color: #28a745; font-weight: bold;">
                @php
                    $newStatusEn = match($newStatus ?? '') {
                        'created' => 'Created',
                        'approved by garant' => 'Approved by garant',
                        'rejected by garant' => 'Rejected by garant',
                        'defended by student' => 'Defended by student',
                        'not defended by student' => 'Not defended by student',
                        'confirmed by company' => 'Confirmed by company',
                        'not confirmed by company' => 'Not confirmed by company',
                        default => $newStatus ?? 'N/A'
                    };
                @endphp
                {{ $newStatusEn }}
            </span>
        </p>
    </div>

    <p style="margin-top: 20px;">Thank you for using the SISP platform.</p>

    <p style="margin-top: 30px;">Regards,<br>SISP Team</p>
@endsection

