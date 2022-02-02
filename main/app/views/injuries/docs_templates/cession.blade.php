<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('templates-src/css/backup/notification-common.css') }}" rel="stylesheet">
    <title></title>
</head>

<style>
    @page{
        margin: 2cm;
    }
    *{
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-size: 0.9em;
        line-height: 1.5em;
        text-align: justify;
        text-justify: inter-word;
    }
    p{
        line-height: 1.5em;
        margin-top: 5px;
        font-size: 0.9em;
    }
    p strong{
        line-height: 1.5em;
        font-size: 0.9em;
    }

    p b{
        line-height: 1.5em;
        font-size: 0.9em;
    }

    td{
        line-height: 1.5em;
        font-size: 0.9em;
    }
</style>

<body >

<div class="center"  style="margin: 0.2cm 0 1cm 0;"><b>UMOWA CESJI PRAW</b></div>

<p>Zawarta w dniu {{date("d.m.Y")}} r. we Wrocławiu, pomiędzy:</p>

<p><strong>{{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)->first()->value : '---'}}</strong> z siedzibą we Wrocławiu, {{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()->value  : '---'}}, wpisaną do Rejestru Przedsiębiorców Krajowego Rejestru Sądowego prowadzonego przez Sąd Rejonowy dla Wrocławia - Fabrycznej we Wrocławiu, VI Wydział Gospodarczy Krajowego Rejestru Sądowego pod nr KRS: {{($owner->data()->where('parameter_id', 7)->first() ) ? $owner->data()->where('parameter_id', 7)->first()->value  : '---'}}, NIP: {{($owner->data()->where('parameter_id', 8)->first() ) ? $owner->data()->where('parameter_id', 8)->first()->value  : '---'}}, REGON: {{($owner->data()->where('parameter_id', 15)->first() ) ? $owner->data()->where('parameter_id', 15)->first()->value  : '---'}}, kapitał zakładowy w wysokości {{($owner->data()->where('parameter_id', 9)->first() ) ? $owner->data()->where('parameter_id', 9)->first()->value  : '---'}} opłacony w całości, reprezentowaną przez: <br><br><br>
    ........................................................................,<br>
    zwanego dalej Cedentem <br>
    a <br>
    <b>{{count($branch) ? $branch->company->name : '---'}}</b> {{count($branch) ? $branch->company->service_cession_data : ''}}, reprezentowaną przez: <br><br><br>
    ........................................................................,<br>
    zwanego dalej Cesjonariuszem
</p>

<div class="center"><b>§ 1</b></div>
<p>Cedent oświadcza iż: <br>
    a)	zawarł leasingobiorcą (,,Korzystający'') umowę leasingu operacyjnego której przedmiotem jest pojazd <b>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}</b> nr rej. <b>{{ $injury->vehicle->registration }}</b>, <br>
    b)	w dniu <b>{{date("d.m.Y", strtotime($injury->date_event))}} r.</b> na Pojeździe wystąpiła szkoda częściowa, która została zgłoszona do <b>{{($injury->insuranceCompany) ? $injury->insuranceCompany->name : ''}}</b> ("Ubezpieczyciel") i zarejestrowana pod numerem <b>{{$injury->injury_nr}}</b>, <br>
    c)	w trakcie likwidacji szkody, Ubezpieczyciel z tytułu ubezpieczenia {{$injury->injuries_type()->first()->name}}, wypłacił odszkodowanie z tytułu tej szkody w kwocie: <b>{{$injury->injuryCessionAmount()->first() ? number_format($injury->injuryCessionAmount()->first()->paid_amount, 2, ",", " ") : '---'}} zł {{$injury->injuryCessionAmount()->first() ? Config::get('definition.compensationsNetGross.'.$injury->injuryCessionAmount()->first()->net_gross) : ''}}</b> tytułem naprawy. <br>
    Cesjonariusz oświadcza, że na zlecenie Korzystającego dokonał naprawy Pojazdu w sposób zapewniający jego pełną sprawność i potwierdza, że na podstawie upoważnienia Cedenta otrzymał od Ubezpieczyciela kwotę ww. odszkodowania na poczet należnego mu od Korzystającego wynagrodzenia za dokonaną naprawę Pojazdu. Cesjonariusz kwestionuje stanowisko Ubezpieczyciela i uważa, że odszkodowanie powinno zostać wypłacone w innej wysokości.
</p>

<div class="center" style="margin-bottom: 0;"><b>§ 2</b></div>
<p>
    1.	Cedent przelewa na rzecz Cesjonariusza wierzytelność w postaci prawa
    @if(in_array($injury->injuries_type_id, [2,5]))
        do odszkodowania za koszty naprawy Pojazdu, o którym mowa w §1 ust. 1a, przysługującego z tytułu szkody komunikacyjnej opisanej w § 1 ust.1b, ponad kwotę już wypłaconego odszkodowania, wskazanego w § 1 ust. 1c., tj. kwoty <b>{{$injury->injuryCessionAmount()->first() ? number_format($injury->injuryCessionAmount()->first()->fv_amount - $injury->injuryCessionAmount()->first()->paid_amount, 2, ",", " ") : '---'}} zł {{$injury->injuryCessionAmount()->first() ? Config::get('definition.compensationsNetGross.'.$injury->injuryCessionAmount()->first()->net_gross) : ''}}</b> wraz z odsetkami i innymi kosztami („Wierzytelność”). Dla uniknięcia wątpliwości Strony wskazują, że przelew wierzytelności obejmuje roszczenia przysługujące Cedentowi w stosunku do sprawcy szkody, jak i Ubezpieczyciela. <br>
    @else
        do dochodzenia roszczeń w stosunku do Ubezpieczyciela w ramach ubezpieczenia {{$injury->injuries_type()->first()->name}}, opisanego w § 1 ust. 1c powyżej, z tytułu szkody w Pojeździe, ponad kwotę już wypłaconego odszkodowania, wskazanego w § 1 ust. 1c., tj. kwoty <b>{{$injury->injuryCessionAmount()->first() ? number_format($injury->injuryCessionAmount()->first()->fv_amount - $injury->injuryCessionAmount()->first()->paid_amount, 2, ",", " ") : '---'}} zł {{$injury->injuryCessionAmount()->first() ? Config::get('definition.compensationsNetGross.'.$injury->injuryCessionAmount()->first()->net_gross) : ''}}</b> wraz z odsetkami i innymi kosztami („Wierzytelność”). <br>
    @endif
    2.	Cedent przelewa na Cesjonariusza Wierzytelność wraz z należnościami ubocznymi związanymi z nimi. <br>
    3.	Cesjonariusz oświadcza, iż przyjmuje przelew Wierzytelności.
</p>

<div class="center"><b>§ 3</b></div>
<p>
    1.	Cesjonariusz oświadcza, że jest świadom możliwości nieistnienia cedowanej Wierzytelności oraz że przyjmuje przelew na swoje wyłączne ryzyko i niebezpieczeństwo. <br>
    2.	Cesja jest dokonana z wyłączeniem rękojmi za wady prawne Wierzytelności. <br>
    3.	Cesjonariusz zrzeka się wobec Cedenta wszelkich ewentualnych roszczeń, związanych
    z niniejszą umową, w szczególności z tytułu nieistnienia Wierzytelności. <br>
    4.	Cedent nie ponosi wobec Cesjonariusza odpowiedzialności za wypłacalność Ubezpieczyciela
</p>

<div class="center"><b>§ 4</b></div>
<p>
    Cesjonariusz zobowiązuje się do niezwłocznego zawiadomienia Ubezpieczyciela o dokonaniu cesji poprzez przesłanie stosownego zawiadomienia.
</p>

<div class="center"><b>§ 5</b></div>
<p>
    1.	Cesjonariusz zrzeka się wszelkich ewentualnych roszczeń wobec Cedenta, związanych ze szkodą opisaną w § 1 ust. 1 lit. b i c, w tym związaną z ewentualnym brakiem uzyskania odszkodowania od Ubezpieczyciela. <br>
    2.	Cesjonariusz oświadcza, że niezależnie od tego, czy będzie dochodził Wierzytelności, będącej przedmiotem niniejszego przelewu, oraz niezależnie od wyniku ewentualnego procesu sądowego, nie będzie żądał ani od Cedenta ani od Korzystającego zapłaty za naprawę Pojazdu w wysokości wyższej niż już otrzymana zapłata (w wysokości odszkodowania już wypłaconego przez Ubezpieczyciela, o którym mowa w § 1 ust. 1 powyżej). Cesjonariusz zrzeka się niniejszym jakichkolwiek roszczeń z tego tytułu w stosunku do Cedenta i do Korzystającego. <br>
    3.	W przypadku, gdyby Ubezpieczyciel kiedykolwiek wystąpił przeciwko Cedentowi z roszczeniami, związanymi z przelewem, będącym przedmiotem niniejszej umowy, Cesjonariusz zobowiązuje się zwolnić Cedenta od tych roszczeń, a w przypadku poniesienia przez Cedenta jakichkolwiek kosztów w związku z tymi roszczeniami, Cesjonariusz zobowiązuje się do zwrotu Cedentowi wartości tych kosztów, niezależnie od swojej winy.
</p>

<div class="center"><b>§ 6</b></div>
<p>
    1. Cesjonariusz nie może żądać od Cedenta zwrotu wydatków poniesionych w celu dochodzenia Wierzytelności, w szczególności zwrotu wszelkich opłat sądowych
    i egzekucyjnych koniecznych dla dochodzenia roszczeń oraz kosztów zastępstwa procesowego w postępowaniu sądowym i egzekucyjnym, które Cesjonariusz ponosi we własnym zakresie. <br>
    2.	W przypadku gdy sprawa o zapłatę przed sądem zostanie przez Cesjonariusza przegrana, bez względu na przyczynę tej przegranej, ponosi on wszelkie obciążające go koszty procesu oraz koszty zastępstwa procesowego. <br>
    3.	W przypadku przegrania sprawy sądowej co do całości lub części Wierzytelności, z chwila uprawomocnienia się orzeczenia sądowego następuje zwrotne przelanie wierzytelności na Cedenta a Cedent oświadcza, że taką cesję zwrotną przyjmie.
</p>

<div class="center"><b>§ 7</b></div>
<p>
    Cesjonariusz zobowiązuje się realizować swoje prawa wynikające z niniejszej Umowy jedynie w zakresie niezbędnym do dochodzenia Wierzytelności.
</p>

<div class="center"><b>§ 8</b></div>
<p>
    1.	Niniejsza umowa sporządzona została w dwóch jednobrzmiących egzemplarzach po jednym dla każdej z stron. <br>
    2.	Umowa wchodzi w życie z dniem zawarcia.
</p>
<br><br><br>
<table style="width: 100%;">
    <tr>
        <td class="text-left" style="width: 60%;">
            ............................................<br />
            Cedent
        </td>
        <td class="text-left" style="width: 40%;">
            ............................................<br />
            Cesjonariusz
        </td>
    </tr>
</table>


</body>
</html>