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
        font-family: Lato;
        font-size: 10.5pt;
        text-align: justify;
        text-justify: inter-word;
        line-height: 1.1;
    }

    p {
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

            {{-- <table style="width:100%; font-size:9pt; margin-top:15pt;  font-weight:normal;" >
                <tr>
                    <td></td>
                    <td style="text-align: right; width: 8.2cm;">{{ ($injury->insuranceCompany) ? $injury->insuranceCompany->name : ''}}</td>
                </tr>
                <tr>
                    <Td></Td>
                    <td style="text-align: right;width: 8.2cm;">{{ ($injury->insuranceCompany) ? $injury->insuranceCompany->street : ''}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: right;width: 8.2cm;">{{ ($injury->insuranceCompany) ? $injury->insuranceCompany->post : ''}} {{ ($injury->insuranceCompany) ? $injury->insuranceCompany->city : ''}}</td>
                </tr>
            </table> --}}


            <p style="margin-top: 1.0cm">Szanowni Państwo,<br><br>
                W imieniu Idea Getin Leasing S.A. uprzejmie informujemy, iż w przypadku zaistnienia szkody
                komunikacyjnej na pojeździe do 3,5 tony mają Państwo możliwość skorzystania z bezpłatnej pełnej
                asysty szkodowej. To wygodna forma likwidacji szkody, w której wszystkie formalności załatwia
                Idea Getin Leasing.<br><br>
                Wystarczy skontaktować się z nami pod numerem telefonu 71/3344807 lub poprzez portal <a style="border-bottom: 1px solid black; font-size: 10.5pt"
                        href="http://www.BankKierowcy.pl">www.BankKierowcy.pl</a><br><br>
            <p>
                W ramach asysty szkodowej zapewniamy:<br>
            <ol style="line-height: 1.1">
                <li>zgłoszenie szkody w TU jeśli szkoda likwidowana jest z polisy AC (Hestia, PZU, Compensa, Warta). W przypadku szkody z OC prosimy o zgłoszenie szkody do TU we własnym zakresie,
                </li>
                <li>holowanie na zasadach pomocy w zorganizowaniu bez gwarantowania nieodpłatności,</li>
                <li>skierowanie pojazdu na naprawę do zakładu naprawczego,</li>
                <li>minimum formalności,</li>
                <li>osobowy samochód zastępczy na czas naprawy, w miarę dostępności pojazdów,</li>
                <li>płatność podatku VAT za naprawę,</li>
            </ol>
            <p>
                Jeżeli nie są Państwo zainteresowani usługami asysty szkodowej prosimy o wypełnienie formularza na
                stronie <a style="border-bottom: 1px solid black; font-size: 10.5pt"href='http://www.bankkierowcy.pl'>www.BankKierowcy.pl</a> w celu sporządzenia dyspozycji
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