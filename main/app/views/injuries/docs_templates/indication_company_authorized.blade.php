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

        <p class="text-left">
            Dotyczy szkody nr {{ $injury->injury_nr }} <br />
            z dnia {{ $injury->date_event }}<br />
            na pojeździe {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}, {{ $injury->vehicle->registration }}<br />
            Umowa nr: {{ $injury->vehicle->nr_contract }}
        </p>

        <p class="block text-justify">Szanowni Państwo<br /><br />

            <b>W związku z otrzymaniem informacji o powstaniu w/w szkody komunikacyjnej, Informujemy Państwa o możliwości naprawy pojazdu w sieci wybranych serwisów. W takim przypadku zyskają Państwo: pierwszeństwo w przyjęciu pojazdu do naprawy, pomoc w zgłoszeniu szkody do ZU i skompletowaniu dokumentów, bezpłatny pojazd zastępczy (osobowy) na czas naprawy, pokrycie kosztów podatku VAT przez Idea Getin Leasing *</b>

            <br><br>

            Jeżeli chcecie Państwo skorzystać z naszej oferty, prosimy o kontakt telefoniczny pod nr tel. <b>71/3344807</b>

            <br><br>

            W przypadku wyboru własnego serwisu prosimy o uzupełnienie wniosku o wydanie upoważnienia do wypłaty odszkodowania. Wypełniony dokument prosimy przesłać bezpośrednio na adres: <a href="mailto:skody@ideagetin.pl">szkody@ideagetin.pl</a>. Wniosek do pobrania na stronie www.ideagetin.pl.

            <br><br>

            Warunkiem wydania upoważnienia zgodnie z wnioskiem jest brak zaległości płatniczych wobec leasingu.

            <br><br>

            <span style="border-bottom: 1px solid #000; padding-bottom:4px;">W przypadku braku odpowiedzi odszkodowanie zostanie wypłacone na konto Idea Getin Leasing</span>

            <br><br>

            Przesłanie wypełnionego wniosku nie zwalnia Państwa z obowiązku uzupełnienia dokumentacji szkodowej w zakładzie ubezpieczeń.

            <br><br>

            Jednocześnie informujemy, że nie wyrażamy zgody na kosztorysowe rozliczenie szkody. Rozliczenie szkody powinno nastąpić w oparciu o faktury VAT, potwierdzające wykonanie naprawy w/w pojazdu, które należy złożyć bezpośrednio do Towarzystwa Ubezpieczeniowego likwidującego szkodę komunikacyjną.

        <p class="small-text">* powyższe nie stanowi oferty w rozumieniu przepisów Kodeksu Cywilnego w szczególności art. 66 i nast. K.c. Każdy przypadek rozpatrywany jest indywidualnie – w przypadku pojazdów specjalistycznych usługa może różnić się od przedstawionej. Nie dotyczy umów pożyczek.</p><br />

        <table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
