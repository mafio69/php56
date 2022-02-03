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

    b {
        font-size: 12pt
    }
    td {
        line-height: 1.5
    }
</style>
<body>
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <p class="block"><b>Dotyczy: szkoda nr {{ $injury->injury_nr }} z dnia {{ $injury->date_event }} pojazd {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}; nr rejestracyjny {{ $injury->vehicle->registration }}; nr umowy leasingu {{ $injury->vehicle->nr_contract }}
        </b></p>

        <p class="block text-justify">Szanowni Państwo,<br /><br />

            W związku z w/w szkodą komunikacyjną zakwalifikowaną jako całkowita, działając w imieniu {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}, będącej właścicielem przedmiotowego pojazdu, w związku z rozwiązaniem wskazanej wyżej umowy w trybie § 25 ust. 1 Ogólnych Warunków Umowy Leasingu stanowiących integralną część przedmiotowej umowy dalej: OWUL) oraz w związku z <b>bezumownym</b> pozostawaniem jej przedmiotu w Państwa posiadaniu składamy ofertę kupna, wskazanego pojazdu:  </p>

        <ul class="list-mega-padding list-decimal font-size-12" style="font-size:12px;">
            <li>przedmiot kupna: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}</li>
            @if($injury->wreck)
              <li>cena za przedmiot, o którym mowa w pkt 1: {{ $injury->wreck->value_repurchase }} {{ Config::get('definition.currencies')[$injury->wreck->value_repurchase_currency] }}
                  @if($injury->wreck->value_repurchase_net_gross == 1)
                      netto
                  @elseif($injury->wreck->value_repurchase_net_gross == 2)
                      brutto
                  @endif
                  ,
              </li>
            @endif  
            <li>zapłata ceny, o której mowa w pkt. 2, nastąpi w oparciu o wystawioną fakturę VAT w terminie 14 dni od dnia jej wystawienia,</li>
            <li>sprzedaż następuje z wyłączeniem uprawnień wynikających z rękojmi,</li>
            <li>oferent żąda niezwłocznego wykonania umowy.</li>
        </ul><br />

        <p class="block text-justify">Niniejsza oferta kupna może zostać odrzucona wyłącznie poprzez dostarczenie do naszej dyspozycji
        pozostałości przedmiotu leasingu zgodnie z § 25 pkt. 3 OWUL wraz z kluczykami oraz wszelkimi dokumentami pojazdu w terminie 3 dni od dnia otrzymania niniejszej oferty. W przeciwnym razie, uznaje się, że przystąpili Państwo do realizacji umowy.</p>

        <p class="block text-justify">Tym samym w przypadku bezskutecznego upływu wyznaczonego terminu i niedokonania zwrotu wraku pojazdu zostanie wystawiona faktura VAT dokumentująca przejście własności na Państwa rzecz.</p>
        <hr/>
        <p class="block text-justify" style="margin-top: 20px;">Termin dostawy prosimy ustalić z naszymi kooperantami.</p>

        <p class="block text-justify">
            <b>Wszystkie pojazdy prosimy dostarczać na adres:</b><br />
            Fabryczna 24, 55-080 Pietrzykowice, tel. 717788953 (pojazdy osobowe), tel. 603-197-038, 512-154-384 (pojazdy ciężarowe, naczepy) <br />
            lub <br />
            FAMAT Słomczyn 70,  05-600 Grójec, tel. 516-579-714
        </p>

        <p class="block text-justify">W sprawie likwidacji szkody prosimy o kontakt pod nr 801 199 199 w. 5.</p><br />

        <table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
            <tbody>
            <tr >
                <td style="width:50%; "></td>
                <td style="text-align:center;">z poważaniem</td>
            </tr>
            <tr >
                <td style="width:50%; "></td>
                <td style="text-align:center;" >
                    @include('modules.signatures')
                </td>
            </tr>
            </tbody>

        </table>

    </div>
</div>

</body>
</html>
