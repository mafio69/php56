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
        font-family: Lato, sans-serif;
        font-size: 11pt;
        line-height: 1.5;
        text-align: justify;
        text-justify: inter-word;
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

<div class="center" style="margin-bottom: 1cm;"><b>UMOWA CESJI PRAW Z POLISY UBEZPIECZENIOWEJ</b></div>

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
    opłacony w całości, <br>reprezentowaną przez: <br><br><br>
       {{-- {{Auth::user()->name}} --}}
       <br>
    ........................................................................<br>
    Pełnomocnik<br><br>
    zwaną dalej <b>Cedentem</b> <br>
    <br>
    <b>{{count($injury->client) ? $injury->client->name : '---'}}</b><br>
    <b>{{count($injury->client) ? $injury->client->registry_street : ''}}</b><br>
    <b>
        {{count($injury->client) ? $injury->client->registry_post.' '.$injury->client->registry_city: '---'}}<br>
        NIP: {{count($injury->client) ? $injury->client->NIP : '---'}},</b><br> reprezentowaną przez: <br><br>
    ........................................................................<br>
    zwanym dalej <b>Cesjonariuszem</b>
</p>

<p>O następującej treści<br><br></p>
<div class="center"><b>§ 1</b></div>
<p style="margin-bottom: 1.0cm">
    <ol>
    <li>Przedmiotem niniejszej umowy, jest
    nieodpłatne
    przeniesienie
    przez CEDENTA na CESJONARIUSZA praw wynikających z umowy ubezpieczenia zawartej przez CEDENTA z Towarzystwem
    Ubezpieczeniowym {{$injury->insuranceCompany->name}} (zwanym dalej Ubezpieczycielem), celem dochodzenia
    roszczeń z tytułu
    likwidacji szkody przedmiotu, ubezpieczonego zgodnie z polisą numer {{$inputs['nr_policy']}}
    powstałej w dniu {{$injury->date_event}}
    szkody nr {{$injury->injury_nr}} na pojeździe marki 
    {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
    numer rej.
    {{$vehicle->registration}}
    Będącym
    przedmiotem
    zawartej pomiędzy {{($owner->data()->where('parameter_id', 1)->first() ) ?$owner->data()->where('parameter_id', 1)
            ->first()->value:'---'}} a firmą
    , umowy leasingu {{$vehicle->nr_contract}}.</li></p>
<p><li>Zawarcie niniejszej umowy w żaden sposób nie wpływa na roszczenia CEDENTA wobec CESJONARIUSZA wynikające z
    zawartej pomiędzy nimi umowy leasingu, o której mowa w ust. 1, w szczególności nie zwalnia CESJONARIUSZA z
    obowiązków z niej wynikających.</li></p>
</ol>
<div class="center"><b>§ 2</b></div>
<p>
    Wszelkie koszty wynikające z dochodzenia roszczeń z tytułu likwidacji szkody, w tym w szczególności koszty sądowe,
    koszty zastępstwa procesowego obciążają wyłącznie CESJONARIUSZA bez względu na wynik sprawy.
</p>

<div class="center"><b>§ 3</b></div>
<p>
    CESJONARIUSZ przyjmuje przelew praw oraz kopię POLISY numer {{$inputs['nr_policy']}}.
</p>

<div class="center"><b>§ 4</b></div>
<p>
    W sprawach nieuregulowanych niniejszą umową mają zastosowanie odpowiednie przepisy Kodeksu Cywilnego.
</p>

<div class="center"><b>§ 5</b></div>
<p>
    CEDENT w terminie 7 dni od daty podpisania niniejszej umowy powiadomi o tym fakcie UBEZPIECZYCIELA.
</p>

<div class="center"><b>§ 6</b></div>
<p>Umowa została sporządzona w dwóch jednobrzmiących egzemplarzach, po jednym dla każdej ze Stron.</p>

<table style="width: 100%; margin-top: 1.0cm">
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