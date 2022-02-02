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
    * {
        font-family: "Times New Roman", "Times", serif;
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 12pt;
        line-height: 1.5;
        margin-top: 0.5cm
    }

    b {
        font-size: 12pt
    }
    td {
        line-height: 1.5
    }
</style>

<body style="{{--font-family:'Times';--}}margin: 0.5cm">

<div class="right" style="margin-bottom: 1.5cm; font-size: 11pt; line-height: 1.5">Wrocław, {{date("d.m.Y")}}<br><br></div>

<div class="center" style="line-height:1.5"><b style="font-size: 11pt; border-bottom: 1px solid black;">Dotyczy pojazdu 
    {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
         nr rej: {{$vehicle->registration}}<br>umowa nr
        {{$vehicle->nr_contract}} typ szkody {{$injury->injuries_type()->first()->name}}
        szkoda nr
        {{$injury->injury_nr}}</b><br><br><br>
</div>
<div class="left">
    <p>
        Szanowni Państwo, <br><br>
    <p>
    <p>W związku z w/w szkodą komunikacyjną, zakwalifikowaną jako całkowita, {{($owner->data()->where
            ('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)
    ->first()->value : '---'}}
        {{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()
->value : '---'}}
        {{($owner->data()->where('parameter_id', 3)->first() ) ? $owner->data()->where('parameter_id', 3)->first()
        ->value : '---'}}
        {{($owner->data()->where('parameter_id', 13)->first() ) ? $owner->data()->where('parameter_id', 13)->first()
->value : '---'}}, będąc właścicielem przedmiotowego pojazdu, zwraca się z prośbą o <strong>dostarczenie do
            naszej
            dyspozycji</strong> pozostałości przedmiotu leasingu zgodnie z Ogólnych Warunków Umowy Leasingu (OWUL).</p>
    <p>
    Wraz z pojazdem, prosimy o dostarczenie wszystkich kompletów kluczy oraz wszelkiej dokumentacji pojazdu.<br>
    Termin dostawy prosimy ustalić z naszymi kooperantami( numery telefonów poniżej).<br><br>
    </p>
    <span style="border-bottom: 1px solid black;">
        Pojazd prosimy dostarczyć w terminie do {{$inputs['delivery_deadline']}} r.<br>
    </span>
    <p>
        <b>Wszystkie pojazdy prosimy dostarczać na adres:</b></p>
    <p>
        ul. Fabryczna 24, 55-080 Pietrzykowice,<br>tel. 667-892-806, 71 77-88-950 (pojazdy osobowe)<br> tel.603-197-038, 601-150-428 (pojazdy ciężarowe) <br>
        lub <br>FAMAT Słomczyn 70, 05-600 Grójec, tel.: 516-579-714 lub 882-55-33-14
    </p>
</div>
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