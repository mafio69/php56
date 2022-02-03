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

        <table class="border-outside">
            <tr>
                <td class="text-center">DEKLARACJA </td>
            </tr>
        </table>
        @if( endswith($injury->vehicle->nr_contract, 'P/SK') || endswith($injury->vehicle->nr_contract, '/P')  )
            <p>Numer umowy leasingu/pożyczki: {{ $injury->vehicle->nr_contract }}</p>
            <p>Oświadczam, że w związku ze szkodą całkowitą nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}
                dotyczącą pojazdu {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} nr rej. {{ $injury->vehicle->registration }} nr VIN {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}</p>

            <p><b>Chcę pozostawić pojazd w swojej dyspozycji  / chcę skorzystać z możliwości sprzedaży pojazdu oferentowi aukcyjnemu
                    <br /><br/>
                (*niepotrzebne skreślić)
                </b>
            </p>

            <p>Pojazd znajduje się pod adresem:<br /><br />
                ........................................................................................................................................................<br /><br />
                ........................................................................................................................................................

            <p>Wraz z pojazdem dla nowonabywcy zostaną przekazane następujące dokumenty:</p>
        @else
            <p>Numer umowy leasingu/pożyczki: {{ $injury->vehicle->nr_contract }}</p>

            <p>Oświadczam, że w związku ze szkodą całkowitą nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}
                dotyczącą pojazdu {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} nr rej. {{ $injury->vehicle->registration }} nr VIN {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}</p>

            <p><b>Chcę skorzystać z prawa pierwokupu / rezygnuję z przysługującego mi prawa pierwokupu pojazdu</b>* użytkowanego w ramach umowy leasingu.<br />(*niepotrzebne skreślić) </p>

            <p>W PRZYPADKU REZYGNACJI Z WYKUPU, zagospodarowania pozostałości pozostawiam właścicielowi pojazdu Idea Getin Leasing S.A.</p>

            <p>Pojazd znajduje się pod adresem:<br />
            ........................................................................................................................................................<br /><br />
            ........................................................................................................................................................

            <p>Wraz z pojazdem dla nowego nabywcy zostaną przekazane następujące dokumenty:</p>
        @endif

        <table class="overflow-hidden border-outside2 table-size-12">
            <tr>
                <td     class="text-center" style="width: 44%;">Nazwa dokumentu </td>
                <td class="text-center" style="width: 12%;">
                    Posiadam<br />
                    TAK / NIE
                </td>
                <td class="text-center">Nie posiadam, dokument znajduje się...</td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Dowód rejestracyjny STAŁY</b><br />
                    (prosimy o przesłanie skanu dokumentu)
{{--                        pokwitowanie o zabraniu<br />--}}
{{--                        dowodu z policji*<br />--}}
{{--                        ( *niepotrzebne skreślić) <br />--}}
{{--                        Jednocześnie prosimy o przeslanie skanu dokumentu</i>--}}
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Pozwolenie CZASOWE</b>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Pokwitowanie o zatrzymaniu dowodu rejestracyjnego przez policje</b><br />
                    (prosimy o przesłanie skanu dokumentu)
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Polisa OC</b><br />
                        (prosimy o podanie okresu ważności)
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td  class="text-center">
                    <b>Ilość kluczy </b>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="text-center">
                    <b>Książki serwisowe pojazdu </b>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
{{--            <tr>--}}
{{--                <td class="text-center">--}}
{{--                    <i>Inne</i>--}}
{{--                </td>--}}
{{--                <td>&nbsp;</td>--}}
{{--                <td>&nbsp;</td>--}}
{{--            </tr>--}}
        </table><br /><br />

        @if( endswith($injury->vehicle->nr_contract, 'P/SK') || endswith($injury->vehicle->nr_contract, '/P')  )
            <p style="margin-top:0px;margin-bottom:0px;">
                <b>
                    Nieprzekazanie w/w dokumentów nowemu nabywcy, będzie wiązało się z koniecznością wrobienia wtórników, a koszty ich wyrobienia będą brane pod uwagę podczas rozliczenia Państwa umowy pożyczki.
                </b>
            </p>
        @else
            <p style="margin-top:0px;margin-bottom:0px;"><b>Nieprzekazanie w/w dokumentów nowemu nabywcy, będzie wiązało się z koniecznością wrobienia wtórników, a koszty ich wyrobienia będą brane pod uwagę podczas rozliczenia Państwa umowy leasingu.</b></p><br />

            <p style="margin-top:0px;margin-bottom:0px;">W przypadku skorzystania z prawa pierwokupu lub uruchomienia sprzedaży oferentowi aukcyjnemu/ kupcowi wskazanemu przez Państwa <b>informujemy, że istnieje możliwość zwolnienia z przetransportowania pojazdu na plac Idea Getin Leasing S.A., pod warunkiem zapewnienia należytego zabezpieczenia pojazdu po szkodzie oraz braku kosztów parkowania.</b> W takim przypadku, prosimy o przysłanie pisemnego oświadczenia wraz <br>z podpisem.</p>
        @endif

        <br />
{{--        <table>--}}
{{--            <tr>--}}
{{--                <td class="text-right" style="margin-top:0px;">--}}
{{--                    .........................................<br />--}}
{{--                    <p style="margin-top:0px;margin-bottom:0px;">(data i podpis)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--        </table>--}}
        <p style="margin-top:0px;margin-bottom:0px; text-align: right">.........................................<br /></p>
        <p style="margin-top:0px;margin-bottom:0px; text-align: right">(data i podpis)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>

    </div>
</div>

</body>
</html>
