<?php //odmowa wydania upoważnienia ?>
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

        <div style ="margin-top:30pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
            <p >
                Szanowni Państwo,
            </p>
            <p style="text-indent:30px; margin-top: 30pt;">
                W nawiązaniu do pisma z dnia {{ $inputs['document_date'] }} {{ checkIfEmpty('1', $ideaA) }}, zgodnie z Ogólnymi Warunkami Umowy leasingu/pożyczki nie wyraża zgody na kosztorysowe rozliczenie szkody częściowej.
            </p>
            <p style=" margin-top: 20px; ">
                W wyjątkowych przypadkach zgoda na kosztorysowe rozliczenie szkody może być udzielona pod warunkiem:<br/>
                - sporządzenia opinii niezależnego rzeczoznawcy zleconego przez {{ checkIfEmpty('1', $ideaA) }}, która potwierdzi zgodność naprawy z oceną techniczną ubezpieczyciela.<br/>
                Po wykonaniu pozytywnej opinii technicznej rzeczoznawcy {{ checkIfEmpty('1', $ideaA) }} wystawi upoważnienie do wypłaty odszkodowania na podstawie kosztorysu.<br/>
                Kosztem opinii technicznej w kwocie <strong>390,00</strong> PLN netto zostanie obciążony Leasingobiorca
                pojazdu.
            </p>
            <p style=" margin-top: 20px;">
                Prosimy o pisemną akceptację w/w warunków w ciągu 7 dni roboczych od dnia otrzymania niniejszego pisma wraz z kopią badania technicznego wykonanego po naprawie pojazdu.
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
