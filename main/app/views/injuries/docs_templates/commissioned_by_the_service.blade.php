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
    <div class="t-body">
        <table>
            <tr>
                <td style="font-size: 9px";>
                    {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br />
                    {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}<br />
                    {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br />
                    {{ (isset($ideaA[5])) ? $ideaA[5] : '---' }}<br />
                        <a href="mailto:szkodyasysta@cas-auto.pl">szkodyasysta@cas-auto.pl</a>
                </td>
                <td class="text-right" style="font-size: 9px";>
                    Zleceniobiorca: {{ ($branch) ? $branch->company->name : '<i>brak danych</i>'}}<br />
                    Adres: {{ ($branch) ? $branch->street : '<i>brak danych</i>' }}<br />
                           {{ ($branch) ? $branch->code.',' : ''}} {{ ($branch) ? $branch->city : ''}}<br />
                    Tel.   {{ ($branch) ? $branch->phone : '<i>brak danych</i>'}}
                </td>
            </tr>
        </table>
        <h2 class="text-center">
            Zgłoszenie szkody z dnia {{ date('Y-m-d') }}<br />
            Nr sprawy {{$injury->case_nr}}
        </h2>
        <table class="overflow-hidden border-outside  border-none-last">
            <tr>
                <td >
                    <table class="non-bordered no-padding no-margin" cellpadding="0" cellspacing="0">
                        <tr>
                            <td >
                                Nr rejestracyjny:
                            </td>
                            <td>
                                {{ $injury->vehicle->registration }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Marka pojazdu:
                            </td>
                            <td>
                                {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}
                            </td>
                        </tr>
                        <tr>
                            <td >
                                Numer VIN:
                            </td>
                            <td>
                                {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}
                            </td>
                        </tr>
                        <tr>
                            <td >
                                Rok produkcji:
                            </td>
                            <td>
                                {{ $injury->vehicle->year_production }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Właściciel pojazdu:
                            </td>
                            <td>
                                {{ $injury->vehicle->owner->name }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td >
                    <table class="non-bordered no-padding no-margin" cellpadding="0" cellspacing="0">

                        <tr>
                            <td>
                                Klient:
                            </td>
                            <td>
                                {{ ($injury->client) ? $injury->client->name : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Użytkownik pojazdu:
                            </td>
                            <td>
                                @if($injury->contact_person == 1  )
                                    @if($injury->driver)
                                        {{ $injury->driver->surname }} {{ $injury->driver->name }}
                                    @endif
                                @else
                                    {{ $injury->notifier_surname}} {{ $injury->notifier_name }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tel. Kontaktowy:
                            </td>
                            <td>
                                @if($injury->contact_person == 1 )
                                    @if($injury->driver_id !='' && $injury->driver)
                                        {{ $injury->driver->phone }}
                                    @endif
                                @else
                                    {{ $injury->notifier_phone }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Adres e-mail:
                            </td>
                            <td>
                                @if($injury->contact_person == 1 )
                                    @if($injury->driver_id != '' && $injury->driver)
                                        {{ $injury->driver->email }}
                                    @endif
                                @else
                                    {{ $injury->notifier_email }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="overflow-hidden border-outside  border-none-last" style="margin-top: 5px;">
            <tr>
                <td style="width: 60%;">
                    <table class="non-bordered no-padding no-margin" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 220px;">
                                Sposób&nbsp;likwidacji&nbsp;szkody:
                            </td>
                            <td style="border-left: 1px solid black;">
                                {{ $injury->injuries_type()->first()->name }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Zakład ubezpieczeń:
                            </td>
                            <td style="border-left: 1px solid black;">
                                {{ $injury->insuranceCompany ? $injury->insuranceCompany->name : ''}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Nr szkody:
                            </td>
                            <td style="border-left: 1px solid black;">
                                {{$injury->injury_nr}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Ubezpieczenie:
                            </td>
                            <td style="border-left: 1px solid black;">
                                @if(in_array($injury->injuries_type_id, [1,4]))
                                    {{ Config::get('definition.compensationsNetGross')[$injury->injuryPolicy->netto_brutto] }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%; vertical-align: middle; border-left: 1px solid black;" class="background-yellow" rowspan="2">
                    <table class="non-bordered no-padding no-margin  " cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                Odbiorca faktury:
                                <br><br>
                                <strong>
                                @if( $injury->invoicereceives_id == 0 || $injury->invoicereceives_id == NULL)
                                    <i>nieokreślono odbiorcy faktury</i>
                                @else
                                    @if($injury->invoicereceives_id == 1)
                                        {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}
                                    @elseif($injury->vehicle->client)
                                        {{ $injury->vehicle->client->name }}
                                    @endif
                                        <br>
                                    @if($injury->invoicereceives_id == 1)
                                        {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }}
                                        {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }},
                                    @elseif($injury->vehicle->client)
                                        {{$injury->vehicle->client->correspond_post}} {{$injury->vehicle->client->correspond_city}},
                                    @endif
                                    <br>
                                    @if( $injury->invoicereceives_id == 0 || $injury->invoicereceives_id == NULL)
                                    @else
                                        @if($injury->invoicereceives_id == 1)
                                            {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
                                        @elseif($injury->vehicle->client)
                                            {{$injury->vehicle->client->correspond_street}}
                                        @endif
                                    @endif
                                @endif
                                <br />
                                NIP:
                                @if($injury->invoicereceives_id == 1)
                                    {{ (isset($ideaA[8])) ? $ideaA[8] : '---' }}
                                @elseif($injury->vehicle->client)
                                    {{$injury->vehicle->client->NIP}}
                                @endif
                                </strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 60%;">
                    <table class="non-bordered no-padding no-margin" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 220px;">
                                Pojazd wymaga holowania:
                            </td>
                            <td style="border-left: 1px solid black;">
                                @if( $injury->if_towing == 1 )
                                    TAK
                                @else
                                    NIE
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Wymagane auto zastępcze&nbsp;na&nbsp;czas&nbsp;naprawy*:
                            </td>
                            <td style="border-left: 1px solid black; vertical-align: bottom">
                                @if( $injury->if_courtesy_car == 1)
                                    TAK
                                @else
                                    NIE
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p class="under-table">UWAGA: Wszelkie koszty niepokryte przez Ubezpieczyciela, wynikające z potrąceń oraz amortyzacji, pokrywa klient!
            W sprawie płatności prosimy o kontakt przed wystawieniem FV. Przy szkodach z OC odbiorcą FV za wynajem samochodu zastępczego jest klient!
        <br>
            *Dostępność pojazdu zastępczego w serwisie w miarę możliwości.
        </p>

        <h5 class="no-bold no-margin no-padding text-center">Uszkodzenia</h5>
        <table class="overflow-hidden border-outside">
            <tr>
                <td>
                    @foreach($damageSet as $k => $v)
                        {{ $damage->find($v->damage_id)->name}}
                        @if($v->param != 0)
                            @if($v->param == 1)
                                lewe/y
                            @else
                                prawe/y
                            @endif
                        @endif
                        ;
                    @endforeach
                </td>
            </tr>
        </table><br />

        <h5 class="no-bold no-margin no-padding text-center">Uwagi</h5>
        <table class="overflow-hidden border-outside">
            <tr>
                <td>
                    @if($injury->remarks_damage != 0)
                        {{ $remarks->content }}
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="text-center v-bottom"><br />
                    .................................<br /><br />
                    Podpis Użytkownika<br />
                    (czytelny)
                </td>
                <td class="text-center v-bottom"><br />
                    .................................<br /><br />
                    Podpis zleceniobiorcy<br />
                    (czytelny)
                </td>
                <td class="text-center v-bottom">
                    @include('modules.signatures')
                    <br />
                    Zlecający
                </td>
            </tr>
        </table>

        <ul class="list-none" style="font-size: 8px; line-height: 10px;">
            <li><b>- PRZED ZAKOŃCZENIEM NAPRAWY POJAZDU PROSIMY O POTWIERDZENIE ZGODY NA WYDANIE POJAZDU UŻYTKOWNIKOWI,</b></li>
            <li>- przed wystawieniem faktury, prosimy o przesłanie do Działu Serwisu i Likwidacji Szkód {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} kompletu dokumentów niezbędnych do wystawienia upoważnienia do odbioru odszkodowania,
            </li>
            <li>- po zakończeniu naprawy należy niezwłocznie wystawić fakturę(y) i wysłać pocztą lub elektroniczną w PDF na adres email,</li>
            <li>- faktury za naprawę proszę wystawić zgodnie z informacją w powyższym zgłoszeniu;
                    <span class="background-yellow" style="font-weight: bold;">
                    Oryginał faktury proszę przesłać: Centrum Asysty Szkodowej sp. z o.o., ul. Gwiaździsta 66, 53-413 Wrocław</span>, kopię do Zakładu Ubezpieczeń,
            </li>
            <li>- użytkownik pojazdu nie jest osobą upoważnioną do podpisywania faktur VAT wystawionych na {{ (isset($ideaA[1])) ? $ideaA[1] : '' }},</li>
            <li>- jeżeli zakres czynności wykonanych będzie wykraczał poza powyższe (bez uzgodnienia z {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}) lub faktura będzie miała błędy merytoryczne, {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} zastrzega sobie prawo do wstrzymania wypłaty należności do momentu wyjaśnienia niezgodności.</li>
        </ul>
    </div>
    <table style="width: 100%; position: fixed; bottom: 0; ">
        <tr >
            <td style="text-align: center;  font-size: 9px; color: grey;">1/2</td>
        </tr>
    </table>
</div>
<hr>
<div class="body content body-margin-big" style="margin-top: 0.3in;">
    <div class="t-body-size-14">
        <table style="margin-top: 10px; width: 100%">
            <tr>
                <td style="width: 50%;">
                    Miejscowość……………………………………………………………
                </td>
                <td style="width: 50%; text-align: right;">
                    dnia…………………………………
                </td>
            </tr>
        </table>
        <h2 class="text-center" style="margin-top: 30px;">
            Protokół przekazania pojazdu do naprawy
        </h2>
        <table class="overflow-hidden" cellspacing="0" cellpadding="0" style="width: 100%; border: 1px solid black;">
            <tr>
                <td style="width: 50%; padding: 5px;">
                    Przekazujący / Dane Klienta
                </td>
                <td style="width: 50%; border-left: 1px solid black; padding: 5px;">
                    Przyjmujący / Serwis /
                </td>
            </tr>
            <tr>
                <td style=" padding: 5px;">
                    <strong>
                    {{ ($injury->client) ? $injury->client->name.'<br/>'.$injury->client->correspond_street.'<br/>'.$injury->client->correspond_post.' '.$injury->client->correspond_city : '' }}
                    </strong>
                </td>
                <td style="border-left: 1px solid black; line-height: 16px; padding: 5px;">
                    <strong>
                    {{ ($branch) ? $branch->company->name : ''}}<br />
                    {{ ($branch) ? $branch->street : '' }}<br />
                    {{ ($branch) ? $branch->code.',' : ''}} {{ ($branch) ? $branch->city : ''}}<br />
                    </strong>
                    Tel.   {{ ($branch) ? $branch->phone : ''}}
                </td>
            </tr>
            <tr>
                <td style=" padding: 5px;">
                    Nr rej. pojazdu: {{ $injury->vehicle->registration }}
                </td>
                <td style="border-left: 1px solid black; line-height: 16px; padding: 5px;">
                </td>
            </tr>
        </table>
        <p style="padding-top: 30px;">
            W związku ze szkodą nr <strong>{{ $injury->injury_nr}}</strong> z dnia <strong>{{ $injury->date_event }}</strong>
        </p>
        <p>
            Likwidowaną w ramach ubezpieczenia {{ $injury->injuries_type()->first()->name }}  przekazuję pojazd do naprawy oraz przyjmuję do wiadomości, iż:
        </p>
        <p style="font-weight: bold;">
            - naprawa szkody zostanie wykonana w zakresie objętym kosztorysem zatwierdzonym przez ubezpieczyciela,
        </p>
        <p style="font-weight: bold;">
            - wypłata odszkodowania nastąpi po ustaleniu odpowiedzialności ubezpieczyciela za powstałą szkodę,
        </p>
        <p>
            1.	Oświadczam, iż w przypadku ujawnienia się lub powstania okoliczności wyłączających lub ograniczających odpowiedzialność ubezpieczyciela jak również w razie braku wypłaty odszkodowania z innych przyczyn leżących po stronie użytkownika zobowiązuję się do pokrycia kosztów naprawy we własnym zakresie w terminie 7 dni od przekazania mi wiadomości w tym zakresie potwierdzony decyzją Towarzystwa Ubezpieczeniowego.
        </p>
        <p>
            2.	Oświadczam, że w przypadku mojej rezygnacji z naprawy we wskazanym serwisie, zobowiązuje się do pokrycia kosztów poniesionych przez serwis w związku z realizacją zlecenia naprawy, w terminie 7 dni od przekazania mi wiadomości o powstałym zobowiązaniu.
        </p>
        <p style="margin-top: 30px;">
            ……………………………………………………………
            <br>
            Podpis użytkownika
        </p>
        <p>
            Własnoręczność podpisu stwierdzam na podstawie dowodu osobistego
        </p>
        <p>
            Seria ………………………… nr……………………………… wydanego dnia …………………………………
        </p>
        <p>
            przez ……………………………………………………………………………………………………………………
        </p>
        <p style="margin-top: 30px;">
            ………………………………………………………………………………………… Dane serwisu / Pieczęć
        </p>
    </div>
    <table style="width: 100%; position: fixed; bottom: 0;">
        <tr >
            <td style="text-align: center; font-size: 9px; color: grey;">2/2</td>
        </tr>
    </table>
</div>
</body>
</html>
