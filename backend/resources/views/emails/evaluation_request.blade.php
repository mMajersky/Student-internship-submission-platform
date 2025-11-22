@extends('emails.base')

@section('content')
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>

    <!-- SLOVAK VERSION -->
    <h1>Výkaz o vykonanej odbornej praxi</h1>
    <h3>Dobrý deň {{ $companyName ?? 'zástupca spoločnosti' }},</h3>

    <p>Žiadame vás o vyplnenie výkazu o vykonanej odbornej praxi študenta <strong>{{ $studentName ?? 'N/A' }}</strong>, ktorá sa uskutočnila vo vašej spoločnosti.</p>

    <h4>Informácie o stáži:</h4>
    <ul>
        <li><strong>Študent:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Akademický rok:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Dátum začiatku:</strong> {{ $startDate ?? 'N/A' }}</li>
        <li><strong>Dátum konca:</strong> {{ $endDate ?? 'N/A' }}</li>
        <li><strong>Stav:</strong> {{ $status ?? 'N/A' }}</li>
    </ul>

    <p>Prosím, vyplňte výkaz praxe kliknutím na tlačidlo nižšie. Môžete ho vyplniť elektronicky alebo nahrať sken výkazu v papierovej forme:</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $evaluationUrl }}"
           style="display: inline-block; padding: 12px 24px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">
            Vyplniť výkaz praxe
        </a>
    </div>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#f8f9fa" style="border-radius: 4px; margin: 20px 0;">
        <tr>
            <td style="font-size: 12px; color: #666;">
                <strong>Bezpečnostné upozornenie:</strong> Tento odkaz je zabezpečený a vyprší jeho platnosť o 30 dní.
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#fff3cd" style="border: 1px solid #ffeaa7; border-radius: 4px; margin: 20px 0; font-size: 14px; color: #856404;">
        <tr>
            <td style="border: 1px solid #ffeaa7;">
                <strong>Technické problémy alebo expirovaný odkaz:</strong><br>
                V prípade technických problémov alebo expirácie odkazov, kontaktujte garanta: <strong>{{ $garantEmail ?? 'garant@school.sk' }}</strong>
            </td>
        </tr>
    </table>

    <p style="margin-top: 30px;">Ďakujeme za váš čas a hodnotenie.<br>S pozdravom,<br>Tím SISP</p>

    <!-- ENGLISH VERSION -->
    <h1>Internship Report</h1>
    <h3>Dear {{ $companyName ?? 'company representative' }},</h3>

    <p>We are requesting you to fill out the internship report for student <strong>{{ $studentName ?? 'N/A' }}</strong>, which took place at your company.</p>

    <h4>Internship Information:</h4>
    <ul>
        <li><strong>Student:</strong> {{ $studentName ?? 'N/A' }}</li>
        <li><strong>Academic Year:</strong> {{ $academyYear ?? 'N/A' }}</li>
        <li><strong>Start Date:</strong> {{ $startDate ?? 'N/A' }}</li>
        <li><strong>End Date:</strong> {{ $endDate ?? 'N/A' }}</li>
        <li><strong>Status:</strong> @if($status == 'vytvorená') Created @elseif($status == 'potvrdená') Confirmed @elseif($status == 'schválená') Approved @elseif($status == 'zamietnutá') Rejected @elseif($status == 'obhájená') Defended @elseif($status == 'neobhájená') Not defended @else {{ $status ?? 'N/A' }} @endif</li>
    </ul>

    <p>Please fill out the internship report by clicking the button below. You can fill it out electronically or upload a scan of the paper form:</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $evaluationUrl }}"
           style="display: inline-block; padding: 12px 24px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">
            Fill Internship Report
        </a>
    </div>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#f8f9fa" style="border-radius: 4px; margin: 20px 0;">
        <tr>
            <td style="font-size: 12px; color: #666;">
                <strong>Security Notice:</strong> This link is secure and will expire in 30 days.
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#fff3cd" style="border: 1px solid #ffeaa7; border-radius: 4px; margin: 20px 0; font-size: 14px; color: #856404;">
        <tr>
            <td style="border: 1px solid #ffeaa7;">
                <strong>Technical issues or expired link:</strong><br>
                For technical issues or expired links, contact the supervisor: <strong>{{ $garantEmail ?? 'supervisor@school.sk' }}</strong>
            </td>
        </tr>
    </table>

    <p style="margin-top: 30px;">Thank you for your time and evaluation.<br>Regards,<br>SISP Team</p>
@endsection

