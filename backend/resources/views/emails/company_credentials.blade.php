@extends('emails.base')

@section('content')
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>

    <!-- SLOVAK VERSION -->
    <h1>Váš firemný účet bol schválený</h1>
    <h3>Dobrý deň {{ $contactPersonName }},</h3>

    <p>Skvelé správy! Vaša registrácia spoločnosti <strong>{{ $companyName }}</strong> bola schválená našou administráciou.</p>

    <p>Teraz môžete pristupovať k systému riadenia stáží pomocou nasledujúcich prihlasovacích údajov:</p>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#e3f2fd" style="border-left: 4px solid #3498db; border-radius: 4px; margin: 20px 0;">
        <tr>
            <td>
                <p style="margin: 5px 0;"><strong>Používateľské meno (Email):</strong> {{ $username }}</p>
                <p style="margin: 5px 0;"><strong>Heslo:</strong> <code style="background-color: #f4f4f4; padding: 2px 6px; font-size: 14px;">{{ $password }}</code></p>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#fff3cd" style="border: 1px solid #ffeaa7; border-radius: 4px; margin: 20px 0; font-size: 14px; color: #856404;">
        <tr>
            <td>
                <strong>Dôležité:</strong> Prosím, zmeňte si heslo po prvom prihlásení z bezpečnostných dôvodov.
            </td>
        </tr>
    </table>

    <p>Teraz sa môžete prihlásiť do systému a začať spravovať príležitosti na stáže pre študentov.</p>

    <p style="margin-top: 30px;">S pozdravom,<br>Tím SISP</p>

    <hr style="border: none; border-top: 1px solid #eaeaea; margin: 40px 0;">

    <!-- ENGLISH VERSION -->
    <h1>Your Company Account Has Been Approved</h1>
    <h3>Dear {{ $contactPersonName }},</h3>

    <p>Great news! Your company registration for <strong>{{ $companyName }}</strong> has been approved by our administration.</p>

    <p>You can now access the internship management system using the following credentials:</p>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#e3f2fd" style="border-left: 4px solid #3498db; border-radius: 4px; margin: 20px 0;">
        <tr>
            <td>
                <p style="margin: 5px 0;"><strong>Username (Email):</strong> {{ $username }}</p>
                <p style="margin: 5px 0;"><strong>Password:</strong> <code style="background-color: #f4f4f4; padding: 2px 6px; font-size: 14px;">{{ $password }}</code></p>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#fff3cd" style="border: 1px solid #ffeaa7; border-radius: 4px; margin: 20px 0; font-size: 14px; color: #856404;">
        <tr>
            <td>
                <strong>Important:</strong> Please change your password after your first login for security purposes.
            </td>
        </tr>
    </table>

    <p>You can now log in to the system and start managing internship opportunities for students.</p>

    <p style="margin-top: 30px;">Regards,<br>SISP Team</p>
@endsection

