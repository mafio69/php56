<?php //druk zgłoszenia szkody do serwisu?>
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

    <div class="page"  id="content" style="margin-top: 30px; margin-bottom: 0px;">
        <div>
            <table style="width: 100%; font-size: 9pt; font-weight:normal; border:thin solid black;">
                <tbody>
                <tr >
                    <td style="padding: 8px;">
                        Zgłoszenie szkody poprzez stronę {{ checkIfEmpty('12', $ideaA) }} spowoduje szybsze uzyskanie upoważnienia do wypłaty odszkodowania
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            <table style="width: 100%; font-size: 9pt; font-weight:normal; border:thin solid black;">
                <tbody>
                    <tr class="middle">
                        <td style="width:70%;">SZKODA Z POLISY:<br/><span style="font-size: 6pt;">(prosimy zaznaczyć)</span></td>
                        <td><p class="square"></p></td><td>OC</td>
                        <td><p class="square"></p></td><td>AC</td>
                        <td><p class="square"></p></td><td>NNW</td>
                    </tr>
                </tbody>
            </table>
            <?php $driver = $injury->driver()->first();?>
            <?php $vehicle = $injury->vehicle()->first();?>
            <table style="width: 100%; font-size: 8pt; font-weight:normal;" class="bordered-all" cellpadding="1" cellspacing="0">
                <tbody>
                <tr class="blue">
                    <td style="width:50%;">Użytkownik – pieczęć i adres</td>
                    <td style="width:50%;">Imię i nazwisko osoby do kontaktu</td>
                </tr>
                <tr class="">
                    <td rowspan="3" style="width: 50%;"></td>
                    <td style="width: 50%; height: 40px;">
                        @if($injury->contact_person == 2)
                            {{ $injury->notifier_name }} {{ $injury->notifier_surname }}
                        @elseif($injury->driver_id != '' && !is_null($driver))
                        {{ $driver->name }} {{ $driver->surname }}
                        @endif
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 50%;">Imię i nazwisko kierującego pojazdem</td>
                </tr>
                <tr class="">
                    <td style="width: 50%; height: 40px;">
                        @if($injury->driver_id != '' && !is_null($driver))
                        {{ $driver->name }} {{ $driver->surname }}
                        @endif
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 50%;">Nr umowy leasingu</td>
                    <td style="width: 50%;">Telefon</td>
                </tr>
                <tr class="">
                    <td style="width: 50%; height: 20px;">
                        {{ $vehicle->nr_contract }}
                    </td>
                    <td style="width: 50%; height: 20px;">
                        @if($injury->contact_person == 2)
                            {{ $injury->notifier_phone }}
                        @elseif($injury->driver_id != '' && !is_null($driver))
                            {{ $driver->phone }}
                        @endif
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 50%;">Data szkody</td>
                    <td style="width: 50%;">Miejsce szkody</td>
                </tr>
                <tr class="">
                    <td style="width: 50%; height: 20px;">
                        {{ $injury->date_event }}
                    </td>
                    <td style="width: 50%; height: 20px;">
                        {{ $injury->event_post}} {{ $injury->event_city }} - {{ $injury->event_street }}
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 50%;">Marka pojazdu</td>
                    <td style="width: 50%;">Nr rejestracyjny pojazdu</td>
                </tr>
                <tr class="">
                    <td style="width: 50%; height: 20px;">
                        {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
                    </td>
                    <td style="width: 50%; height: 20px;">
                        {{ $vehicle->registration}}
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 100%;" colspan="2">Jeżeli szkoda spowodowana jest przez osobę trzecią prosimy podać: imię i nazwisko sprawcy szkody, marka i nr rejestracyjny pojazdu, nr polisy OC i nazwa zakładu ubezpieczeń</td>
                </tr>
                <tr class="">
                    <td style="width: 100%; height: 50px;" colspan="2">
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 100%;" colspan="2">Upoważnienie do wypłaty odszkodowania na: UŻYTKOWNIKA LUB ZAKŁAD NAPRAWCZY – PIECZĘĆ, NAZWA, NIP, NR KONTA BANKOWEGO</td>
                </tr>
                <tr class="">
                    <td style="width: 100%; height: 100px;" colspan="2">
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 100%;" colspan="2">Nazwa zakładu ubezpieczeń, w którym zgłoszono szkodę: ADRES, TELEFON</td>
                </tr>
                <tr class="">
                    <td style="width: 100%; height: 50px;" colspan="2">
                        @if($injury->insuranceCompany)
                            <?php $insurance_company = $injury->insuranceCompany;?>
                            {{ $insurance_company->name }}, {{ $insurance_company->post }} {{ $insurance_company->city }} {{ $insurance_company->street }}, {{ $insurance_company->phone }}
                        @endif
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 100%;" colspan="2">Numer szkody</td>
                </tr>
                <tr class="">
                    <td style="width: 100%; height: 50px;" colspan="2">
                        {{ $injury->injury_nr }}
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 100%;" colspan="2">Opis zdarzenia i zakres uszkodzeń</td>
                </tr>
                <tr class="">
                    <td style="width: 100%; height: 50px;" colspan="2">
                        @if($injury->remarks != 0)
                            {{ $injury->getRemarks->content }}
                        @endif
                    </td>
                </tr>
                <tr class="blue">
                    <td style="width: 100%;" colspan="2">Miejsce postoju uszkodzonego pojazdu</td>
                </tr>
                <tr class="">
                    <td style="width: 100%; height: 50px;" colspan="2">
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
                <tbody>
                <tr >
                    <td style="width:50%; "></td>
                    <td style="text-align:center;">......................................</td>
                </tr>
                <tr >
                    <td style="width:50%; "></td>
                    <td style="text-align:center; font-size: 6pt;" >Czytelny podpis
                    </td>
                </tr>
                </tbody>
            </table>
        </div>



        <div style ="margin-top:0px; font-size: 9pt; font-weight:bold;">
            UWAGA:
        </div>
        <div style ="margin-top:10px; font-size: 7pt; line-height: 7pt;">
            <ul class="disc">
                <li><span>Odszkodowanie może być wypłacone tylko i wyłącznie na podstawie faktur za naprawę pojazdu.</span></li>
                <li><span>Faktury powinny zostać wystawione na leasingobiorcę</li>
                <li><span>Czytelnie i kompletnie wypełniony wniosek należy odesłać na nr faksu {{ checkIfEmpty('6', $ideaA) }} lub email: {{ checkIfEmpty('4', $ideaA) }}</span></li>
                <li><span>Po wystawieniu upoważnienia wystawiona zostanie opłata w wysokości 15 zł lub 25 zł.</span></li>
                <li><span>Przypominamy o wykonaniu badania technicznego jeżeli uszkodzeniu uległ układ hamulcowy, nośny lub kierowniczy
                    mający wpływ na bezpieczeństwo w ruchu drogowym.</span></li>
                <li><span>Wypłata odszkodowania nastąpi zgodnie ze wskazaniem Użytkownika, o ile Użytkownik nie będzie miał jakiejkolwiek
                    zaległości w płatnościach wobec {{ checkIfEmpty('1', $ideaA) }}</span></li>
            </ul>
        </div>

    </div>

</div>
</div>

</body>
</html>
