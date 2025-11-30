@extends('emails.base')

@section('content')
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>

    <!-- SLOVAK VERSION -->
    <h1>Aktualizácia registrácie spoločnosti</h1>
    <h3>Dobrý deň {{ $contactPersonName }},</h3>

    <p>Ďakujeme za váš záujem o registráciu spoločnosti <strong>{{ $companyName }}</strong> v našom systéme riadenia stáží.</p>

    <p>Po dôkladnom preskúmaní vás s ľútosťou informujeme, že vaša žiadosť o registráciu spoločnosti nebola v tomto čase schválená.</p>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#ffebee" style="border-left: 4px solid #e74c3c; border-radius: 4px; margin: 20px 0;">
        <tr>
            <td>
                <p style="margin: 5px 0;"><strong>Dôvod zamietnutia:</strong></p>
                <p style="margin: 10px 0; color: #555;">{{ $rejectionReason }}</p>
            </td>
        </tr>
    </table>

    <p>Ak si myslíte, že toto rozhodnutie bolo prijaté omylom alebo ak by ste chceli riešiť vznesené pripomienky, neváhajte kontaktovať náš administratívny tým.</p>

    <p>Po vyriešení vyššie uvedených problémov môžete podať novú žiadosť o registráciu.</p>

    <p style="margin-top: 30px;">S pozdravom,<br>Tím SISP</p>

    <hr style="border: none; border-top: 1px solid #eaeaea; margin: 40px 0;">

    <!-- ENGLISH VERSION -->
    <h1>Company Registration Request Update</h1>
    <h3>Dear {{ $contactPersonName }},</h3>

    <p>Thank you for your interest in registering <strong>{{ $companyName }}</strong> with our internship management system.</p>

    <p>After careful review, we regret to inform you that your company registration request has not been approved at this time.</p>

    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#ffebee" style="border-left: 4px solid #e74c3c; border-radius: 4px; margin: 20px 0;">
        <tr>
            <td>
                <p style="margin: 5px 0;"><strong>Reason for rejection:</strong></p>
                <p style="margin: 10px 0; color: #555;">{{ $rejectionReason }}</p>
            </td>
        </tr>
    </table>

    <p>If you believe this decision was made in error or if you would like to address the concerns raised, please feel free to contact our administration team.</p>

    <p>You are welcome to submit a new registration request after addressing the issues mentioned above.</p>

    <p style="margin-top: 30px;">Regards,<br>SISP Team</p>
@endsection

