<?php //wniosek o naprawę szkody całkowitej ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
    <link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
    <title></title>
</head>
<style type="text/css">
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        line-height: 1.1;
        margin-top: 5px;
    }

    b {
        font-size: 11pt;
    }
</style>
<body>
<div id="body">
    <div class="page" id="content">

        <div style="font-size: 11pt;">


            @include('injuries.docs_templates.modules.place')


            @include('injuries.docs_templates.modules.insurance_company')

            <p style="margin-top: 40pt; font-weight: bold; font-size: 11pt">
                Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}<br>
                Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull
                ($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }} <br>
                Nr umowy leasingu/ pożyczki {{ $vehicle->nr_contract }}</p>
            </p>

            <div class="center"><b><br>Upoważnienie<br></b></div>

            <p>
                {{$owner->name}} z siedzibą we {{$owner->city}} przy {{$owner->street}} upoważnia do odbioru
                przyznanego odszkodowania firmę:
            </p>
            
            <div class="center">
                <table style="width:100%; font-size:9pt; margin-top:20pt; margin-bottom:20pt; font-weight:normal;" >

                    <tr>
                        <Td></Td>
                        <td style="text-align: center">{{$injury->client->name}}</td>
                    </tr>
                    <tr>
                        <Td></Td>
                        <td style="text-align: center">{{$injury->client->registry_street}}</td>
                    </tr>
                    <tr>
                        <Td></Td>
                        <td style="text-align: center">{{$injury->client->registry_post}} {{$injury->client->registry_city}}</td>
                    </tr>
                
                </table>
            </div>
            <p><br>
                <span style="font-size: 11pt; border-bottom: solid black 1px">Wyrażamy zgodę na kosztorysowe rozliczenie szkody. Niniejszym anulujemy
                    poprzednie
                    upoważnienie jeśli
                    zostało wydane.</span><br><br><br>

                Upoważnienie dotyczy szkody częściowej.<br><br><br><br>


                Decyzję o wypłacie odszkodowania prosimy przesłać na adres:<br>
                <a href="mailto:szkody@ideagetin.pl" style="font-size: 11pt">szkody@ideagetin.pl</a>

            </p>

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

        </div>

    </div>
</div>

</body>
</html>