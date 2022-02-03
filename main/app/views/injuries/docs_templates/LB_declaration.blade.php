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
    * {
        font-family: "Times New Roman", "Times", serif;
        font-size: 14pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 14pt;
        line-height: 1.5;
        margin-top: 5px;
    }

    b {
        line-height: 1.5;
        font-size: 14pt;
    }
    span {
        font-size: 14pt;
    }
</style>

<body style="margin-top: 0.5cm">

<div id="body">
    <div class="center" style="outline: 1px solid black; padding-top: 0.3cm; padding-bottom: 0.3cm">
        <span style="font-size: 12pt; font-family: Times New Roman, Times, serif;" class="center">D E K L A R A C J A</span>
    </div>
    <p>
        <br>Numer umowy leasingu/pożyczki: {{$vehicle->nr_contract}}.<br>
    </p>
    <p>
        Oświadczam, że w związku ze szkodą całkowitą nr {{$injury->injury_nr}} z dnia {{$injury->date_event}}<br> dotyczącą
        pojazdu {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
        nr rej. {{$vehicle->registration}}
        nr VIN {{$vehicle->VIN}}.
    </p>
    <p>
        <strong>
            Chcę skorzystać z prawa pierwokupu / rezygnuję z przysługującego mi prawa
            pierwokupu pojazdu*
        </strong> użytkowanego w ramach umowy leasingu.
        (*niepotrzebne skreślić)

    </p>
    <p>
        W PRZYPADKU REZYGNACJI Z WYKUPU, zagospodarowania pozostałości pozostawiam właścicielowi pojazdu Idea Getin
        Leasing
        S.A.<br><br>

        Pojazd znajduje się pod adresem:<br><br>
        ………………………………………………………………………………………………………………………………………………………………………………………………………………<br><br>
        ………………………………………………………………………………………………………………………………………………………………………………………………………………

    </p>
    <p style="font-size: 14pt">Wraz z pojazdem dla nowego nabywcy zostaną przekazane następujące dokumenty:</p>
    <table style="border: 1px solid black;
        width: 100%; border-collapse:collapse;border-spacing:0;">
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.2cm">
                Nazwa dokumentu
            </td>
            <td class="text-center" style="border: 1px solid black; height: 1.2cm">
                Posiadam TAK/NIE
            </td>
            <td class="text-center" style="border: 1px solid black; height: 1.2cm">
                Nie posiadam, dokument znajduje się w...
            </td>
        </tr>
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.2cm">
                <b>Dowód rejestracyjny STAŁY</b><br>Prosimy o przesłanie skanu dokumentu
            </td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
        </tr>
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.2cm">
                <b>Pozwolenie CZASOWE</b>
            </td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
        </tr>
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.5cm">
                <b>Pokwitowanie o zatrzymaniu dowodu rejestracyjnego przez policję</b><br>Prosimy o przesłanie skanu dokumentu
            </td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
        </tr>
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.5cm">
                <b>Polisa OC</b><br>Prosimy o podanie okresu ważnosci
            </td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
        </tr>
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.5cm">
                <b>Ilość kluczy</b>
            </td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
        </tr>
        <tr>
            <td class="text-center" style="border: 1px solid black; height: 1.2cm">
                <b>Książki serwisowe pojazdu</b>
            </td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
            <td style="border: 1px solid black; height: 1.2cm"></td>
        </tr>
    </table>

    <p style="font-size: 14pt; font-family: Times New Roman, Times, serif;"><br>
        <b style="font-size: 14pt; font-family: Times New Roman, Times, serif;">Nieprzekazanie w/w dokumentów nowemu nabywcy, będzie wiązało się z koniecznością
            wyrobienia
            wtórników, a
            koszty
            ich wyrobienia będą brane pod uwagę podczas rozliczenia Państwa umowy leasingu.</b><br><br>

        W przypadku skorzystania z prawa pierwokupu lub uruchomienia sprzedaży oferentowi aukcyjnemu/ kupcowi wskazanemu
        przez Państwa
        <b style="font-size: 14pt; font-family: Times New Roman, Times, serif;">informujemy, że istnieje możliwość zwolnienia z przetransportowania pojazdu na plac
            Idea Getin Leasing S.A.,
            pod
            warunkiem zapewnienia należytego zabezpieczenia pojazdu po szkodzie oraz braku kosztów parkowania.</b> W
        takim
        przypadku , prosimy o przysłanie pisemnego oświadczenia wraz z podpisem.</p>
    <div class="right" style="margin-top: 2cm; margin-right: 0.5cm">
        .................................<br>
        Data, podpis
    </div>
</div>
</body>
</html>