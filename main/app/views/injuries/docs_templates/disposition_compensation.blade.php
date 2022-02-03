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
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <p class="block text-right">
            {{$injury->insuranceCompany->name}}<br />
            {{$injury->insuranceCompany->street}}, {{$injury->insuranceCompany->post}} {{$injury->insuranceCompany->city}}
        </p><br />

        <p class="block text-left bold">Dotyczy: szkoda komunikacyjna nr. {{ $injury->injury_nr }} z dnia {{ $injury->date_event }}<br />
            Pojazd {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}; nr rejestracyjny {{ $injury->vehicle->registration }}<br />
            nr umowy leasingu/pożyczki {{ $injury->vehicle->nr_contract }}
        </p><br /><br />

        <h4 class="text-center">Upoważnienie
            @if(isset($inputs['description']))<br/>{{ $inputs['description'] }}@endif
        </h4>

        <p class="block text-justify">

            {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
            upoważnia do odbioru przyznanego odszkodowania firmę: <br />
         </p>
        <p class="block text-left">
            <b>
            {{ ($injury->client) ? $injury->client->name : '....................' }}<br />
            {{ ($injury->client) ? $injury->client->registry_street : '....................' }} {{ ($injury->client) ? $injury->client->registry_post : '' }}, {{ ($injury->client) ? $injury->client->registry_city : '' }}
            </b>
        </p>

        <p class="small-block  text-justify">
            Wyrażamy zgodę na kosztorysowe rozliczenie szkody. Niniejszym anulujemy poprzednie upoważnienie.
        </p>

        <p class="small-block">Upoważnienie dotyczy szkody częściowej.</p>

        <p class="small-block">Decyzję o wypłacie odszkodowania prosimy przesłać na adres:
            <a href="mailto:szkody@ideagetin.pl">szkody@ideagetin.pl</a></p>

        <p class="small-block">W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. 801 199 199 w. 5
            lub<br/>
            email: <a href="mailto:szkody@ideagetin.pl">szkody@ideagetin.pl</a></p>

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

</body>
</html>
