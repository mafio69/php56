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
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br /><br />

        <p class=" text-right">
            {{ ($injury->client) ? $injury->client->name : '....................' }}<br />
            {{ ($injury->client) ? $injury->client->correspond_street : '....................' }}<br />
            {{ ($injury->client) ? $injury->client->correspond_post : '' }} {{ ($injury->client) ? $injury->client->correspond_city : '.............................................' }}
        </p><br /><br />

        <h4 class="text-left">   Dotyczy: szkoda komunikacyjna numer {{ $injury->injury_nr }} z dnia  {{ $injury->date_event }}<br />
            pojazd {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}; nr rejestracyjny {{ $injury->vehicle->registration }};  nr umowy leasingu/pożyczki: {{ $injury->vehicle->nr_contract }}
        </h4>

        <p class="block text-justify">Szanowni Państwo,<br /><br />

            Z związku z w/w szkodą komunikacyjną zwracamy się z uprzejmą prośbą o zapoznanie się z treścią załączonego pisma i udzielenie wszelkich informacji mających na celu zakończenie procesu likwidacji w/w szkody. </p>

        <p class="block">Dokumenty proszę kierować bezpośrednio do Towarzystwa Ubezpieczeniowego likwidującego szkodę. </p>

        <p class="block">Sprawę prosimy potraktować jako pilną. </p><br /><br />

        <p class="block">
            W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel 801 199 199 w. 5  lub <br />e-mail: szkody@ideagetin.pl
        </p>

        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px; ">
            @include('injuries.docs_templates.modules.regards_small')
        </table>

    </div>
</div>

</body>
</html>
