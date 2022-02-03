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

    @page{
        margin: 2cm;
    }
     @import url('https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700&display=swap');

    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: Lato, sans-serif;
        font-size: 11pt;
        line-height: 1.5;
        text-align: justify;
        text-justify: inter-word;
        text-decoration: none
    }

    p {
        margin-top: 5px;
        font-size: 11pt;
    }

    p strong {
        font-size: 11pt;
    }

    p b {
        font-size: 11pt;
    }

    td {
        font-size: 11pt;
    }
    ol {
        padding-left: 17px;
    }
    li {
        text-decoration: none;
    }
</style>
<body>

<div class="center" style="margin-bottom: 1cm;"><b>UMOWA O PRZELEW WIERZYTELNOŚCI</b></div>

<p>Zawarta w dniu {{date("d.m.Y")}} r. we Wrocławiu, pomiędzy:</p>


<p>
    <b>{{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)->first()->value : '---'}}</b>
    z siedzibą we
    Wrocławiu, {{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()->value  : '---'}}
    , wpisaną do Rejestru Przedsiębiorców Krajowego Rejestru Sądowego prowadzonego przez Sąd Rejonowy dla Wrocławia -
    Fabrycznej we Wrocławiu, VI Wydział Gospodarczy Krajowego Rejestru Sądowego pod nr
    KRS: {{($owner->data()->where('parameter_id', 7)->first() ) ? $owner->data()->where('parameter_id', 7)->first()->value  : '---'}}
    ,
    NIP: {{($owner->data()->where('parameter_id', 8)->first() ) ? $owner->data()->where('parameter_id', 8)->first()->value  : '---'}}
    ,
    REGON: {{($owner->data()->where('parameter_id', 15)->first() ) ? $owner->data()->where('parameter_id', 15)->first()->value  : '---'}}
    , kapitał zakładowy w
    wysokości {{($owner->data()->where('parameter_id', 9)->first() ) ? $owner->data()->where('parameter_id', 9)->first()->value  : '---'}}
    opłacony w całości, reprezentowaną przez: <br><br>
       {{-- {{Auth::user()->name}} --}}
       <br>
    ........................................................................<br>
        Pełnomocnik
        zwaną dalej <b>Cedentem</b> <br>
    <br>
    <b>{{count($injury->client) ? $injury->client->name : '---'}}</b><br>
    {{count($injury->client) ? $injury->client->registry_street : ''}}<br>
    <b>
        {{count($injury->client) ? $injury->client->registry_post.' '.$injury->client->registry_city: '---'}}<br>
        NIP: {{count($injury->client) ? $injury->client->NIP : '---'}},</b><br> reprezentowaną przez: <br><br>
    <b>........................................................................<br>
        zwanego dalej Cesjonariuszem</b>
</p>

<p>O następującej treści<br></p>
<div class="center"><b>§ 1</b></div>

<ol>
<li>Cedent oświadcza, że jest właścicielem pojazdu marki 
    {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
    
     o numerze rejestracyjnym
    {{$vehicle->registration}}, zwanego dalej Pojazdem.</li>
    <li>Strony oświadczają, że zawarły umowę leasingu nr {{$vehicle->nr_contract}} na podstawie której
    Cedent oddał
    Cesjonariuszowi
    Pojazd do
    używania.</li>
    <li>Cedent oświadcza, że Pojazd w dniu {{$injury->date_event}} został uszkodzony wskutek wypadku komunikacyjnego
    spowodowanego
    przez kierującego pojazdem ubezpieczonym od odpowiedzialności cywilnej przez
    {{$injury->insuranceCompany->name}}
    (dalej zwany
    Zakładem
    Ubezpieczeń).</li>
    <li>Cesjonariusz oświadcza, że Zakład Ubezpieczeń prowadzi postępowanie likwidacyjne szkody powstałej w związku z
    wypadkiem z dnia {{$injury->date_event}} oznaczone numerem {{$injury->injury_nr}}.</li>
</ol>

<div class="center"><b>§ 2</b></div>
<p>
    Strony wskazują, że przedmiotem niniejszej umowy są wszelkie wierzytelności, jakie Cedent ma w stosunku do Zakładu
    Ubezpieczeń z tytułu uszkodzenia Pojazdu wskutek wypadku komunikacyjnego z dnia {{$injury->date_event}}
    spowodowanego przez kierującego pojazdem ubezpieczonym od odpowiedzialności cywilnej przez Zakład Ubezpieczeń, w tym
    w szczególności wierzytelności z tytułu należnego Cedentowi od Zakładu
    Ubezpieczeń: {{$injury->insuranceCompany->name}} odszkodowania za uszkodzenie Pojazdu oraz wierzytelności powstałe w
    związku z przeprowadzonym przez Zakład Ubezpieczeń postępowaniem likwidacyjnym o numerze {{$injury->injury_nr}}
    (dalej zwane Wierzytelnościami).
</p>

<div class="center"><b>§ 3</b></div>
<ol>
    <li>Cedent oświadcza, że niniejszym przenosi nieodpłatnie na Cesjonariusza, a Cesjonariusz oświadcza, że
    dokonany
    przez Cedenta akt cesji przyjmuje.</li>
    <li>Cedent oświadcza, że dokonując cesji Wierzytelności, przenosi na Cesjonariusza również prawo do
    dochodzenia
    przez Nabywcę we własnym imieniu i na własny rachunek od Zakładu Ubezpieczeń tych Wierzytelności, w tym
    prawo
    dochodzenia odszkodowania za uszkodzenie Pojazdu.</li>
    <li>Zawarcie niniejszej umowy w żaden sposób nie wpływa na roszczenia Cedenta wobec Cesjonariusza wynikające z
    zawartej pomiędzy nimi umowy leasingu, o której mowa w § 1 ust. 2, w szczególności nie zwalania
    Cesjonariusza z
    obowiązków z niej wynikających.</li>
    <li>Wszelkie koszty wynikające z dochodzenia roszczeń z tytułu likwidacji szkody, w tym w szczególności koszty
    sądowe, koszty zastępstwa procesowego obciążają wyłącznie Cesjonariusza bez względu na wynik sprawy.</li>
</ol>
<div class="center"><b>§ 4</b></div>
<ol>
<li>W kwestiach nieuregulowanych niniejszą umową zastosowanie mają przepisy prawa polskiego, a w szczególności
    ustawy
    Kodeks cywilny.</li>
<li>Spory wynikłe na tle wykonania niniejszej umowy podlegają rozpatrzeniu przez właściwy miejscowo i rzeczowo
    sąd powszechny.</li>
<li>Zmiany umowy wymagają formy pisemnej pod rygorem nieważności.</li>
<li>Umowę sporządzono w dwóch jednobrzmiących egzemplarzach, po jednym dla każdej ze stron.</li></ol>

<br><br><br><br><br>
<table style="width: 100%;">
    <tr>
        <td class="text-left" style="width: 60%;">
            ............................................<br/>
            Cedent
        </td>
        <td class="text-right" style="width: 40%;">
            ............................................<br/>
            Cesjonariusz
        </td>
    </tr>
</table>


</body>
</html>