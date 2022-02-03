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
            @include('injuries.docs_templates.modules.branch')

        </div>

        <div style="margin-top: 40pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu/pożyczki: {{ $vehicle->nr_contract }}</p>
        </div>

        <div style ="margin-top:25pt; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p >
                Szanowni Państwo,
            </p>
            <p style="text-indent:30px; margin-top: 30pt;">
                Wyrażamy zgodę na wystawienie faktury VAT za naprawę w/w pojazdu na {{ checkIfEmpty('1', $ideaA) }}. Wartość netto zostanie wypłacona przez zakład ubezpieczeń zgodnie z wystawionym upoważnieniem. Kwota należnego podatku VAT zostanie uregulowana przez {{ checkIfEmpty('1', $ideaA) }}.
            </p>
            <p style="margin-top: 20px;">
                Warunkiem odebrania faktury przez {{ checkIfEmpty('1', $ideaA) }} jest przedłożenie zatwierdzonej przez ubezpieczyciela kalkulacji naprawy pojazdu oraz potwierdzenia wypłaty odszkodowania w kwocie netto z faktury.
            </p>

            <p style="margin-top: 20px;">
                Dane do wystawienia faktury:
            </p>
            <p style=" font-weight: bold; ">
                {{ checkIfEmpty('1', $ideaA) }}<br/>
                {{ checkIfEmpty('2', $ideaA) }}<br/>
                {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}<br/>
                NIP {{ checkIfEmpty('8', $ideaA) }}
            </p>
            <p style=" margin-top: 40px; ">
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
