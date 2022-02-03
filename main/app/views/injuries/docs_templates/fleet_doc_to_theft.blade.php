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
            @include('injuries.docs_templates.modules.insurance_company')
        </div>

        <div style="margin-top: 50pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda komunikacyjna numer {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu/pożyczki: {{ $vehicle->nr_contract }}</p>
        </div>


        <div style ="margin-top:30pt; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p >
                W związku z kradzieżą w/w pojazdu {{ checkIfEmpty('1', $ideaA) }} z siedzibą we {{ checkIfEmpty('13', $ideaA) }} przy {{ checkIfEmpty('2', $ideaA) }} przesyła poniższe dokumenty niezbędne do zakończenia procesu likwidacji szkody:
            </p>
            <p style=" margin-top: 80px; ">
                W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. {{ checkIfEmpty('5', $ideaA) }} lub e-mail {{ checkIfEmpty('4', $ideaA) }}
            </p>
        </div>
        <table style="width: 100%; font-size: 8pt; font-weight:normal; margin-top:25px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
