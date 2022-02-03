<?php //wniosek o naprawę szkody całkowitej ?>
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
    @import url('https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700&display=swap');
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: Lato, sans-serif;
        font-size: 10.5pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        line-height: 1.2;
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>
<body>
<div id="body">
    <div class="page" id="content">

        <div>


            @include('injuries.docs_templates.modules.place')

            @include('injuries.docs_templates.modules.injury_info')

            <p><br>Szanowni Państwo,<br><br>
                W imieniu Idea Getin Leasing S.A. uprzejmie informujemy, iż w przypadku zaistnienia szkody
                komunikacyjnej na pojeździe powyżej 3,5 tony mają Państwo możliwość skorzystania z bezpłatnej asysty
                szkodowej. To wygodna forma likwidacji szkody, w której wszystkie formalności załatwia Idea Getin
                Leasing. <br><br>
                Wystarczy skontaktować się z nami pod numerem telefonu 71/3344807 lub poprzez portal <a
                        href="http://www.BankKierowcy.pl">www.BankKierowcy.pl</a><br><br>
            <p>
                W ramach asysty szkodowej zapewniamy:<br>
            <ol style="line-height: 1.1">
                <li>zgłoszenie szkody w TU jeśli szkoda likwidowana jest z polisy AC (Hestia, PZU, Compensa, Warta). W przypadku szkody z OC prosimy o zgłoszenie szkody do TU we własnym zakresie,
                </li>
                <li>skierowanie pojazdu na naprawę do zakładu naprawczego,</li>
                <li>minimum formalności,</li>
                <li>płatność podatku VAT za naprawę,</li>
            </ol>
            <p>
                Jeżeli nie są Państwo zainteresowani usługami asysty szkodowej prosimy o wypełnienie formularza na
                stronie <a href='http://www.bankkierowcy.pl'>www.bankkierowcy.pl</a> w celu sporządzenia dyspozycji
                wypłaty
                odszkodowania.

            </p>

            <p>
                <b><br>
                    Jednocześnie informujemy, iż w przypadku likwidacji szkody poza asystą szkodową pobierana jest
                    Opłata z
                    tytułu wystawienia upoważnienia w kwocie 399 zł netto zgodnie z Tabelą Opłat i Ogólnymi Warunkami
                    Umowy.<br><br>
                </b>
            </p>
            <p>
                <u>
                    W przypadku braku odpowiedzi odszkodowanie zostanie wypłacone na konto Idea Getin Leasing.<br><br>
                </u>
            </p>
            <p>
                Przesłanie wypełnionego wniosku nie zwalnia Państwa z obowiązku uzupełnienia dokumentacji
                szkodowej w zakładzie ubezpieczeń.<br><br>
                Jednocześnie informujemy, że nie wyrażamy zgody na kosztorysowe rozliczenie szkody. Rozliczenie szkody
                powinno nastąpić w oparciu o faktury VAT, potwierdzające wykonanie naprawy w/w pojazdu, które należy
                złożyć
                bezpośrednio do Towarzystwa Ubezpieczeniowego likwidującego szkodę komunikacyjną.<br>
            </p>
            <p>
                <em style="font-size: 9pt;font-weight: lighter; font-style: italic; text-decoration: none">
                    * powyższe nie stanowi oferty w rozumieniu przepisów Kodeksu Cywilnego w szczególności art.
                    66 i nast. K.c. Każdy przypadek rozpatrywany jest indywidualnie – w przypadku pojazdów
                    specjalistycznych usługa może różnić się od przedstawionej. Nie dotyczy umów pożyczek.<br>
                </em>
            </p>
        </div>

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