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
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <p class=" text-right">
            {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br />
            {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}<br />
            {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br />
        </p><br /><br />

        <p class="block">
            Dział Księgowości {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br />
            Dział Ubezpieczeń {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}
        </p>

        <p class="block">
            <b>
                Dotyczy szkody z dnia {{substr($injury->date_event, 0, 10)}} nr szkody {{ $injury->injury_nr }}<br />
                pojazd: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}<br />
                nr rej. {{ $injury->vehicle->registration }}
            </b>
        </p>

        <p class="block">
            Leasingobiorca: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ ($injury->client) ? $injury->client->name : '---' }}<br />
            Umowa Leasingu: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $injury->vehicle->nr_contract }}<br />
            Data zdarzenia: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $injury->date_event }}
        </p>

        <p class="block">Informujemy, że firma
                Centrum Asysty Szkodowej

            Sp. z o. o została poinformowana przez Ubezpieczyciela o zakwalifikowaniu szkody jako całkowitej. Prosimy o zablokowanie umowy leasingu.</p>

        <p class="block">
          @if($injury->wreck)
            Wartość pojazdu bezpośrednio przed zdarzeniem : {{ $injury->wreck->value_undamaged }} {{ ($injury->wreck->value_undamaged_net_gross == 0) ? 'netto/brutto' : Config::get('definition.net_gross')[$injury->wreck->value_undamaged_net_gross] }} {{ Config::get('definition.currencies')[$injury->wreck->value_undamaged_currency] }}<br />
            Wartość pojazdu po zdarzeniu : {{ $injury->wreck->value_repurchase }} {{ ($injury->wreck->value_repurchase_net_gross == 0) ? 'netto/brutto' : Config::get('definition.net_gross')[$injury->wreck->value_repurchase_net_gross] }} {{ Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency] }}<br />
            Wysokość odszkodowania : {{ $injury->wreck->value_compensation }} {{ ($injury->wreck->value_compensation_net_gross == 0) ? 'netto/brutto' : Config::get('definition.net_gross')[$injury->wreck->value_compensation_net_gross] }} {{ Config::get('definition.currencies')[$injury->wreck->value_compensation_currency] }}<br />
          @endif
        </p>

        <table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
            @include('injuries.docs_templates.modules.regards')
        </table>

    </div>
</div>

</body>
</html>
