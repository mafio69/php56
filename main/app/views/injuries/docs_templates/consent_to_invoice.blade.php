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
    <div class="t-body t-body-size-14">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <p class="small-block text-right">
            {{$branch->company->name}}<br/>
            {{$branch->street}}<br/>
            {{$branch->code}} {{$branch->city}}
        </p><br />

        <p class="text-left"><b>
            Dotyczy: szkoda komunikacyjna numer {{ $injury->injury_nr }}
            z dnia {{ $injury->date_event }} r.<br/>
            pojazd {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}, nr rejestracyjny  {{ $injury->vehicle->registration }}<br/>
        </b></p>


        <p class="small-block text-justify">Szanowni Państwo,<br /><br />
            Wyrażamy zgodę na wystawienie faktury VAT za naprawę w/w pojazdu na {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} Wartość netto zostanie wypłacona przez zakład ubezpieczeń zgodnie z wystawionym upoważnieniem. Kwota należnego podatku VAT zostanie uregulowana przez {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}</p>

        <p class="small-block text-justify">Warunkiem odebrania faktury przez {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} jest przedłożenie zatwierdzonej przez ubezpieczyciela kalkulacji naprawy pojazdu oraz potwierdzenia wypłaty odszkodowania w kwocie netto z faktury.</p>

        <p class="small-block text-justify">Informujemy, iż leasingobiorca w/w pojazdu na mocy umowy leasingu zobowiązany jest do pokrycia
            kosztów naprawy:</p>

        <p>
        <ul class="list-none " style="font-size: 0.8em;">
            <li>- w części, za którą nie odpowiada zakład ubezpieczeń</li>
            <li>- w całości, jeżeli zakład ubezpieczeń odmówi wypłaty odszkodowania</li>
        </ul>
        </p>

        <p class=" text-left"><b>
                Dane do wystawienia faktury:<br />
                {{ (isset($ideaA[1])) ? $ideaA[1] : '...........................' }}<br />
                {{ (isset($ideaA[2])) ? $ideaA[2] : '...........................' }}, {{ (isset($ideaA[3])) ? $ideaA[3] : '' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '' }}<br />
                NIP {{ (isset($ideaA[8])) ? $ideaA[8] : '...........................' }}
            </b>
        </p>

        <p class="text-left">
            W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny 801 199 199 w. 5  lub <a href="mailto:szkody@ideagetin.pl">szkody@ideagetin.pl</a>.
        </p>

        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px; ">
            @include('injuries.docs_templates.modules.regards_small')
        </table>
    </div>
</div>

</body>
</html>
