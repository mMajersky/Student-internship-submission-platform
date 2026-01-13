<!DOCTYPE html>
<html lang="sk">
<head>
     <meta charset="UTF-8">
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Dohoda o odbornej praxi študenta</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        @font-face {
    font-family: 'Calibri';
    font-style: normal;
    font-weight: 400;
    src: url("{{ storage_path('fonts/Calibri-Regular.ttf') }}") format("truetype");
}
@font-face {
    font-family: 'Calibri';
    font-style: normal;
    font-weight: 700;
    src: url("{{ storage_path('fonts/Calibri-Bold.ttf') }}") format("truetype");
}
@font-face {
    font-family: 'Calibri';
    font-style: italic;
    font-weight: 400;
    src: url("{{ storage_path('fonts/Calibri-Italic.ttf') }}") format("truetype");
}
@font-face {
    font-family: 'Calibri';
    font-style: italic;
    font-weight: 700;
    src: url("{{ storage_path('fonts/Calibri-BoldItalic.ttf') }}") format("truetype");
}

        body {
            font-family: "Calibri", sans-serif;
            font-size: 11pt;
            line-height: 1.15;
            margin: 0;
            padding: 0;
            text-align: justify;
        }

        p {
            margin-top: 3pt;
            margin-bottom: 0;
            text-align: justify;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        b {
            font-weight: bold;
        }

        .text-upper {
            text-transform: uppercase;
        }

        .indent-1 {
            text-indent: 1cm;
        }

        .margin-left-18 {
            margin-left: 18pt;
        }

        .margin-left-21 {
            margin-left: 21.3pt;
        }

        .margin-left-36 {
            margin-left: 36pt;
        }

        .margin-left-48 {
            margin-left: 48pt;
        }

        .no-margin-top {
            margin-top: 0;
        }

        .dotted-line {
            border: none;
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 100px;
        }

        a {
            color: green;
            text-decoration: underline;
        }
        
        .header-note {
            font-size: 9pt;
            text-align: right;
            margin-bottom: 10pt;
        }
        
        .signature-block {
            margin-top: 40pt;
        }
        
        .signature-line {
            border: none;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            margin-bottom: 5pt;
        }
    </style>
</head>
<body>

<div class="header-note"><i>Platnosť tlačiva od 1.10.2022 (aplikovaná informatika)</i></div>

<p class="center no-margin-top"><b class="text-upper">Dohoda o odbornej praxi študenta</b></p>

<p class="center no-margin-top">uzatvorená v zmysle § 51 Občianskeho zákonníka a Zákona č. 131/2002 Z.z. o vysokých školách</p>

<p class="left no-margin-top">&nbsp;</p>

<p class="left no-margin-top"><b>Univerzita Konštantína Filozofa v Nitre</b></p>

<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fakulta prírodných vied a informatiky</p>

<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trieda A. Hlinku 1, 949 01 Nitra</p>

<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;v zastúpení prof. RNDr. František Petrovič, PhD. – dekan fakulty</p>

<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e-mail: <a href="mailto:fpetrovic@ukf.sk">fpetrovic@ukf.sk</a>, tel. 037/6408 555</p>

<p class="left no-margin-top">&nbsp;</p>

<p class="left no-margin-top"><b>Poskytovateľ odbornej praxe (organizácia, resp. inštitúcia)</b></p>

<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plný názov: <span class="dotted-line" style="width: 350px;">{{ $company_name }}</span></p>
<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adresa: <span class="dotted-line" style="width: 400px;">{{ $company_full_address }}</span></p>

<p class="left no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;v zastúpení <span class="dotted-line" style="width: 350px;">{{ $company_contact }}</span> (meno, pozícia)</p>

<p class="left no-margin-top">&nbsp;</p>

<p class="left no-margin-top"><b>Študent:</b></p>

<p class="left no-margin-top indent-1">Meno a priezvisko:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $student_name }}</p>

<p class="left no-margin-top indent-1">Adresa trvalého bydliska:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $student_address }}</p>

<p class="left no-margin-top indent-1">Kontakt študenta FPVaI UKF v Nitre:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $student_contact }}</p>

<p class="left no-margin-top indent-1">Študijný program:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;aplikovaná informatika</p>

<p class="left no-margin-top">&nbsp;</p>

<p class="left no-margin-top">uzatvárajú túto dohodu o odbornej praxi študenta.</p>

<p class="left no-margin-top">&nbsp;</p>

<p class="center no-margin-top"><b>I. Predmet dohody</b></p>

<p class="no-margin-top">&nbsp;</p>

<p class="no-margin-top">Predmetom tejto dohody je vykonanie odbornej praxe študenta v rozsahu 150 hodín, v termíne od <span class="dotted-line" style="width: 150px;">{{ $start_date }}</span> do <span class="dotted-line" style="width: 150px;">{{ $end_date }}</span> bezodplatne.</p>

<p class="no-margin-top">&nbsp;</p>

<p class="center no-margin-top"><b>II. Práva a povinnosti účastníkov dohody</b></p>

<p class="center no-margin-top">&nbsp;</p>

<p class="no-margin-top"><b>1. Fakulta prírodných vied a informatiky Univerzity Konštantína Filozofa v Nitre:</b></p>

<p class="no-margin-top margin-left-18">1.1 Poverí svojho zamestnanca: Mgr. Martin Vozár, PhD. (<a href="mailto:mvozar@ukf.sk">mvozar@ukf.sk</a>) za 1. stupeň, PaedDr. Peter Švec, Ph.D. (<a href="mailto:psvec@ukf.sk">psvec@ukf.sk</a>) za 2. stupeň (ďalej garant odbornej praxe) garanciou odbornej praxe.</p>

<p class="no-margin-top">1.2 Prostredníctvom garanta odbornej praxe:</p>

<p class="no-margin-top margin-left-36">a) poskytne študentovi:</p>

<p class="no-margin-top margin-left-48">- informácie o organizácii praxe, o podmienkach dojednania dohody o odbornej praxi, o obsahovom zameraní odbornej praxe a o požiadavkách na obsahovú náplň správy z odbornej praxe,</p>
<p class="no-margin-top margin-left-48">- návrh dohody o odbornej praxi študenta,</p>

<p class="no-margin-top margin-left-36">b) rozhodne o udelení hodnotenia „ABS" (absolvoval) študentovi na základe dokladu „Výkaz o vykonanej odbornej praxi", vydaného poskytovateľom odbornej praxe a na základe študentom vypracovanej správy o odbornej praxi, ktorej súčasťou je verejná obhajoba výsledkov odbornej praxe,</p>

<p class="no-margin-top margin-left-36">c) spravuje vyplnenú a účastníkmi podpísanú dohodu o odbornej praxi.</p>

<p class="no-margin-top">&nbsp;</p>

<p class="no-margin-top"><b>2. Poskytovateľ odbornej praxe:</b></p>

<p class="no-margin-top margin-left-21">2.1 poverí svojho zamestnanca (tútor - zodpovedný za odbornú prax v organizácii) <span class="dotted-line" style="width: 300px;">{{ $tutor_name }}</span>, ktorý bude dohliadať na dodržiavanie dohody o odbornej praxi, plnenie obsahovej náplne odbornej praxe a bude nápomocný pri získavaní potrebných údajov pre vypracovanie správy z odbornej praxe,</p>

<p class="no-margin-top margin-left-21">2.2 na začiatku praxe vykoná poučenie o bezpečnosti a ochrane zdravia pri práci v zmysle platných predpisov,</p>

<p class="no-margin-top margin-left-21">2.3 vzniknuté organizačné problémy súvisiace s plnením dohody rieši spolu s garantom odbornej praxe,</p>

<p class="no-margin-top margin-left-21">2.4 po ukončení odbornej praxe vydá študentovi „Výkaz o vykonanej odbornej praxi", ktorý obsahuje popis vykonávaných činností a stručné hodnotenie študenta a je jedným z predpokladov úspešného ukončenia predmetu Odborná prax,</p>

<p class="no-margin-top margin-left-21">2.5 umožní garantovi odbornej praxe a garantovi študijného predmetu kontrolu študentom plnených úloh.</p>

<p class="no-margin-top">&nbsp;</p>

<p class="no-margin-top"><b>3. Študent FPVaI UKF v Nitre:</b></p>

<p class="no-margin-top margin-left-18">3.1 osobne zabezpečí podpísanie tejto dohody o odbornej praxi študenta,</p>
<p class="no-margin-top margin-left-18">3.2 zodpovedne vykonáva činnosti pridelené tútorom odbornej praxe,</p>
<p class="no-margin-top margin-left-18">3.3 zabezpečí doručenie dokladu „Výkaz o vykonanej odbornej praxi" najneskôr v termínoch predpísaných garantom pre daný semester,</p>
<p class="no-margin-top margin-left-18">3.4 okamžite, bez zbytočného odkladu informuje garanta odbornej praxe o problémoch, ktoré bránia plneniu odbornej praxe.</p>

<p class="no-margin-top">&nbsp;</p>

<p class="center no-margin-top"><b>III. Všeobecné a záverečné ustanovenia</b></p>

<p class="no-margin-top">&nbsp;</p>

<p class="no-margin-top margin-left-18">1. Dohoda sa uzatvára na dobu určitú. Dohoda nadobúda platnosť a účinnosť dňom podpísania obidvomi zmluvnými stranami. Obsah dohody sa môže meniť písomne len po súhlase jej zmluvných strán.</p>

<p class="no-margin-top margin-left-18">2. Dohoda sa uzatvára v 3 vyhotoveniach, každá zmluvná strana obdrží jedno vyhotovenie dohody.</p>

<div class="signature-block">
    <p class="no-margin-top">V Nitre, dňa <span class="dotted-line" style="width: 100px;">{{ $date_nitra }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;V <span class="dotted-line" style="width: 80px;">{{ $city_company }}</span>, dňa <span class="dotted-line" style="width: 100px;">{{ $date_company }}</span></p>

    <p class="no-margin-top">&nbsp;</p>
    <p class="no-margin-top">&nbsp;</p>
    <p class="no-margin-top">&nbsp;</p>

    <table width="100%">
        <tr>
            <td width="45%" style="vertical-align: top;">
                <p class="no-margin-top"><span class="signature-line" style="width: 300px;"></span></p>
                <p class="no-margin-top">prof. RNDr. František Petrovič, PhD.</p>
                <p class="no-margin-top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dekan FPVaI UKF v Nitre</p>
            </td>
            <td width="10%">&nbsp;</td>
            <td width="45%" style="vertical-align: top;">
                <p class="no-margin-top"><span class="signature-line" style="width: 300px;"></span></p>
                <p class="no-margin-top">{{ $company_contact }}</p>
                <p class="no-margin-top">štatutárny zástupca pracoviska odb. praxe</p>
            </td>
        </tr>
    </table>

    <p class="no-margin-top">&nbsp;</p>
    <p class="no-margin-top">&nbsp;</p>
    <p class="no-margin-top">&nbsp;</p>

    <table width="100%">
        <tr>
            <td width="100%" style="text-align: right;">
                <p class="no-margin-top"><span class="signature-line" style="width: 300px;"></span></p>
                <p class="no-margin-top">{{ $student_name }}</p>
            </td>
        </tr>
    </table>
</div>

</body>
</html>