<?php //odmowa wydania upoważnienia ?>
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
            @include('injuries.docs_templates.modules.client')

        </div>

        <div style="margin-top: 40pt; font-size: 9pt;">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu/pożyczki: {{ $vehicle->nr_contract }}</p>
        </div>

        <div style ="margin-top:30pt; font-size:9pt; text-align:justify;text-justify:inter-word;">
            <p >
                Szanowni Państwo,
            </p>
            <p style="margin-top: 10pt;">
                Z związku z otrzymaniem informacji o powstaniu w/w szkody komunikacyjnej, zwracamy się z uprzejmą prośbą o pilne wypełnienie załączonego wniosku.
            </p>
            <p style="margin-top: 10pt;">
                Wypełniony dokument prosimy przesłać bezpośrednio na adres: {{{ checkIfEmpty('4', $ideaA) }}}
            </p>
            <p style="margin-top: 10pt;">
                Przesłanie wypełnionego wniosku nie jest równoznaczne ze zgłoszeniem szkody do ubezpieczyciela pojazdu i nie zwalnia Państwa z obowiązku uzupełnienia dokumentacji szkodowej w zakładzie ubezpieczeń.
            </p>
            <p style="margin-top: 10pt;">
                Jednocześnie informujemy, że nie wyrażamy zgody na kosztorysowe rozliczenie szkody. Rozliczenie szkody powinno nastąpić w oparciu o faktury VAT, potwierdzające wykonanie naprawy w/w pojazdu, które należy złożyć bezpośrednio do Towarzystwa Ubezpieczeniowego likwidującego szkodę komunikacyjną.
            </p>
            <p style="margin-top: 30pt; font-style: italic; ">
                Informujemy Państwa o możliwości naprawy pojazdu w sieci wybranych serwisów współpracujących z Idea Leasing. W takim przypadku oferujemy Państwu : <span style="border-bottom: 1px solid black;">pierwszeństwo w przyjęciu pojazdu do naprawy, pomoc w zgłoszeniu szkody do ZU i skompletowaniu dokumentów, bezpłatny pojazd zastępczy na czas naprawy, pokrycie kosztów podatku VAT.</span>
            </p>
            <p style="margin-top: 10pt; font-style: italic;">
                Jeżeli chcecie Państwo skorzystać z naszej oferty, prosimy o zaznaczenie w załączonym druku: <br/>"<span style="border-bottom: 1px solid black; ">serwis współpracujący z Idea Leasing</span>" lub o kontakt z nami.
            </p>
            <p style=" margin-top: 30pt; ">
                W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. {{ checkIfEmpty('5', $ideaA) }} lub na adres {{ checkIfEmpty('4', $ideaA) }}
            </p>
        </div>
        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:25px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
