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

        <p class="block"><b>Dotyczy: Szkody kradzieżowej pojazdu: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}<br />
            o numerze rej: {{ $injury->vehicle->registration }}
        </b></p>

        <p class="block">
            Leasingobiorca: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ ($injury->client) ? $injury->client->name : '---' }}<br />
            Umowa Leasingu: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $injury->vehicle->nr_contract }}<br />
            Data zdarzenia: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $injury->date_event }}
        </p>

        <p class="block">Informujemy, iż firma
                Centrum Asysty Szkodowej

            Sp. z o. o została powiadomiona przez LB o kradzieży pojazdu. Prosimy o zablokowanie umowy. Umowa aktywna. </p>


        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
