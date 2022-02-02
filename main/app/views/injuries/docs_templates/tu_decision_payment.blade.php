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

    <div class="page"  id="content">

        <div style="font-size: 7pt;">

            @include('injuries.docs_templates.modules.place')
            <?php $vehicle = $injury->vehicle()->first();?>
            @include('injuries.docs_templates.modules.client')

        </div>

        <div style="margin-top: 50pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu/pożyczki: {{ $vehicle->nr_contract }}</p>
        </div>

        <div style ="margin-top:30pt; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p >
                Szanowni Państwo,
            </p>
            <p style="text-indent:30px; margin-top: 30pt;">
                {{ checkIfEmpty('1', $ideaA) }} w załączeniu przesyła decyzję dotyczącą wypłaty odszkodowania otrzymaną z Towarzystwa Ubezpieczeniowego likwidującego w/w szkodę.
            </p>
            <p style="margin-top: 20px;">
                Przypominamy, iż zgodnie z Art. 81 Ust. 11 pkt 5 ustawy Prawo o ruchu drogowym z dnia 20 czerwca 1997 roku, istnieje obowiązek wykonania dodatkowego badania technicznego pojazdu, w którym została dokonana naprawa wynikająca ze zdarzenia powodującego odpowiedzialność zakładu ubezpieczeń z tytułu zawartej umowy ubezpieczenia. Obowiązek wykonania badania dotyczy sytuacji, gdy naprawa wykonywana była w zakresie elementów układu nośnego, hamulcowego lub kierowniczego mających wpływ na bezpieczeństwo ruchu drogowego.<br/>
                Zgodnie z Art. 17 Ustawy z dnia 22 maja 2003 roku o działalności ubezpieczeniowej istnieje obowiązek poinformowania Ubezpieczyciela o wykonaniu dodatkowego badania technicznego.
            </p>

            <p style=" margin-top: 60px; ">
                W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. {{ checkIfEmpty('5', $ideaA) }} lub na adres {{ checkIfEmpty('4', $ideaA) }}
            </p>
        </div>
        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:25px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
