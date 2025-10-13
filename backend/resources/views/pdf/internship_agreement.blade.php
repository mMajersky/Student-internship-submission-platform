<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 15px;
        }

        .field {
            margin-left: 30px;
            margin-bottom: 5px;
        }

        .article-title {
            font-weight: bold;
            margin-top: 20px;
        }

        ul {
            margin-left: 20px;
            margin-top: 5px;
        }

        ol {
            margin-left: 20px;
        }

        .page-break {
            page-break-after: always;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }

        .signature-table td {
            width: 33%;
            vertical-align: top;
        }

        .signature-line {
            display: inline-block;
            width: 80%;
            border-bottom: 1px solid #000;
            margin-bottom: 3px;
        }

        footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <h1>DOHODA O ODBORNEJ PRAXI ŠTUDENTA</h1>
    <div class="subtitle">
        uzatvorená v zmysle § 51 Občianskeho zákonníka a Zákona č. 131/2002 Z.z. o vysokých školách
    </div>

    <div class="section">
        <strong>Univerzita Konštantína Filozofa v Nitre</strong><br>
        Fakulta prírodných vied a informatiky<br>
        Trieda A. Hlinku 1, 949 01 Nitra<br>
        v zastúpení prof. RNDr. František Petrovič, PhD. – dekan fakulty<br>
        e-mail: fpetrovic@ukf.sk,&nbsp;&nbsp;tel. 037/6408 555
    </div>

    <div class="section">
        <strong>Poskytovateľ odbornej praxe (organizácia, resp. inštitúcia)</strong><br>
        Plný názov a adresa: {{ $company_name ?? '..............................................................' }}<br>
        v zastúpení: {{ $company_contact ?? '.............................................................. (meno, pozícia)' }}
    </div>

    <div class="section">
        <strong>Študent:</strong><br>
        Meno a priezvisko: {{ $student_name ?? '..............................................................' }}<br>
        Adresa trvalého bydliska: {{ $student_address ?? '..............................................................' }}<br>
        Kontakt študenta FPVaI UKF v Nitre: {{ $student_contact ?? '..............................................................' }}<br>
        Študijný program: {{ $study_program ?? '..............................................................' }}
    </div>

    <p>uzatvárajú túto dohodu o odbornej praxi študenta.</p>

    <div class="article-title">I. Predmet dohody</div>
    <p>
        Predmetom tejto dohody je vykonanie odbornej praxe študenta v rozsahu 150 hodín,
        v termíne od {{ $start_date ?? '....................' }} do {{ $end_date ?? '....................' }} bezodplatne.
    </p>

    <div class="article-title">II. Práva a povinnosti účastníkov dohody</div>

    <p><strong>1. Fakulta prírodných vied a informatiky Univerzity Konštantína Filozofa v Nitre:</strong></p>
    <p>
        1.1 Poverí svojho zamestnanca: Mgr. Martin Vozár, PhD. (mvozar@ukf.sk) za 1. stupeň,
        PaedDr. Peter Švec, Ph.D. (psvec@ukf.sk) za 2. stupeň
        (ďalej garant odbornej praxe) garanciou odbornej praxe.
    </p>
    <p>
        1.2 Prostredníctvom garanta odbornej praxe:
    </p>
    <ul>
        <li>poskytne študentovi informácie o organizácii praxe, o podmienkach dojednania dohody o odbornej praxi, o obsahovom zameraní odbornej praxe a o požiadavkách na obsahovú náplň správy z odbornej praxe,</li>
        <li>poskytne návrh dohody o odbornej praxi študenta,</li>
        <li>rozhodne o udelení hodnotenia „ABS“ (absolvoval) študentovi na základe dokladu „Výkaz o vykonanej odbornej praxi“, vydaného poskytovateľom odbornej praxe a na základe študentom vypracovanej správy o odbornej praxi, ktorej súčasťou je verejná obhajoba výsledkov odbornej praxe,</li>
        <li>spravuje vyplnenú a účastníkmi podpísanú dohodu o odbornej praxi.</li>
    </ul>

    <p><strong>2. Poskytovateľ odbornej praxe:</strong></p>
    <ul>
        <li>poverí svojho zamestnanca (tútor - zodpovedný za odbornú prax v organizácii) {{ $tutor_name ?? '.....................................' }}, ktorý bude dohliadať na dodržiavanie dohody o odbornej praxi, plnenie obsahovej náplne odbornej praxe a bude nápomocný pri získavaní potrebných údajov pre vypracovanie správy z odbornej praxe,</li>
        <li>na začiatku praxe vykoná poučenie o bezpečnosti a ochrane zdravia pri práci v zmysle platných predpisov,</li>
        <li>vzniknuté organizačné problémy súvisiace s plnením dohody rieši spolu s garantom odbornej praxe,</li>
        <li>po ukončení odbornej praxe vydá študentovi „Výkaz o vykonanej odbornej praxi“, ktorý obsahuje popis vykonávaných činností a stručné hodnotenie študenta a je jedným z predpokladov úspešného ukončenia predmetu Odborná prax,</li>
        <li>umožní garantovi odbornej praxe a garantovi študijného predmetu kontrolu študentom plnených úloh.</li>
    </ul>

    <p><strong>3. Študent FPVaI UKF v Nitre:</strong></p>
    <ul>
        <li>osobne zabezpečí podpísanie tejto dohody o odbornej praxi študenta,</li>
        <li>zodpovedne vykonáva činnosti pridelené tútorom odbornej praxe,</li>
        <li>zabezpečí doručenie dokladu „Výkaz o vykonanej odbornej praxi“ najneskôr v termínoch predpísaných garantom pre daný semester,</li>
        <li>okamžite, bez zbytočného odkladu informuje garanta odbornej praxe o problémoch, ktoré bránia plneniu odbornej praxe.</li>
    </ul>
    
    <div class="article-title">III. Všeobecné a záverečné ustanovenia</div>
    <ol>
        <li>Dohoda sa uzatvára na dobu určitú. Dohoda nadobúda platnosť a účinnosť dňom podpísania obidvomi zmluvnými stranami. Obsah dohody sa môže meniť písomne len po súhlase jej zmluvných strán.</li>
        <li>Dohoda sa uzatvára v 3 vyhotoveniach, každá zmluvná strana obdrží jedno vyhotovenie dohody.</li>
    </ol>

    <div style="margin-top:30px;">
        V Nitre, dňa {{ $generation_date ?? '....................' }} &nbsp;&nbsp;&nbsp;&nbsp;
        V {{ $company_city ?? '....................' }}, dňa {{ $sign_date ?? '....................' }}
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div><br>
                prof. RNDr. František Petrovič, PhD.<br>
                <small>dekan FPVaI UKF v Nitre</small>
            </td>
            <td>
                <div class="signature-line"></div><br>
                <small>štatutárny zástupca pracoviska odb. praxe</small>
            </td>
            <td>
                <div class="signature-line"></div><br>
                <small>meno a priezvisko študenta</small>
            </td>
        </tr>
    </table>

    <footer>
        Vygenerované: {{ $generation_date ?? date('d.m.Y') }} | Strana 2
    </footer>

</body>
</html>
