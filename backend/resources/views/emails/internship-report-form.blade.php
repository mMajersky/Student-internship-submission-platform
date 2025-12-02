<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="sk" xml:lang="sk">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Výkaz o vykonanej odbornej praxi - SISP</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
        }
        .container {
            max-width: 1000px;
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
            text-transform: uppercase;
            font-size: 24px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }
        .section-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
            text-transform: uppercase;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .activities-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .activities-table th,
        .activities-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .activities-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .activities-table input[type="date"],
        .activities-table input[type="text"],
        .activities-table input[type="number"] {
            width: 100%;
            padding: 5px;
            border: none;
            font-size: 13px;
        }
        .btn-add-row {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 14px;
        }
        .btn-add-row:hover {
            background-color: #0056b3;
        }
        .btn-remove-row {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
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
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .evaluation-criteria {
            margin-top: 20px;
        }
        .criterion {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .criterion-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .rating-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .rating-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Výkaz o vykonanej odbornej praxi</h1>
        
        @php
            // Define criteria globally for JavaScript validation
            $criteria = $criteria ?? [
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

        @if(isset($error))
            <div class="error">
                <strong>Chyba:</strong> {{ $error }}
            </div>
        @endif

        @if(isset($success) && $success)
            <div class="success">
                <strong>Úspech:</strong> Výkaz praxe bol úspešne odoslaný. Ďakujeme.
            </div>
        @else
            <form method="POST" action="{{ $submitUrl }}" id="reportForm">
                @csrf
                <input type="hidden" name="upload_mode" value="false">
                
                <!-- ŠTUDENT sekcia -->
                <div class="section">
                    <div class="section-title">Študent</div>
                    
                    <div class="two-column">
                        <div class="form-group">
                            <label for="student_name">Meno a priezvisko:</label>
                            <input type="text" id="student_name" name="report[student_name]" value="{{ $studentName ?? '' }}" readonly required>
                        </div>
                        
                        <div class="form-group">
                            <label for="student_program">Študijný program:</label>
                            <input type="text" id="student_program" name="report[student_program]" value="{{ $studentProgram ?? '' }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="school_name">Názov a sídlo školy:</label>
                        <input type="text" id="school_name" name="report[school_name]" value="{{ $schoolName ?? '' }}" required>
                    </div>
                </div>

                <!-- ORGANIZÁCIA sekcia -->
                <div class="section">
                    <div class="section-title">Organizácia</div>
                    
                    <div class="form-group">
                        <label for="company_name">Názov a sídlo organizácie/pracoviska praxe:</label>
                        <input type="text" id="company_name" name="report[company_name]" value="{{ $companyName ?? '' }}" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tutor_name">Meno a priezvisko tútora praxe:</label>
                        <input type="text" id="tutor_name" name="report[tutor_name]" value="{{ $tutorName ?? '' }}" required>
                    </div>
                </div>

                <!-- OBDOBIE ABSOLVOVANIA sekcia -->
                <div class="section">
                    <div class="section-title">Obdobie absolvovania odbornej praxe</div>
                    
                    <div class="two-column">
                        <div class="form-group">
                            <label for="start_date">Dátum nástupu na prax:</label>
                            <input type="date" id="start_date" name="report[start_date]" value="{{ $startDate ?? '' }}" readonly required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">Dátum ukončenia praxe:</label>
                            <input type="date" id="end_date" name="report[end_date]" value="{{ $endDate ?? '' }}" readonly required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_hours">Celkový počet hodín absolvovanej praxe:</label>
                        <input type="number" id="total_hours" name="report[total_hours]" min="0" step="0.5" required>
                    </div>
                </div>

                <!-- PRACOVNÉ ČINNOSTI sekcia -->
                <div class="section">
                    <div class="section-title">Pracovné činnosti</div>
                    
                    <table class="activities-table">
                        <thead>
                            <tr>
                                <th>Dátum</th>
                                <th>Popis činností</th>
                                <th>Počet hodín</th>
                            </tr>
                        </thead>
                        <tbody id="activitiesTableBody">
                            <tr>
                                <td><input type="date" name="report[activities][0][date]" required></td>
                                <td><textarea name="report[activities][0][description]" required></textarea></td>
                                <td><input type="number" name="report[activities][0][hours]" min="0" step="0.5" required></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <button type="button" class="btn-add-row" onclick="addActivityRow()">Pridať riadok</button>
                </div>

                <!-- HODNOTENIE OD FIRMY sekcia -->
                <div class="section evaluation-criteria">
                    <div class="section-title">Hodnotenie od firmy</div>
                    <p style="margin-bottom: 15px; color: #666;">Kritériá hodnotenia (1 – najmenej vyhovujúce, 5 – najviac vyhovujúce, n/a – not applicable)</p>
                    
                    @foreach($criteria as $key => $label)
                        <div class="criterion">
                            <label class="criterion-label">{{ $label }}:</label>
                            <div class="rating-options">
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_1" name="report[evaluation][{{ $key }}]" value="1" required>
                                    <label for="{{ $key }}_1">1</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_2" name="report[evaluation][{{ $key }}]" value="2">
                                    <label for="{{ $key }}_2">2</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_3" name="report[evaluation][{{ $key }}]" value="3">
                                    <label for="{{ $key }}_3">3</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_4" name="report[evaluation][{{ $key }}]" value="4">
                                    <label for="{{ $key }}_4">4</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_5" name="report[evaluation][{{ $key }}]" value="5">
                                    <label for="{{ $key }}_5">5</label>
                                </div>
                                <div class="rating-option">
                                    <input type="radio" id="{{ $key }}_na" name="report[evaluation][{{ $key }}]" value="n/a">
                                    <label for="{{ $key }}_na">n/a</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="submit-btn">Odoslať výkaz praxe</button>
            </form>
        @endif

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; text-align: center; color: #6c757d; font-size: 12px;">
            <p>© {{ date('Y') }} SISP – Systém riadenia stáží</p>
        </div>
    </div>

    <script>
        let activityRowIndex = 1;
        
        function addActivityRow() {
            const tbody = document.getElementById('activitiesTableBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="date" name="report[activities][${activityRowIndex}][date]" required></td>
                <td><textarea name="report[activities][${activityRowIndex}][description]" required></textarea></td>
                <td><input type="number" name="report[activities][${activityRowIndex}][hours]" min="0" step="0.5" required></td>
            `;
            tbody.appendChild(row);
            activityRowIndex++;
        }
        
        function removeActivityRow(button) {
            const row = button.closest('tr');
            if (document.getElementById('activitiesTableBody').rows.length > 1) {
                row.remove();
            } else {
                alert('Musí byť aspoň jeden riadok pracovných činností.');
            }
        }

        // JavaScript validation - ensure all criteria have at least one option selected or n/a
        @if(isset($criteria))
        const reportFormValidation = document.getElementById('reportForm');
        if (reportFormValidation) {
            reportFormValidation.addEventListener('submit', function(e) {
                const criteria = @json(array_keys($criteria));
            let allValid = true;
            
            criteria.forEach(key => {
                const radios = document.querySelectorAll(`input[name="report[evaluation][${key}]"]`);
                const checked = Array.from(radios).some(radio => radio.checked);
                
                if (!checked) {
                    allValid = false;
                    // Automatically select n/a if nothing is selected
                    const naRadio = document.getElementById(`${key}_na`);
                    if (naRadio) {
                        naRadio.checked = true;
                    }
                }
            });
            
            if (!allValid) {
                // Auto-select n/a for unchecked criteria
                criteria.forEach(key => {
                    const radios = document.querySelectorAll(`input[name="report[evaluation][${key}]"]`);
                    const checked = Array.from(radios).some(radio => radio.checked);
                    if (!checked) {
                        const naRadio = document.getElementById(`${key}_na`);
                        if (naRadio) {
                            naRadio.checked = true;
                        }
                    }
                });
            }
            });
        }
        @endif
    </script>
</body>
</html>

