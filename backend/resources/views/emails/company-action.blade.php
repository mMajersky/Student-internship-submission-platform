<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="sk" xml:lang="sk">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Výsledok akcie stáže - SISP</title>
</head>
<body style="margin: 0 !important; padding: 0 !important; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; background: #f6f6f6; font-family: Arial, sans-serif;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f6f6f6" style="margin: 0; padding: 20px;">
    <tr>
        <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-radius: 8px;">
                <tr>
                    <td style="padding: 30px;">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef;">
                                    <h1 style="color: #007BFF; margin: 0; font-size: 24px; font-family: Arial, sans-serif;">SISP</h1>
                                    <p style="color: #666; font-family: Arial, sans-serif;">Systém riadenia stáží</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    @if($success ?? false)
                                        <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#d4edda" style="border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0;">
                                            <tr>
                                                <td style="color: #155724;">
                                                    <h3 style="margin: 0 0 10px 0;">Akcia bola úspešne dokončená!</h3>
                                                    <p style="margin: 0; font-size: 14px;">Stáž bola úspešne {{ $action === 'confirm' ? 'potvrdená spoločnosťou' : 'nepotvrdená spoločnosťou' }}.</p>
                                                </td>
                                            </tr>
                                        </table>

                                        @if(isset($internship))
                                            <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#f8f9fa" style="border-radius: 4px; border-left: 4px solid #007bff; margin: 20px 0;">
                                                <tr>
                                                    <td>
                                                        <h4 style="margin: 0 0 10px 0; color: #333;">Podrobnosti o stáži:</h4>
                                                        <p style="margin: 5px 0;"><strong>Študent:</strong> {{ $internship->student->name ?? 'N/A' }} {{ $internship->student->surname ?? '' }}</p>
                                                        <p style="margin: 5px 0;"><strong>Spoločnosť:</strong> {{ $internship->company->name ?? 'N/A' }}</p>
                                                        <p style="margin: 5px 0;"><strong>Akademický rok:</strong> {{ $internship->academy_year }}</p>
                                                        <p style="margin: 5px 0;"><strong>Stav:</strong> {{ ucfirst($internship->status) }}</p>
                                                        <p style="margin: 5px 0;"><strong>Akcia:</strong> {{ ucfirst($action) }}</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif

                                        <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#d1ecf1" style="border: 1px solid #bee5eb; border-radius: 4px; margin: 20px 0;">
                                            <tr>
                                                <td style="color: #0c5460;">
                                                    <p style="margin: 0; font-size: 14px;">Môžete teraz zatvoriť toto okno. Stáž bola {{ $action === 'confirm' ? 'potvrdená spoločnosťou' : 'nepotvrdená spoločnosťou' }} a študent bude informovaný.</p>
                                                </td>
                                            </tr>
                                        </table>

                                    @else
                                        <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#f8d7da" style="border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px 0;">
                                            <tr>
                                                <td style="color: #721c24;">
                                                    <h3 style="margin: 0 0 10px 0;">Chyba pri spracovaní akcie</h3>
                                                    <p style="margin: 0; font-size: 14px;">{{ $error ?? 'Nastala neznáma chyba.' }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#d1ecf1" style="border: 1px solid #bee5eb; border-radius: 4px; margin: 20px 0;">
                                            <tr>
                                                <td style="color: #0c5460;">
                                                    <p style="margin: 0 0 5px 0; font-size: 14px;">Ak si myslíte, že ide o chybu, kontaktujte svojho garanta alebo skúste použiť odkaz z vášho emailu znovu.</p>
                                                    @if(isset($internship) && $internship->garant)
                                                        <p style="margin: 0; font-size: 14px;"><strong>Kontakt na garanta:</strong> {{ $internship->garant->user->email ?? 'garant@school.sk' }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    @endif

                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px solid #e9ecef; margin-top: 30px; padding-top: 10px;">
                                        <tr>
                                            <td style="text-align: center; color: #6c757d; font-size: 14px; font-family: Arial, sans-serif;">
                                                <p style="margin: 0;">© {{ date('Y') }} SISP – Automated Notification</p>
                                                <p style="margin: 5px 0 0 0; font-size: 12px;">ENG: This is an automated message. Please do not reply to this email.</p>
                                                <p style="margin: 5px 0 0 0; font-size: 12px;">SK: Toto je automatická správa zo systému. Prosím, neodpovedajte na tento e-mail.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 40px;">
                                    <!-- ENGLISH VERSION -->
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef;">
                                                <h1 style="color: #007BFF; margin: 0; font-size: 24px; font-family: Arial, sans-serif;">SISP Platform</h1>
                                                <p style="color: #666; font-family: Arial, sans-serif;">Internship Management System</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                @if($success ?? false)
                                                    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#d4edda" style="border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0;">
                                                        <tr>
                                                            <td style="color: #155724;">
                                                                <h3 style="margin: 0 0 10px 0;">The action has been completed successfully!</h3>
                                                                <p style="margin: 0; font-size: 14px;">{{ $message }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    @if(isset($internship))
                                                        <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#f8f9fa" style="border-radius: 4px; border-left: 4px solid #007bff; margin: 20px 0;">
                                                            <tr>
                                                                <td>
                                                                    <h4 style="margin: 0 0 10px 0; color: #333;">Internship Details:</h4>
                                                                    <p style="margin: 5px 0;"><strong>Student:</strong> {{ $internship->student->name ?? 'N/A' }} {{ $internship->student->surname ?? '' }}</p>
                                                                    <p style="margin: 5px 0;"><strong>Company:</strong> {{ $internship->company->name ?? 'N/A' }}</p>
                                                                    <p style="margin: 5px 0;"><strong>Academic Year:</strong> {{ $internship->academy_year }}</p>
                                                                    @php
                                                                        $statusEn = match($internship->status) {
                                                                            'created' => 'Created',
                                                                            'approved by garant' => 'Approved by garant',
                                                                            'rejected by garant' => 'Rejected by garant',
                                                                            'defended by student' => 'Defended by student',
                                                                            'not defended by student' => 'Not defended by student',
                                                                            'confirmed by company' => 'Confirmed by company',
                                                                            'not confirmed by company' => 'Not confirmed by company',
                                                                            default => ucfirst($internship->status)
                                                                        };
                                                                    @endphp
                                                                    <p style="margin: 5px 0;"><strong>Status:</strong> {{ $statusEn }}</p>
                                                                    <p style="margin: 5px 0;"><strong>Action:</strong> {{ ucfirst($action) }}</p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endif

                                                    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#d1ecf1" style="border: 1px solid #bee5eb; border-radius: 4px; margin: 20px 0;">
                                                        <tr>
                                                            <td style="color: #0c5460;">
                                                                <p style="margin: 0; font-size: 14px;">You can now close this window. The internship has been {{ $action === 'confirm' ? 'approved' : 'rejected' }} and the student will be notified.</p>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                @else
                                                    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#f8d7da" style="border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px 0;">
                                                        <tr>
                                                            <td style="color: #721c24;">
                                                                <h3 style="margin: 0 0 10px 0;">Error processing the action</h3>
                                                                <p style="margin: 0; font-size: 14px;">{{ $error ?? 'An unknown error occurred.' }}</p>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <table width="100%" border="0" cellpadding="15" cellspacing="0" bgcolor="#d1ecf1" style="border: 1px solid #bee5eb; border-radius: 4px; margin: 20px 0;">
                                                        <tr>
                                                            <td style="color: #0c5460;">
                                                                <p style="margin: 0 0 5px 0; font-size: 14px;">If you think this is a mistake, contact your supervisor or try using the link from your email again.</p>
                                                                @if(isset($internship) && $internship->garant)
                                                                    <p style="margin: 0; font-size: 14px;"><strong>Contact the Student's Supervisor:</strong> {{ $internship->garant->user->email ?? 'supervisor@school.sk' }}</p>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                @endif

                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px solid #e9ecef; margin-top: 30px; padding-top: 10px;">
                                                    <tr>
                                                        <td style="text-align: center; color: #6c757d; font-size: 14px; font-family: Arial, sans-serif;">
                                                            <p style="margin: 0;">© {{ date('Y') }} SISP – Automated Notification</p>
                                                            <p style="margin: 5px 0 0 0; font-size: 12px;">ENG: This is an automated message. Please do not reply to this email.</p>
                                                            <p style="margin: 5px 0 0 0; font-size: 12px;">SK: Toto je automatická správa zo systému. Prosím, neodpovedajte na tento e-mail.</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
