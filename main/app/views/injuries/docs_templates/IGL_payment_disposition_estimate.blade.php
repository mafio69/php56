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
        /* font-family: "Times New Roman", "Times", serif; */
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

            <p style="font-size: 11pt">
                {{$owner->name}} z siedzibą we {{$owner->city}} przy {{$owner->street}} będąc właścicielem w/w
                pojazdu zwraca się z prośbą o przekazanie wypłaty odszkodowania na konto:

            <div class="block text-center"><b style="font-size: 11pt">
                    {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br/>
                    {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
                    , {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br/>
                    Nr konta :
                    @if( ($owner->id == 1 && $vehicle->register_as == 1) || $owner->id != 1)
                        {{ (isset($ideaA[10])) ? $ideaA[10] : '...............................' }}
                    @else
                        {{ (isset($ideaA[16])) ? $ideaA[16] : '...............................' }}
                    @endif
                </b>
                <br>
            </div>


            <p style="font-size: 11pt">
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