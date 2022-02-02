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
    <?php $vehicle = $injury->vehicle()->first();?>
    <div class="page"  id="content" style="margin-top: 30px;">

        <div style="font-size: 7pt;">
            @include('injuries.docs_templates.modules.place')
            @include('injuries.docs_templates.modules.client')
        </div>

        <div style="margin-top: 20pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu: {{ $vehicle->nr_contract }}</p>
        </div>


        <div style ="margin-top:20px; font-size:8pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p>
                Szanowni Państwo,
            </p>
            <p style="text-indent:30px; margin-top: 20px;">
                W związku z w/w szkodą komunikacyjną zakwalifikowaną jako całkowita, działając w imieniu {{ checkIfEmpty('1', $ideaA) }} z siedzibą we {{ checkIfEmpty('13', $ideaA) }} przy {{ checkIfEmpty('2', $ideaA) }}, będącej właścicielem przedmiotowego pojazdu, w związku z rozwiązaniem wskazanej wyżej umowy w trybie § 24 ust. 1 Ogólnych Warunków Umowy Leasingu stanowiących integralną część przedmiotowej umowy (dalej: OWUL) oraz w związku z bezumownym pozostawaniem jej przedmiotu w Państwa posiadaniu składamy ofertę kupna, wskazanego pojazdu:
            </p>
            <p style="margin-top: 20px;">
            <ul class="disc" style="list-style-type: decimal;">
                <li><span>Przedmiot kupna: <strong>{{ $vehicle->brand }} {{ $vehicle->model }}</strong>.</span></li>
                <li><span>Cena za przedmiot, o którym mowa w pkt 1: <strong>{{ $inputs['price'] }} zł</strong>.</span></li>
                <li><span>Zapłata ceny, o której mowa w pkt 2, nastąpi w oparciu o wystawioną fakturę VAT<br/>
                        w terminie 14 dni od dnia jej wystawienia.</span></li>
                <li><span>Sprzedaż następuje z wyłączeniem uprawnień wynikających z rękojmi.</span></li>
                <li><span>Oferent żąda niezwłocznego wykonania umowy.</span></li>
            </ul>
            </p>
            <p style="text-indent:30px; margin-top: 20px;">
                Niniejsza oferta kupna może zostać odrzucona wyłącznie poprzez dostarczenie do naszej dyspozycji pozostałości przedmiotu leasingu zgodnie z § 25 pkt 3 OWUL wraz z kluczykami oraz wszelkimi dokumentami pojazdu w terminie 3 dni od dnia otrzymania niniejszej oferty. W przeciwnym razie, uznaje się, że przystąpili Państwo do realizacji umowy.
            </p>
            <p style="margin-top: 20px;">
                Tym samym w przypadku bezskutecznego upływu wyznaczonego terminu i niedokonania zwrotu wraku pojazdu zostanie wystawiona faktura VAT dokumentująca przejście własności na Państwa rzecz.
            </p>
            <p style=" margin-top: 20px; ">
                Termin dostawy prosimy ustalić z naszymi kooperantami.
            </p>
            <p style=" margin-top: 20px; ">
                Pojazdy o masie całkowitej do 3,5t prosimy dostarczać na adres:<br/>
                Auto-Maxi, ul. Żmigrodzka 185, Wrocław<br/>
                Tel. 71 / 352 99 10 - czynne w godz. 8.00 - 17.00
            </p>
            <p style=" margin-top: 20px; ">
                Pozostałe pojazdy o masie całkowitej powyżej 3,5t prosimy dostarczać na adres: parking, ul. Strzegomska 55, Wrocław (teren Wprieko obok betoniarni Bosta-beton ) Tel. 0880 536 332 - czynne w godz. 8.00 - 16.00
            </p>
            <p style=" margin-top: 20px; ">
                W sprawie likwidacji szkody prosimy o kontakt pod nr {{ checkIfEmpty('5', $ideaA) }}
            </p>
            <p style=" margin-top: 20px; ">
                W imieniu {{ checkIfEmpty('1', $ideaA) }}.
            </p>
        </div>
        <table style="width: 100%; font-size: 8pt; font-weight:normal; margin-top:0px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
