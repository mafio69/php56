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
        <p class="block"><b>Wystawienie FV pro forma na sprzedaż przedmiotu przez DSU  na zlecenie
                    Centrum Asysty Szkodowej

            </b></p><br />

        <p>Proszę o wystawienie FV proforma </p>

        <p><b>Kwota netto  FV:</b> {{ $inputs['net_value'] }}</p>

        <p><b>Waluta:</b> {{ $inputs['currency'] }}</p>

        <p><b>Informacja na kogo FV:</b>
            @if($inputs['for_whom'] == 2)
                @if($injury->wreck->buyerInfo)
                    {{ $injury->wreck->buyerInfo->name }} - NIP: {{ $injury->wreck->buyerInfo->nip }} - {{ $injury->wreck->buyerInfo->address_street }}, {{ $injury->wreck->buyerInfo->address_code }} {{ $injury->wreck->buyerInfo->address_city }}
                @else
                    {{ $inputs['for_whom_info'] }}
                @endif
            @else
                {{ ($injury->client) ? $injury->client->name : '....................' }},
                {{ ($injury->client) ? 'NIP: '.$injury->client->NIP : '' }},
                {{ ($injury->client) ? $injury->client->registry_street : '' }}
                {{ ($injury->client) ? $injury->client->registry_post : '' }} {{ ($injury->client) ? $injury->client->registry_city : '' }}
            @endif
        </p>

        <p><b>Dane pojazdu:</b></p>

        <p>Samochód marki {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} </p>

        <p>nr rejestracyjny: {{ $injury->vehicle->registration }} </p>

        <p>nr VIN: {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}</p>

        <p>rok produkcji: {{ $injury->vehicle->year_production }}</p>

        <p><b>Uwagi: </b>{{ $inputs['remarks'] }}</p>



    </div>
</div>

</body>
</html>
