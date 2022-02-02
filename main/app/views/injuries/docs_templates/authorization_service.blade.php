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
<div class="body content body-margin-big" >
    <div class="t-body t-body-size-14">
        <p class=" text-right" style="margin-top: 20px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <p class=" text-right">
            {{$injury->insuranceCompany->name}}<br />
            {{$injury->insuranceCompany->street}}, {{$injury->insuranceCompany->post}} {{$injury->insuranceCompany->city}}
        </p><br />

        <p class="text-left">
            Dotyczy szkody nr. {{ $injury->injury_nr }}<br/>
            z dnia {{ $injury->date_event }}<br/>
            na pojeździe {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}<br/>
            nr rejestracyjny {{ $injury->vehicle->registration }}<br/>
            Umowa nr: {{ $injury->vehicle->nr_contract }}
        </p>

        <h4 class="text-center">Upoważnienie do odbioru odszkodowania<br />{{ $inputs['description'] }}</h4>

        <p class="small-block text-justify">{{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
            upoważnia do odbioru przyznanego odszkodowania firmę: </p>

        <p class="small-block text-center">
            <b>
                {{$branch->company->name}}<br />
                {{$branch->street}}<br />
                {{$branch->code}} {{$branch->city}}<br />
                @if($branch->company->account_nr && $branch->company->account_nr != '') Nr konta :  {{$branch->company->account_nr}} @endif
            </b>
        </p>

        <p class="small-block">Wypłata odszkodowania powinna nastąpić na podstawie faktur Vat za naprawę pojazdu. </p>

        <p class="small-block">Zgoda na wypłatę kwoty bezspornej na konto serwisu.</p>

        <p class="small-block">Upoważnienie dotyczy szkody częściowej.</p>

        <p class="small-block small-font"><i>Jednocześnie informujemy, że w przypadku zakwalifikowania szkody jako całkowitej, uprawnionym
                do odbioru odszkodowania pozostaje wyłącznie {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}, {{ (isset($ideaA[2])) ? $ideaA[2] : '' }}, {{ (isset($ideaA[3])) ? $ideaA[3] : '' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '' }}</i></p>

        <p class="small-block">Decyzję o wypłacie odszkodowania prosimy o przesłanie na adres:<br />
            <a href="mailto:{{ (isset($ideaA[4])) ? $ideaA[4] : '' }}">{{ (isset($ideaA[4])) ? $ideaA[4] : '' }}</a></p>

        <p>oraz do wiadomości serwisu:<br/>
            {{$branch->company->name}}<br />
            {{$branch->street}}<br />
            {{$branch->code}} {{$branch->city}}<br />
            {{$branch->email}}
        </p>

        <p class="text-left">W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny 71 33 44 807 lub
            mailowy.</p><br/>

        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px; ">
            @include('injuries.docs_templates.modules.regards_small')
        </table>
    </div>
</div>

</body>
</html>
