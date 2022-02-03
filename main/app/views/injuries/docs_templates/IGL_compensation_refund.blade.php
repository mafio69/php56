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
        font-family: Lato, sans-serif;
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }
    p { 
        font-size: 11pt;
        line-height: 1.5;
    }
</style>

<body>
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        <p style="text-align: right">
            ....................................................................................<br>
            [Miejscowość, data]
        </p>
        @include('injuries.docs_templates.modules.injury_info_2')
        <br>
        <?php 
        $owner_template_data = [
            'style' => 'text-align:right;'
        ]
        ?>
        @include('injuries.docs_templates.modules.owner', $owner_template_data)
       
        <br><br>

        <p class="text-left">
            Dyspozycja przekazania środków pieniężnych<br><br>
            Proszę o przekazanie kwoty ………………… otrzymanej przez Idea Getin leasing SA z zakładu ubezpieczeń {{ $injury->insuranceCompany ? $injury->insuranceCompany->name : ''}} tytułem odszkodowania za uszkodzenia pojazdu {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} o numerze rejestracyjnym {{$injury->vehicle->registration}}. firmie, która dokonała naprawy pojazdu:<br><br>
            Pełna nazwa: …………………..<br><br>
            Adres: …………………………<br><br>
            NIP ……………………………<br><br>
            Zgodnie z załączoną FV nr………………………….<br><br>


        </p>
        <br><br><br>
        <p style="text-align: right; margin-right: 1cm">
            ………………………………..………………………………………..<br>
            Własnoręczny podpis osoby uprawnionej i pieczątka korzystającego
            </p>
 
    </div>
</div>

</body>
</html>
