<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
</head>

<style>
        
    body {
        font-family: "Times New Roman", "Times", serif;
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        /* font-family: Lato, sans-serif; */
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 11pt;
        line-height: 1.1;
        margin-top: 5px;
    }
    td {
        text-align: left;
        font-size: 11pt;
        line-height: 1.5;
        padding: 5px;
    }
</style>

<body style="{{--font-family:'Times';--}} margin: 1.0cm">

<p class="right" style="line-height: 2.0;">Wrocław, {{date("d.m.Y")}}<br><br><br>
    <br>{{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)->first()->value : '---'}}
    <br>{{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()
    ->value : '---'}}<br>
    {{($owner->data()->where('parameter_id', 3)->first() ) ? $owner->data()->where('parameter_id', 3)->first()
    ->value : '---'}}
    {{($owner->data()->where('parameter_id', 13)->first() ) ? $owner->data()->where('parameter_id', 13)->first()
    ->value : '---'}}
    <br>szkody@ideagetin.pl<br><br><br></p>


<p style="margin-top: 1.0cm;line-height: 1.5"><b>
    Dotyczy szkody z dnia {{$injury->date_event}} nr szkody {{$injury->injury_nr}}<br>
    Pojazd 
    {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} <br>
    nr rej. {{$vehicle->registration}}</b>
</p>

<p style="margin-top: 0.5cm; line-height: 1.5">
    Leasingobiorca: {{!is_null($vehicle->client)?$vehicle->client->name:'---'}}<br>
    Umowa Leasingu: {{$vehicle->nr_contract}}<br>
    Data zdarzenia: {{$injury->date_event}}<br><br><br>

    Informujemy, że Firma Centrum Asysty Szkodowej Sp. z o. o została poinformowana przez Ubezpieczyciela o
    zakwalifikowaniu szkody jako całkowitej. Prosimy o zablokowanie umowy leasingu.

    Wartość pojazdu bezpośrednio przed zdarzeniem:<br></p>

<p style="margin-top: 40px;">
<table style=" width: 100%; ">
    <tr>
        <td style="width: 60%;">Wartość pojazdu bezpośrednio przed zdarzeniem:</td>
        <td>
            {{$injury->wreck!=null?$injury->wreck->value_undamaged.' '
    .( Config::get('definition.compensationsNetGross.'.$injury->wreck->value_undamaged_net_gross)):'---'}}
</td>
    </tr>
    <tr>
        <td style="width: 60%;">Wartość pojazdu po zdarzeniu:</td>
        <td >{{$injury->wreck!=null?$injury->wreck->value_repurchase.' '
    .( Config::get('definition.compensationsNetGross.'.$injury->wreck->value_repurchase_net_gross)):'---'}}</td>
    </tr>
    <tr>
        <td style="width: 60%;">Wysokość odszkodowania:</td>
        <td >{{$injury->wreck!=null?$injury->wreck->value_compensation.' '
    .( Config::get('definition.compensationsNetGross.'.$injury->wreck->value_compensation_net_gross)):'---'}}</td>
    </tr>
</table>

</body>
</html>