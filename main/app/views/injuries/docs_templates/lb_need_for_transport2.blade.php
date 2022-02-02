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
</style>
<body>
<div class="body content body-margin-big">
    <div class="t-body t-body-size-16">
        <p class=" text-right" style="margin-top: 0px;">{{ checkIfEmpty('13', $ideaA) }}, {{date('d-m-Y')}}</p><br />

        <table class="letter">
            <tr>
                <td></td>
                <td style="width: 9cm; height: 4.5cm; text-align: right; vertical-align: middle;">
                    <span style="padding-right: 40px;">Sz. P.</span><br/>
                    {{ ($injury->client) ? $injury->client->name : '....................' }}<br />
                    {{ ($injury->client) ? $injury->client->correspond_street : '....................' }}<br />
                    {{ ($injury->client) ? $injury->client->correspond_post : '....................' }} {{ ($injury->client) ? $injury->client->correspond_city : '' }}
                </td>
            </tr>
        </table>

        <h4 class="text-center">DOTYCZY POJAZDU: {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}} nr rej.: {{ $injury->vehicle->registration }}<br />
            umowa nr.: {{ $injury->vehicle->nr_contract }}; typ szkody: {{ $injury->injuries_type()->first()->name }};  nr szkody: {{ $injury->injury_nr }}
        </h4>

        <p class="small-block text-justify" >Szanowni Państwo,<br /><br />
            <span style="font-size: 0.8em;">w związku z w/w szkodą komunikacyjną, zakwalifikowaną jako całkowita, {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}, będąc właścicielem przedmiotowego pojazdu, zwraca się z prośbą o <b>dostarczenie do naszej dyspozycji</b> pozostałości przedmiotu leasingu zgodnie z § 25 pkt. 3 Ogólnych Warunków Umowy Leasingu (OWUL). Wraz z pojazdem, prosimy o dostarczenie wszystkich kompletów kluczy oraz wszelkiej dokumentacji pojazdu. Termin dostawy prosimy ustalić z naszymi kooperantami (numery telefonów poniżej).</span>
        </p>

        <p class="small-block" >
            <span style="font-size: 0.8em;">Pojazd prosimy dostarczyć w terminie do  {{ $inputs['sending_date'] }}  r. </span>
        </p>

        <p class="small-block" >
            <span style="font-size: 0.8em;">
                <b>Wszystkie pojazdy prosimy dostarczać na adres:</b><br />
                ul. Fabryczna 24, 55-080 Pietrzykowice,<br>tel. 667-892-806, 71 77-88-950 (pojazdy osobowe)<br> tel.603-197-038, 601-150-428 (pojazdy ciężarowe) <br>
                lub <br>FAMAT Słomczyn 70, 05-600 Grójec, tel.: 516-579-714 lub 882-55-33-14
            </span>
        </p>


        <p class="small-block" >
            <span style="font-size: 0.8em;">
                Zgodnie z OWU - niewykonanie obowiązku zwrotu pojazdu spowoduje, że przejęcie i zagospodarowanie pozostałości przedmiotu leasingu, nastąpi na Państwa koszt, co obejmie wszelkie ewentualne koszty z tym związane.
            </span>
        </p>

        <p>
            <span style="font-size: 0.8em;">
                Niezależnie od powyższego, w przypadku braku reakcji z Państwa strony, narażą się Państwo na odpowiedzialność odszkodowawczą z tytułu bezumownego korzystania z rzeczy.
            </span>
        </p>


        <table style="width: 100%; font-size: 0.8em !important; font-weight:normal; margin-top:0px; ">
            <tbody>
            <tr >
                <td style="width:50%; "></td>
                <td style="text-align:center; "><span style="font-size: 0.8em !important;">Z poważaniem</span></td>
            </tr>
            <tr >
                <td style="width:50%; "></td>
                <td style="text-align:center;" >
                    @include('modules.signatures_small')
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
