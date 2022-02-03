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


<div id="body">
    <div class="page"  id="content">
        <div style="font-size: 7pt;">
            @include('injuries.docs_templates.modules.place')
            <?php $vehicle = $injury->vehicle()->first();?>
            @include('injuries.docs_templates.modules.insurance_company')
        </div>

        <div style="margin-top: 40pt; font-size: 9pt; font-weight: bold">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>nr umowy leasingu/ pożyczki {{ $vehicle->nr_contract }}</p>
        </div>

        <div style ="margin-top:10pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
            <p style="text-indent:30px;">
                {{ checkIfEmpty('1', $ideaA) }} z siedzibą we {{ checkIfEmpty('13', $ideaA) }} przy {{ checkIfEmpty('2', $ideaA) }} będąc właścicielem w/w pojazdu zwraca się z prośbą o przekazanie wypłaty odszkodowania na konto:
            </p>
            <p style="margin-top: 20px; font-weight: bold;">
                {{ checkIfEmpty('1', $ideaA) }}<br/>
                {{ checkIfEmpty('14', $ideaA) }}<br/>
                @if($owner->id == 1 && $vehicle->register_as == 0)
                    {{ checkIfEmpty('16', $ideaA) }}
                @else
                    {{ checkIfEmpty('10', $ideaA) }}
                @endif
            </p>
            <p style=" margin-top: 20px;">
                z zaznaczeniem numeru sprawy {{ $vehicle->nr_contract }}
            </p>
            <p style="margin-top: 20px;">
                @if($injury->settlement_cost_estimate == 1)
                    Wyrażamy zgodę na kosztorysowe rozliczenie szkody.
                @else
                    Nie wyrażamy zgody na kosztorysowe rozliczenie szkody.
                @endif
            </p>
            <p style="margin-top: 20px;">
                Upoważnienie dotyczy szkody częściowej.
            </p>
            <p style=" margin-top: 20px; font-weight: bold;">
                Decyzję o wypłacie odszkodowania prosimy przesłać na adres:<br/>
                {{ checkIfEmpty('1', $ideaA) }} <br/>
                Dział Likwidacji Szkód<br/>
                {{ checkIfEmpty('2', $ideaA) }} {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}<br/>
            </p>
            <p style=" margin-top: 60px; ">
                W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. {{ checkIfEmpty('5', $ideaA) }} lub e-mail: {{ checkIfEmpty('4', $ideaA) }}
            </p>
        </div>
        <table style="width: 100%; font-size: 8pt; font-weight:normal; margin-top:25px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
