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
        font-family: 'Times New Roman', Times, serif;
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        line-height: 1.5;
        margin-top: 5px;
    }

    b {
        line-height: 1.5;
        font-size: 11pt;
    }
</style>


<body style="{{--font-family:'Times';--}} margin: 1.0cm">

<div class="right">Wrocław, {{date("d.m.Y")}}</div>
<div class="right" style="margin-top: 0.5cm">
    <span style="font-size: 11pt; line-height:1.5">
        @if(!is_null($injury->insuranceCompany))
        {{$injury->insuranceCompany->name}}<br>
        {{$injury->insuranceCompany->street}}<br>
        {{$injury->insuranceCompany->post.' '.$injury->insuranceCompany->city}}
        @endif
        <br><br><br></span>
</div>

<div style="margin-bottom: 1.5cm;"><b style="font-size: 11pt">
        Dotyczy: szkoda numer {{$injury->injury_nr}} z dnia
        {{$injury->date_event}}<br>
        Przedmiot: {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
         nr rej: {{$vehicle->registration}} .nr umowy leasingu
        {{$vehicle->nr_contract}}
    </b></div>

<div class="center" style="margin-bottom: 0.5cm;"><b style="font-size: 14pt;">Dyspozycja wypłaty odszkodowania</b></div>

<p style="font-size: 11pt"> {{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)->first()->value : '---'}}

    z siedzibą we
    Wrocławiu,
    przy {{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()->value: '---'}}
    będąc właścicielem w/w pojazdu
    zwraca się z prośbą o przekazanie wypłaty odszkodowania na konto:

<p class="block text-center" style="margin-top: 1.0cm"><b>
    {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}
    {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
    {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} <br/>
    {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br/>
    Nr konta :
    @if( ($owner->id == 1 && $vehicle->register_as == 1) || $owner->id != 1)
        {{ (isset($ideaA[10])) ? $ideaA[10] : '...............................' }}
    @else
        {{ (isset($ideaA[16])) ? $ideaA[16] : '...............................' }}
    @endif
</b></p>
<p style="margin-top: 1.0cm; margin-bottom: 1.0cm; font-size: 11pt">Decyzję o wypłacie odszkodowania prosimy o przesłanie na adres: <a style="font-size: 11pt; tborder-bottom: 1px solid black;">szkody@ideagetin.pl</a><br>
    W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny 801 199 199 w. 5 lub mailowy.<br></p>
<table style="width: 100%; font-size: 11pt; font-weight:normal;  ">
    <tbody>
    <tr>
        <td style="width:50%; "></td>
        <td style="text-align:center;">Z poważaniem</td>
    </tr>
    <tr>
        <td style="width:50%; "></td>
        <td style="text-align:center;">
            @include('modules.signatures')
        </td>
    </tr>
    </tbody>

</table>
</body>
</html>