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
    body, * {
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
<body>
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        <p class="block text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <p class="block text-left">
            Dotyczy szkody nr. {{ $injury->injury_nr }}<br/>
            z dnia {{ $injury->date_event }}<br/>
            na pojeździe {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}, {{ $injury->vehicle->registration }}<br/>
            Umowa nr: {{ $injury->vehicle->nr_contract }}
        </p>
        <p class="block text-right">
            {{$injury->insuranceCompany->name}}<br />
            {{$injury->insuranceCompany->street}}, {{$injury->insuranceCompany->post}} {{$injury->insuranceCompany->city}}
        </p><br />
        <br/><br/>

        <h4 class="text-center">Dyspozycja wypłaty odszkodowania<br />{{ $inputs['description'] }}</h4>


        <p class="block text-justify">{{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }} będąc właścicielem w/w pojazdu zwraca się z prośbą o przekazanie wypłaty odszkodowania na konto:   </p>

        <p class="block text-center"><b>
            {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br />
            {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}, {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br />
            Nr konta :
                @if( ($owner->id == 1 && $vehicle->register_as == 1) || $owner->id != 1)
                    {{ (isset($ideaA[10])) ? $ideaA[10] : '...............................' }}
                @else
                    {{ (isset($ideaA[16])) ? $ideaA[16] : '...............................' }}
                @endif
        </b></p>

        <p class="small-block">Decyzję o wypłacie odszkodowania prosimy o przesłanie na adres:<br />
            <a href="mailto:szkody@ideagetin.pl">szkody@ideagetin.pl</a></p>

        <p class="block text-left">W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny 801 199 199 w. 5  lub mailowy.</p><br /><br /><br />

        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>