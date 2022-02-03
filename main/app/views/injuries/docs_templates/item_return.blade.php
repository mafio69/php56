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

    td, h4 {
        line-height: 1.5
    }
</style>
<body>
<div class="body content body-margin-big">


        <div>

            @include('injuries.docs_templates.modules.place')

        </div>

        <table class="letter">
            <tr>
                <td style="width: 9cm; height: 3cm; text-align: left; vertical-align: middle;">
                    <p class="block text-justify">
                        Szanowni Państwo
                    </p>
                </td>
            </tr>
        </table>

        <p class="block text-justify" style="margin-top:1.0cm;"><b >
            Dotyczy umowy leasingowej {{$vehicle->nr_contract}} <br>
            Numer rejestracyjny {{$vehicle->registration}} <br>
            Numer szkody {{$injury->injury_nr}}</b>
        </p>


        <h4 class="text-center">WEZWANIE DO ZWROTU PRZEDMIOTU LEASINGU</h4>

        <p class="block text-justify" style="margin-top: 0.5cm;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            W imieniu firmy Idea Getin Leasing Spółka Akcyjna, wzywamy Państwa do natychmiastowego zwrotu przedmiotu leasingu w terminie do 7 dni od daty otrzymania niniejszego pisma.
            <br>
            <b>
            Pojazd prosimy dostarczać na adres:
            </b>
            <br>
            ul. Fabryczna 24, 55-080 Pietrzykowice,
            <br>
            tel. 667-892-806, 71 77-88-950 (pojazdy osobowe)
            <br>
            tel.603-197-038, 601-150-428 (pojazdy ciężarowe)
            <br>
            lub
            <br>
            FAMAT Słomczyn 70, 05-600 Grójec, tel.: 516-579-714 lub 882-55-33-14
        </p>
        <p class="block text-justify" style="margin-top: 0.5cm; margin-bottom: 1.0cm">
            Wraz z pojazdem, prosimy o dostarczenie wszystkich kompletów kluczy oraz wszelkiej dokumentacji pojazdu.
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Zgodnie z Ogólnymi Warunkami Umowy Leasingowej § 24 pkt. 3 Leasingodawca może, przed rozwiązaniem umowy leasingu, dodatkowo wezwać Leasingobiorcę do zwrotu przedmiotu leasingu.
        </p>

        <table style="width: 100%;  font-size: 9pt; font-weight:normal; margin-top: 1.0cm">
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

</body>
</html>
