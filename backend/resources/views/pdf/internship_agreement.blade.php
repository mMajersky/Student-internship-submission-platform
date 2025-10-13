<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            margin: 25px; 
            line-height: 1.3;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
        }
        .header-top {
            font-size: 9px;
            margin-bottom: 8px;
        }
        h1 { 
            text-align: center; 
            font-size: 13px; 
            margin-bottom: 15px;
            text-decoration: underline;
        }
        .section { 
            margin-bottom: 12px; 
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 6px;
            text-decoration: underline;
        }
        .field { 
            margin-bottom: 3px; 
        }
        .signatures { 
            margin-top: 30px; 
            display: flex; 
            justify-content: space-between;
        }
        .signature { 
            text-align: center; 
            width: 45%; 
        }
        .signature-line {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .clause {
            margin-bottom: 6px;
        }
        .subclause {
            margin-left: 12px;
            margin-bottom: 4px;
        }
        footer { 
            position: fixed; 
            bottom: 5px; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 8px; 
        }
        .page-break {
            page-break-before: always;
        }
        .underline-field {
            display: inline-block;
            min-width: 250px;
            border-bottom: 1px solid black;
            margin-left: 5px;
        }
        .empty-line {
            height: 15px;
        }
        .text-center {
            text-align: center;
        }
        .signature-block {
            margin-top: 40px;
        }
        .signature-item {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Stránka 1 -->
    <div class="header">
        <div class="header-top">Platnosť tlačiva od 1.10.2022 (aplikovaná informatika)</div>
        <h1>DOHODA O ODBORNEJ PRAXI ŠTUDENTA</h1>
        <div>uzatvorená v zmysle § 51 Občianskeho zákonníka a Zákona č. 131/2002 Z.z. o vysokých školách</div>
    </div>

    <div class="section">
        <div class="field"><strong>Univerzita Konštantína Filozofa v Nitre</strong></div>
        <div class="field">Fakulta prírodných vied a informatiky</div>
        <div class="field">Trieda A. Hlinku 1, 949 01 Nitra</div>
        <div class="field">v zastúpení prof. RNDr. František Petrovič, PhD. - dekan fakulty</div>
        <div class="field">e-mail: fpetrovic@ukf.sk, tel. 037/6408 555</div>
    </div>

    <div class="empty-line"></div>

    <div class="section">
        <div class="field"><strong>Poskytovateľ odbornej praxe (organizácia, resp. inštitúcia)</strong></div>
        <div class="field">Plný názov a adresa :<span class="underline-field">{{ $company->name ?? '' }}, {{ $company->address ?? '' }}</span></div>
        <div class="field">v zastúpení <span class="underline-field">{{ $company->contact_person ?? '' }}</span> (meno, pozícia)</div>
    </div>

    <div class="empty-line"></div>

    <div class="section">
        <div class="field"><strong>Študent:</strong></div>
        <div class="field">Meno a priezvisko: <span class="underline-field">{{ $student->full_name ?? '' }}</span></div>
        <div class="field">Adresa trvalého bydliska: <span class="underline-field">{{ $student->address ?? '' }}</span></div>
        <div class="field">Kontakt študenta FPVal UKF v Nitre: <span class="underline-field">{{ $student->email ?? '' }}, {{ $student->phone ?? '' }}</span></div>
        <div class="field">Študijný program: <span class="underline-field">{{ $student->study_program ?? '' }}</span></div>
    </div>

    <div class="empty-line"></div>

    <div class="section">
        <div>uzatvárajú túto dohodu o odbornej praxi študenta.</div>
    </div>

    <div class="empty-line"></div>

    <div class="section">
        <div class="section-title">I. Predmet dohody</div>
        <div class="clause">
            Predmetom tejto dohody je vykonanie odbornej praxe študenta v rozsahu 150 hodín, v termíne od
            <span class="underline-field">{{ $internship->start_date ?? '' }}</span> do <span class="underline-field">{{ $internship->end_date ?? '' }}</span> bezodplatne.
        </div>
    </div>

    <div class="empty-line"></div>

    <div class="section">
        <div class="section-title">II. Práva a povinnosti účastníkov dohody</div>
        
        <div class="clause"><strong>1. Fakulta prírodných vied a informatiky Univerzity Konštantína Filozofa v Nitre:</strong></div>
        <div class="subclause">1.1 Poverí svojho zamestnanca: Mgr. Martin Vozár, PhD. (mvozar@ukf.sk) za 1. stupeň,</div>
        <div class="subclause" style="margin-left: 25px;">PaedDr. Peter Švec, Ph.D. (psvec@ukf.sk) za 2 . stupeň</div>
        <div class="subclause" style="margin-left: 25px;">(ďalej garant odbornej praxe) garanciou odbornej praxe.</div>
        
        <div class="subclause">1.2 Prostredníctvom garanta odbornej praxe:</div>
        <div class="subclause" style="margin-left: 25px;">a) poskytne študentovi:</div>
        <div class="subclause" style="margin-left: 35px;">- informácie o organizácii praxe, o podmienkach dojednania dohody o odbornej</div>
        <div class="subclause" style="margin-left: 35px;">praxi, o obsahovom zameraní odbornej praxe a o požiadavkách na obsahovú náplň</div>
        <div class="subclause" style="margin-left: 35px;">správy z odbornej praxe,</div>
        <div class="subclause" style="margin-left: 35px;">- návrh dohody o odbornej praxi študenta,</div>
        
        <div class="subclause" style="margin-left: 25px;">b) rozhodne o udelení hodnotenia „ABS" (absolvoval) študentovi na základe dokladu</div>
        <div class="subclause" style="margin-left: 35px;">„Výkaz o vykonanej odbornej praxi", vydaného poskytovateľom odbornej praxe a na</div>
        <div class="subclause" style="margin-left: 35px;">základe študentom vypracovanej správy o odbornej praxi, ktorej súčasťou je verejná</div>
        <div class="subclause" style="margin-left: 35px;">obhajoba výsledkov odbornej praxe,</div>
        
        <div class="subclause" style="margin-left: 25px;">c) spravuje vyplnenú a účastníkmi podpísanú dohodu o odbornej praxi.</div>

        <div class="clause"><strong>2. Poskytovateľ odbornej praxe:</strong></div>
        <div class="subclause">2.1 poverí svojho zamestnanca (tútor - zodpovedný za odbornú prax v organizácii)</div>
    </div>

    <footer>
        Strana 1
    </footer>

    <!-- Stránka 2 -->
    <div class="page-break">
    <div class="section">
        <div class="subclause" style="margin-left: 35px;">dohody o odbornej praxi, plnenie obsahovej náplne odbornej praxe a bude nápomocný</div>
        <div class="subclause" style="margin-left: 35px;">pri získavaní potrebných údajov pre vypracovanie správy z odbornej praxe,</div>
        
        <div class="subclause">2.2 na začiatku praxe vykoná poučenie o bezpečnosti a ochrane zdravia pri práci v zmysle</div>
        <div class="subclause" style="margin-left: 25px;">platných predpisov,</div>
        
        <div class="subclause">2.3 vzniknuté organizačné problémy súvisiace s plnením dohody rieši spolu s garantom</div>
        <div class="subclause" style="margin-left: 25px;">odbornej praxe,</div>
        
        <div class="subclause">2.4 po ukončení odbornej praxe vydá študentovi „Výkaz o vykonanej odbornej praxi", ktorý</div>
        <div class="subclause" style="margin-left: 25px;">obsahuje popis vykonávaných činností a stručné hodnotenie študenta a je jedným</div>
        <div class="subclause" style="margin-left: 25px;">z predpokladov úspešného ukončenia predmetu Odborná prax,</div>
        
        <div class="subclause">2.5 umožní garantovi odbornej praxe a garantovi študijného predmetu kontrolu študentom</div>
        <div class="subclause" style="margin-left: 25px;">plnených úloh.</div>

        <div class="clause"><strong>3. Študent FPVal UKF v Nitre:</strong></div>
        <div class="subclause">3.1 osobne zabezpečí podpísanie tejto dohody o odbornej praxi študenta,</div>
        <div class="subclause">3.2 zodpovedne vykonáva činnosti pridelené tútorom odbornej praxe,</div>
        <div class="subclause">3.3 zabezpečí doručenie dokladu „Výkaz o vykonanej odbornej praxi" najneskôr v termínoch</div>
        <div class="subclause" style="margin-left: 25px;">predpísaných garantom pre daný semester,</div>
        <div class="subclause">3.4 okamžite, bez zbytočného odkladu informuje garanta odbornej praxe o problémoch, ktoré</div>
        <div class="subclause" style="margin-left: 25px;">bránia plneniu odbornej praxe.</div>
    </div>

    <div class="section">
        <div class="section-title">III. Všeobecné a záverečné ustanovenia</div>
        <div class="clause">1. Dohoda sa uzatvára na dobu určitú. Dohoda nadobúda platnosť a účinnosť dňom</div>
        <div class="subclause">podpísania obidvomi zmluvnými stranami. Obsah dohody sa môže meniť písomne len po</div>
        <div class="subclause">súhlase jej zmluvných strán.</div>
        
        <div class="clause">2. Dohoda sa uzatvára v 3 vyhotoveniach, každá zmluvná strana obdrží jedno vyhotovenie</div>
        <div class="subclause">dohody.</div>
    </div>

    <div class="signatures">
        <div class="signature">
            V Nitre, dňa........................<br>
            <div class="signature-line">_________________________</div>
            prof. RNDr. František Petrovič, PhD.<br>
            dekan FPVal UKF v Nitre
        </div>
        <div class="signature">
            V ......, dňa........................<br>
            <div class="signature-line">_________________________</div>
            štatutárny zástupca pracoviska odb. praxe
        </div>
    </div>
    
    <div class="text-center" style="margin-top: 20px;">
        <div class="signature-line">_________________________</div>
        {{ $student->full_name ?? '' }}<br>
        meno a priezvisko študenta
    </div>

    <footer>
        Strana 2
    </footer>
    </div>
</body>
</html>