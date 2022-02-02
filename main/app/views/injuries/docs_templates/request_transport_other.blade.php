<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
</head>
<body>
<div id="body">
    <?php $vehicle = $injury->vehicle()->first();?>
    <div class="page"  id="content">

        <div style="font-size: 7pt;">
            @include('injuries.docs_templates.modules.place')
            @include('injuries.docs_templates.modules.client')
        </div>

        <div style="margin-top: 50pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu/pożyczki: {{ $vehicle->nr_contract }}</p>
        </div>


        <div style ="margin-top:30pt; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p>
                Szanowni Państwo,
            </p>
            <p style="text-indent: 30px; margin-top: 20px;">
                W związku z w/w szkodą komunikacyjną zakwalifikowaną jako całkowita {{ checkIfEmpty('1', $ideaA) }} z siedzibą we {{ checkIfEmpty('13', $ideaA) }} przy {{ checkIfEmpty('2', $ideaA) }}, będąc właścicielem przedmiotowego pojazdu, zwraca się z prośbą o <strong>dostarczenie do naszej dyspozycji</strong> pozostałości przedmiotu leasingu zgodnie z § 25 pkt. 3 Ogólnych Warunków Umowy Leasingu. Wraz z pojazdem prosimy o dostarczenie wszystkich kompletów kluczyków oraz wszelkiej dokumentacji pojazdu.
            </p>
            <p style="margin-top: 20px;">
                Termin dostawy prosimy ustalić z naszymi kooperantami.
            </p>
            <p style="margin-top: 20px;">
                Pojazdy o masie całkowitej do 3,5t prosimy dostarczać na adres:<br/>
                Auto-Maxi, ul. Żmigrodzka 185, Wrocław<br/>
                Tel. 71 / 352 99 10 - czynne w godz. 8.00-17.00<br/>
            </p>
            <p style="margin-top: 20px;">
                Pozostałe pojazdy o masie całkowitej powyżej 3,5t prosimy dostarczać na adres: parking,<br/>
                ul. Strzegomska 55, Wrocław (teren Wprieko obok betoniarni Bosta-beton )<br/>
                Tel. 0880 536 332 - czynne w godz. 8.00 -16.00
            </p>
            <p style=" margin-top: 40px; ">
                W sprawie likwidacji szkody prosimy o kontakt pod nr {{ checkIfEmpty('5', $ideaA) }}
            </p>
            <p style=" margin-top: 20px; font-weight: bold;">
                Prosimy o dostarczenie pojazdu do dnia {{ date('Y-m-d', strtotime("+7 days")) }} r.
            </p>
            <p style=" margin-top: 20px; font-weight: bold;">
                Do wiadomości: Departament Sprzedaży Poleasingowej
            </p>
        </div>
        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
