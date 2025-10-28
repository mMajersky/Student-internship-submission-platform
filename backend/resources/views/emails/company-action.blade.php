<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Výsledok akcie stáže - SISP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin: 20px 0;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #bee5eb;
            margin: 20px 0;
        }
        .internship-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .button.secondary {
            background-color: #6c757d;
        }
        .button.secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>
    <div class="container">
        <div class="header">
            <h1>SISP</h1>
            <p>Systém riadenia stáží</p>
        </div>

        @if($success ?? false)
            <div class="success">
                <h3>Akcia bola úspešne dokončená!</h3>
                <p>{{ $message }}</p>
            </div>

            @if(isset($internship))
                <div class="internship-details">
                    <h4>Podrobnosti o stáži:</h4>
                    <p><strong>Študent:</strong> {{ $internship->student->name ?? 'N/A' }} {{ $internship->student->surname ?? '' }}</p>
                    <p><strong>Spoločnosť:</strong> {{ $internship->company->name ?? 'N/A' }}</p>
                    <p><strong>Akademický rok:</strong> {{ $internship->academy_year }}</p>
                    <p><strong>Stav:</strong> {{ ucfirst($internship->status) }}</p>
                    <p><strong>Akcia:</strong> {{ ucfirst($action) }}</p>
                </div>
            @endif

            <div class="info">
                <p>Môžete teraz zatvoriť toto okno. Stáž bola {{ $action === 'confirm' ? 'schválená' : 'zamietnutá' }} a študent bude informovaný.</p>
            </div>

        @else
            <div class="error">
                <h3>Chyba pri spracovaní akcie</h3>
                <p>{{ $error ?? 'Nastala neznáma chyba.' }}</p>
            </div>

            <div class="info">
                <p>Ak si myslíte, že ide o chybu, kontaktujte svojho garanta alebo skúste použiť odkaz z vášho emailu znovu.</p>
                @if(isset($internship) && $internship->garant)
                <p><strong>Kontakt na garanta:</strong> {{ $internship->garant->user->email ?? 'garant@school.sk' }}</p>
                @endif
            </div>
        @endif

        <div class="footer">
            <p>Platforma SISP - Platforma na podávanie žiadostí o stáže študentov</p>
            <p>Toto je automatická správa zo systému.</p>
        </div>
    </div>

    <!-- ENGLISH VERSION -->
    <div class="container" style="margin-top: 40px;">
        <div class="header">
            <h1>SISP Platform</h1>
            <p>Internship Management System</p>
        </div>

        @if($success ?? false)
            <div class="success">
                <h3>The action has been completed successfully!</h3>
                <p>{{ $message }}</p>
            </div>

            @if(isset($internship))
                <div class="internship-details">
                    <h4>Internship Details:</h4>
                    <p><strong>Student:</strong> {{ $internship->student->name ?? 'N/A' }} {{ $internship->student->surname ?? '' }}</p>
                    <p><strong>Company:</strong> {{ $internship->company->name ?? 'N/A' }}</p>
                    <p><strong>Academic Year:</strong> {{ $internship->academy_year }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($internship->status) }}</p>
                    <p><strong>Action:</strong> {{ ucfirst($action) }}</p>
                </div>
            @endif

            <div class="info">
                <p>You can now close this window. The internship has been {{ $action === 'confirm' ? 'approved' : 'rejected' }} and the student will be notified.</p>
            </div>

        @else
            <div class="error">
                <h3>Error processing the action</h3>
                <p>{{ $error ?? 'An unknown error occurred.' }}</p>
            </div>

            <div class="info">
                <p>If you think this is a mistake, contact your supervisor or try using the link from your email again.</p>
                @if(isset($internship) && $internship->garant)
                <p><strong>Contact Supervisor:</strong> {{ $internship->garant->user->email ?? 'supervisor@school.sk' }}</p>
                @endif
            </div>
        @endif

        <div class="footer">
            <p>SISP Platform - Student Internship Application Platform</p>
            <p>This is an automated message from the system.</p>
        </div>
    </div>
</body>
</html>
