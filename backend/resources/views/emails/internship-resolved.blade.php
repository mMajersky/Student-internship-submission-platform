<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stáž už bola vyriešená - Platforma SISP</title>
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
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ffeaa7;
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
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <p style="font-size: 12px; color: #666; text-align: center; margin-bottom: 20px;">English version can be found below the Slovak version.</p>
    <div class="container">
        <div class="header">
            <h1>Platforma SISP</h1>
            <p>Systém riadenia stáží</p>
        </div>

        <div class="warning">
            <h3>Akcia už nie je dostupná</h3>
            <p>Táto žiadosť o stáž už bola spracovaná a vyriešená.</p>
        </div>

        @if(isset($internship))
            <div class="internship-details">
                <h4>Podrobnosti o stáži:</h4>
                <p><strong>Študent:</strong> {{ $internship->student->name ?? 'N/A' }} {{ $internship->student->surname ?? '' }}</p>
                <p><strong>Spoločnosť:</strong> {{ $internship->company->name ?? 'N/A' }}</p>
                <p><strong>Akademický rok:</strong> {{ $internship->academy_year }}</p>
                <p><strong>Aktuálny stav:</strong>
                    <span class="status-badge status-{{ strtolower($internship->status) }}">
                        {{ ucfirst($internship->status) }}
                    </span>
                </p>
            </div>
        @endif

        <div class="info">
            <p>Spoločnosť už prijala opatrenia týkajúce sa tejto žiadosti o stáž. Odkazy v emaili už nie sú platné.</p>
            <p>Ak si myslíte, že ide o chybu alebo potrebujete vykonať zmeny, kontaktujte svojho garanta.</p>
            @if(isset($internship) && $internship->garant)
            <p><strong>Kontakt na garanta:</strong> {{ $internship->garant->user->email ?? 'garant@school.sk' }}</p>
            @endif
        </div>

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

        <div class="warning">
            <h3>Action no longer available</h3>
            <p>This internship application has already been processed and resolved.</p>
        </div>

        @if(isset($internship))
            <div class="internship-details">
                <h4>Internship Details:</h4>
                <p><strong>Student:</strong> {{ $internship->student->name ?? 'N/A' }} {{ $internship->student->surname ?? '' }}</p>
                <p><strong>Company:</strong> {{ $internship->company->name ?? 'N/A' }}</p>
                <p><strong>Academic Year:</strong> {{ $internship->academy_year }}</p>
                <p><strong>Current Status:</strong>
                    <span class="status-badge status-{{ strtolower($internship->status) }}">
                        {{ ucfirst($internship->status) }}
                    </span>
                </p>
            </div>
        @endif

        <div class="info">
            <p>The company has already taken action regarding this internship application. The links in the email are no longer valid.</p>
            <p>If you think this is a mistake or need to make changes, contact your garant.</p>
            @if(isset($internship) && $internship->garant)
            <p><strong>Contact Garant:</strong> {{ $internship->garant->user->email ?? 'garant@school.sk' }}</p>
            @endif
        </div>

        <div class="footer">
            <p>SISP Platform - Student Internship Application Platform</p>
            <p>This is an automated message from the system.</p>
        </div>
    </div>
</body>
</html>
