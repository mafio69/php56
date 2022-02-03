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

    @import url('https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700&display=swap');
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: Lato, sans-serif;
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        line-height: 1.5;
        margin-top: 5px;
    }
    b, div {
        font-size: 11pt;
        line-height: 1.5;
    }
</style>


<body style="{{--font-family:'Times';--}}">

<div class="right">Wrocław, {{date("d.m.Y")}}<br><br><br><br></div>
<div class="right" style="margin-right: 1.0cm">
    {{$injury->insuranceCompany->name}}<br>
    {{$injury->insuranceCompany->street}}<br>
    {{$injury->insuranceCompany->post.' '.$injury->insuranceCompany->city}}<br><br><br><br><br></div>

<div class="left" style="margin-bottom: 1.5cm; line-height: 2.0; font-size: 11pt"><strong style="font-size: 11pt">Dotyczy: szkoda numer {{$injury->injury_nr}} z
        dnia
        {{$injury->date_event}} <br>

        Przedmiot: {{checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
         nr rej: {{$vehicle->registration}} .nr umowy leasingu
        {{$vehicle->nr_contract}}

    </strong></div>
<div class="center" style="margin-bottom: 0.5cm;"><b style="font-size: 14pt;">Dyspozycja wypłaty odszkodowania</b></div>

<p style="font-size: 11pt"> W związku z kradzieżą na w/w pojazdu,   {{$owner->name}} z siedzibą we {{$owner->city}} przy {{$owner->street}} będąc właścicielem w/w pojazdu zwraca się z prośbą o przekazanie wypłaty odszkodowania na
    konto:<br><br>

<p class="block text-center"><b  style="font-size: 11pt">
        {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br/>
        {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
        , {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br/>
        Nr konta :
        @if( ($owner->id == 1 && $vehicle->register_as == 1) || $owner->id != 1)
            {{ (isset($ideaA[10])) ? $ideaA[10] : '...............................' }}
        @else
            {{ (isset($ideaA[16])) ? $ideaA[16] : '...............................' }}
        @endif
    </b></p>

<br>
<p class="left" style="font-size: 11pt">
    Decyzję o wypłacie odszkodowania prosimy o przesłanie na adres: <span style="border-bottom: 1px solid black;;
        font-size: 11pt;"><a>szkody@ideagetin.pl</a></span><br>
    W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny 801 199 199 w. 5 lub mailowy.<br><br></p>


<table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
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