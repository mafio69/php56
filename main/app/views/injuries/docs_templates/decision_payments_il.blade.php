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
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p>
        <br />
        <br />
        <br />
        <br />
        <br />

        <p class=" block text-left"><b>
                Dotyczy: szkoda komunikacyjna numer {{ $injury->injury_nr }}
                z dnia {{ $injury->date_event }} r.<br/>
                pojazd {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}},
                nr rejestracyjny  {{ $injury->vehicle->registration }}; nr umowy leasingu/pożyczki: {{ $injury->vehicle->nr_contract }}
            </b></p>


        <p class="block text-justify">Szanowni Państwo,<br /><br />
            {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} w załączeniu przesyła decyzję dotyczącą wypłaty odszkodowania otrzymaną z Towarzystwa
            Ubezpieczeniowego likwidującego ww. szkodę.</p>

        <p class="block text-justify">Przypominamy, iż zgodnie z Art. 81 ust. 11 pkt 5 ustawy Prawo o ruchu drogowym z dnia 20 czerwca 1997 roku istnieje obowiązek wykonania dodatkowego badania technicznego pojazdu, w którym została dokonana naprawa wynikająca ze zdarzenia powodującego odpowiedzialność zakładu ubezpieczeń z tytułu zawartej umowy ubezpieczenia. Obowiązek wykonania badania dotyczy sytuacji, gdy naprawa wykonywana była w zakresie elementów układu nośnego, hamulcowego lub kierowniczego mających wpływ na bezpieczeństwo ruchu drogowego.
            <br/>
            Zgodnie z Art. 17 ustawy z dnia 22 maja 2003 roku o działalności ubezpieczeniowej istnieje obowiązek poinformowania Ubezpieczyciela o wykonaniu dodatkowego badania technicznego.</p>

        <p class="block text-left"><b>Jednocześnie prosimy o przesłanie kopii Faktury za naprawę pojazdu w związku z w/w szkodą komunikacyjną na adres:</b><br/>
                <a href="mailto:szkody@idealeasing.pl">szkody@idealeasing.pl</a></p>

        <p class="block"><b>Informujemy, iż rozliczenie kwoty odszkodowania będzie możliwe dopiero po przedłożeniu faktury.</b></p>

        <p class="text-left">W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny 71 33 44 807 lub <a href="mailto:szkody@idealeasing.pl">szkody@idealeasing.pl</a>.</p><br /><br />

        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px; ">
            @include('injuries.docs_templates.modules.regards_small')
        </table>
    </div>
</div>

</body>
</html>
