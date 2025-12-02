<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="sk" xml:lang="sk">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hodnotenie stáže - SISP</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 30px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
            margin-bottom: 30px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .evaluation-criteria {
            margin-bottom: 30px;
        }
        .criterion {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .criterion:last-child {
            border-bottom: none;
        }
        .criterion-label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }
        .rating-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .rating-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .rating-option input[type="radio"] {
            margin: 0;
        }
        .rating-option label {
            margin: 0;
            cursor: pointer;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            margin: 30px auto 0;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SISP - Hodnotenie stáže</h1>

        @if(isset($error))
            <div class="error">
                <strong>Chyba:</strong> {{ $error }}
            </div>
        @endif

        @if(isset($success) && $success)
            <div class="success">
                <strong>Úspech:</strong> Hodnotenie bolo úspešne odoslané. Ďakujeme za vaše hodnotenie.
            </div>
        @else
            <div class="info-box">
                <h3>Informácie o stáži:</h3>
                <p><strong>Študent:</strong> {{ $studentName ?? 'N/A' }}</p>
                <p><strong>Spoločnosť:</strong> {{ $companyName ?? 'N/A' }}</p>
                <p><strong>Akademický rok:</strong> {{ $academyYear ?? 'N/A' }}</p>
                <p><strong>Dátum začiatku:</strong> {{ $startDate ?? 'N/A' }}</p>
                <p><strong>Dátum konca:</strong> {{ $endDate ?? 'N/A' }}</p>
            </div>

            <form method="POST" action="{{ $submitUrl }}">
                @csrf
                
                <div class="evaluation-criteria">
                    <h3>Kritériá hodnotenia (1 – najmenej vyhovujúce, 5 – najviac vyhovujúce, n/a – not applicable)</h3>
                    
                    @php
                        $criteria = [
                            'organizovanie_a_planovanie_prace' => 'Organizovanie a plánovanie práce',
                            'schopnost_pracovat_v_time' => 'Schopnosť pracovať v tíme',
                            'schopnost_ucit_sa' => 'Schopnosť učiť sa',
                            'uroven_digitalnej_gramotnosti' => 'Úroveň digitálnej gramotnosti',
                            'kultivovanost_prejavu' => 'Kultivovanosť prejavu',
                            'pouzivanie_zauzivanych_vyrazov' => 'Používanie zaužívaných výrazov',
                            'prezentovanie' => 'Prezentovanie',
                            'samostatnost' => 'Samostatnosť',
                            'adaptabilita' => 'Adaptabilita',
                            'flexibilita' => 'Flexibilita',
                            'schopnost_improvizovat' => 'Schopnosť improvizovať',
                            'schopnost_prijimat_rozhodnutia' => 'Schopnosť prijímať rozhodnutia',
                            'schopnost_niest_zodpovednost' => 'Schopnosť niesť zodpovednosť',
                            'dodrzovanie_etickych_zasad' => 'Dodržovanie etických zásad',
                            'schopnost_jednania_s_ludmi' => 'Schopnosť jednania s ľuďmi'
                        ];
                    @endphp

                    @foreach($criteria as $key => $label)
                        <div class="criterion">
                            <label class="criterion-label">{{ $label }}:</label>
                            <div class="rating-options">
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_1" name="evaluation[{{ $key }}]" value="1" required>
                                    <label for="{{ $key }}_1">1</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_2" name="evaluation[{{ $key }}]" value="2">
                                    <label for="{{ $key }}_2">2</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_3" name="evaluation[{{ $key }}]" value="3">
                                    <label for="{{ $key }}_3">3</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_4" name="evaluation[{{ $key }}]" value="4">
                                    <label for="{{ $key }}_4">4</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_5" name="evaluation[{{ $key }}]" value="5">
                                    <label for="{{ $key }}_5">5</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_na" name="evaluation[{{ $key }}]" value="n/a">
                                    <label for="{{ $key }}_na">n/a</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="submit-btn">Odoslať hodnotenie</button>
            </form>
        @endif

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; text-align: center; color: #6c757d; font-size: 12px;">
            <p>© {{ date('Y') }} SISP – Systém riadenia stáží</p>
        </div>
    </div>
</body>
</html>

