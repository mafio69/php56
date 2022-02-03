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
<style>
      @import url('https://fonts.googleapis.com/css?family=Fira+Sans:300i,400,700&display=swap');
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: 'Fira Sans', sans-serif; 
        font-size: 11pt;rgd
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

        <div>


            @include('injuries.docs_templates.modules.place')

            @include('injuries.docs_templates.modules.injury_info')

            @include('injuries.docs_templates.modules.insurance_company')

            <div class="center" style="margin-bottom: 1cm;"><b style="font-size: 12pt">Upoważnienie wypłaty odszkodowania
                {{isset($inputs['description'])?'<br>'.$inputs['description']:'' }}
            </b></div>
                


            <p>
                {{$owner->name}} z siedzibą we {{$owner->city}} przy {{$owner->street}} będąc właścicielem w/w
                pojazdu zwraca się z prośbą o przekazanie wypłaty odszkodowania na konto:

            <div class="block text-center" style="margin-bottom: 1.0cm; margin-top: 1.0cm"><b>
                    {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br/>
                    {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
                    , {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br/>
                    Nr konta :
                    @if( ($owner->id == 1 && $vehicle->register_as == 1) || $owner->id != 1)
                        {{ (isset($ideaA[10])) ? $ideaA[10] : '...............................' }}
                    @else
                        {{ (isset($ideaA[16])) ? $ideaA[16] : '...............................' }}
                    @endif
                </b></div>


            <p>
                <br>Faktury powinny zostać wystawione na Korzystającego.<br><br>

                Upoważnienie dotyczy szkody częściowej.<br><br></p>

            <i style="font-size: 10pt;font-weight: lighter">Jednocześnie
                informujemy, że w
                przypadku
                zakwalifikowania
                szkody
                jako
                całkowitej,
                uprawnionym do
                odbioru
                odszkodowania pozostaje wyłącznie {{$owner->name.', '.$owner->street.', '.$owner->post.' '
                .$owner->city}}.</i><br><br>


            <p>Decyzję o wypłacie odszkodowania prosimy o przesłanie na adres: <a href="mailto:szkody@ideagetin
                .pl" style="border-bottom: 1px solid black;">szkody@ideagetin.pl</a><br><br></p>


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
        </div>
    </div>
</div>

</body>
</html>