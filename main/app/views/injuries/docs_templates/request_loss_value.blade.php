<?php //wniosek o handlowy ubytek wartości pojazdu ?>
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

            @if($injury->insuranceCompany)
            <table style="width:100%; font-size:9pt; margin-top:20pt;  font-weight:normal;" >
                <tr>
                    <Td></Td>
                    <td style="text-align: right; width: 8.2cm;">{{$injury->insuranceCompany->name}}</td>
                </tr>
                <tr>
                    <Td></Td>
                    <td style="text-align: right; width: 8.2cm;">{{$injury->insuranceCompany->street}}</td>
                </tr>
                <tr>
                    <Td></Td>
                    <td style="text-align: right; width: 8.2cm;">{{$injury->insuranceCompany->post}} {{$injury->insuranceCompany->city}}</td>
                </tr>
            </table>
            @endif

        </div>

        <div style="margin-top: 40pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy: {{ $vehicle->nr_contract }}</p>
        </div>

        <div style ="margin-top:25pt; font-size:9pt; text-align:justify;text-justify:inter-word;">
            <p style="font-weight:bold; text-align:center">
              Wniosek o zapłatę odszkodowania z tytułu utraty wartości handlowej pojazdu.
            </p>
            <p style="text-indent:30px; margin-top: 26pt; text-align:left">
              Zgodnie z Art. 34 ust.1 ustawy z dnia 22 maja 2003 o ubezpieczeniach obowiązkowych, Ubezpieczeniowym Funduszu Gwarancyjnym i Polskim Biurze Ubezpieczycieli Komunikacyjnych w sprawie ogólnych warunków obowiązkowego ubezpieczenia odpowiedzialności cywilnej posiadaczy pojazdów mechanicznych za szkody powstałe w związku z ruchem tych pojazdów /DZ.U. nr 124/2003, poz. 1152/, w związku z przepisem art.361§ 1 k.c. - Niewątpliwie sprawca szkody, a tym samym ubezpieczyciel, zobowiązany jest do jej naprawienia.
              Naprawienie to winno nastąpić przez wypłatę określonej wysokości odszkodowania.
              Zgodnie z przepisem art. 361§ 2 k.c. który mówi, że naprawienie szkody obejmuje straty, które poszkodowany poniósł oraz korzyści, które mógłby osiągnąć gdyby mu szkody nie wyrządzono. Zapisami art. 363 § 1 k.c. - Naprawienie szkody powinno nastąpić, według wyboru poszkodowanego, bądź przez przywrócenie stanu poprzedniego, bądź przez zapłatę odpowiedniej sumy pieniężnej.
            </p>
            <p style="margin-top: 20px; text-align:left">
              W wyniku powstania szkody, w której uczestniczył pojazd poszkodowanego, doszło do utraty wartości handlowej. Zgodnie bowiem z art. 361 § 1 k.c., <b>utratę wartości handlowej pojazdu</b> należy uznać za normalne następstwo kolizji, z której szkoda w pojeździe poszkodowanego wynikła.
              Samochody powypadkowe mimo ich naprawy zwłaszcza nowe, a za taki należy uznać powyższy samochód, który w momencie kolizji miał nie więcej niż trzy lata, tracą na swej wartości handlowej - rynkowej. Nabywcy samochodów używanych ustalają czy brały one udział w kolizji, a jeżeli tak płacą za nie mniej niż za tą samą markę i ten sam rocznik ale nie biorący udziału w kolizji drogowej. Jest to normalne zjawisko występujące na rynku samochodowym.
            </p>
            <p style=" margin-top: 20px;">
              Prosimy zatem o określenie handlowego ubytku wartości pojazdu wg instrukcji Określania Rynkowego Ubytku Wartości Pojazdu nr 1/2009 Stowarzyszenia Rzeczoznawców Samochodowych - EKSPERTMOT z dnia 12.02.2009r.
              <br><br>
              Należna kwotę prosimy o przekazanie na wskazane konto :<br>
                @if($vehicle->owner&&$vehicle->owner->data)
                  {{($vehicle->owner->data()->where('parameter_id',10)->first()) ? $vehicle->owner->data()->where('parameter_id',10)->first()->value : ''}}
                @endif
            </p>
            <p style=" margin-top: 20px; ">
                Decyzje proszę przesłać do:
                <br>
                @if($vehicle->owner&&$vehicle->owner->data)
                  {{($vehicle->owner->data()->where('parameter_id',1)->first()) ? $vehicle->owner->data()->where('parameter_id',1)->first()->value : ''}}
                  <br>
                  {{($vehicle->owner->data()->where('parameter_id',2)->first()) ? $vehicle->owner->data()->where('parameter_id',2)->first()->value : ''}}
                  <br>
                  {{($vehicle->owner->data()->where('parameter_id',3)->first()) ? $vehicle->owner->data()->where('parameter_id',3)->first()->value : ''}}   {{($vehicle->owner->data()->where('parameter_id',13)->first()) ? $vehicle->owner->data()->where('parameter_id',13)->first()->value : ''}}
                  <br>
                  {{($vehicle->owner->data()->where('parameter_id',4)->first()) ? $vehicle->owner->data()->where('parameter_id',4)->first()->value : ''}}
                @endif
            </p>
        </div>
        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:10px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
