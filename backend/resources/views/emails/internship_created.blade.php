@extends('emails.base')

@section('content')
    <h3>Dobrý deň {{ $companyName ?? 'zástupca spoločnosti' }},</h3>

    <p>Nová žiadosť o stáž bola odoslaná prostredníctvom platformy <strong>SISP</strong>.</p>

    <h4>Informácie o študentovi:</h4>
    <ul>
        <li><strong>Meno:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Email:</strong> {{ $studentEmail ?? 'N/A' }}</li>
        <li><strong>Telefón:</strong> {{ $studentPhone ?? 'N/A' }}</li>
    </ul>

    <h4>Podrobnosti o stáži:</h4>
    <ul>
        <li><strong>Akademický rok:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Dátum začiatku:</strong> {{ $startDate ?? 'N/A' }}</li>
        <li><strong>Dátum konca:</strong> {{ $endDate ?? 'N/A' }}</li>
        <li><strong>Stav:</strong> {{ $status ?? 'N/A' }}</li>
    </ul>

    <p>Prosím, skontrolujte žiadosť a prijímte rozhodnutie:</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $confirmUrl }}"
           style="display: inline-block; margin: 0 10px; padding: 12px 24px; background: #28a745; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">
            ✅ Potvrdiť stáž
        </a>
        <a href="{{ $rejectUrl }}"
           style="display: inline-block; margin: 0 10px; padding: 12px 24px; background: #dc3545; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">
            ❌ Zamietnuť stáž
        </a>
    </div>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0; font-size: 12px; color: #666;">
        <p><strong>Bezpečnostné upozornenie:</strong> Tieto odkazy sú zabezpečené a vyprší ich platnosť o 30 dní kvôli vašej ochrane.</p>
    </div>

    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin: 20px 0; font-size: 14px;">
        <p style="margin: 0;"><strong>Technické problémy alebo expirovaný odkaz:</strong><br>
        V prípade technických problémov alebo expirácie odkazov, kontaktujte svojho garanta: <strong>{{ $internship->garant->user->email ?? 'garant@school.sk' }}</strong></p>
    </div>

    <p style="margin-top: 30px;">S pozdravom,<br>Tím SISP</p>
@endsection
