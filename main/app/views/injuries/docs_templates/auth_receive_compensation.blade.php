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
    <?php
    if($injury->receive_id == 1 && !is_null($branch)){
        $a_name = $branch->company->name;
        $a_street = $branch->street;
        $a_address = $branch->code.' '.$branch->city;
    }else if($injury->receive_id == 3){
        $a_name = $injury->client->name;
        $a_street = $injury->client->registry_street;
        $a_address = $injury->client->registry_post.' '.$injury->client->registry_city;
    }else{
        $a_name = $a_street = $a_address = '---';
    }
    ?>
    <div class="page"  id="content">

        <div style="font-size: 7pt; width: 100%;">

            @include('injuries.docs_templates.modules.place')
            @include('injuries.docs_templates.modules.insurance_company')

        </div>
        <?php $vehicle = $injury->vehicle()->first();?>
        <div style="margin-top: 40pt; font-size: 9pt; font-weight: bold">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }} nr umowy leasingu {{ $vehicle->nr_contract }}</p>
        </div>

        <div>
            <table style="font-size:9pt; margin-top:30pt; font-weight:bold;" align="center">
                <tr>
                    <td style="text-align:center;">Upoważnienie</td>
                </tr>
            </table>
        </div>

        <div style ="margin-top:10pt; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p style="text-indent:30px;">
                {{ checkIfEmpty('1', $ideaA) }} z siedzibą we {{ checkIfEmpty('13', $ideaA) }} przy {{ checkIfEmpty('2', $ideaA) }} upoważnia firmę {{$a_name}} z siedzibą przy ul. {{$a_street}} , {{$a_address}} do odbioru przyznanego odszkodowania.
            </p>
            <p >
                Odszkodowanie powinno zostać przekazane na podstawie faktur potwierdzających naprawę w/w pojazdu.
            </p>
            <p style="margin-top: 20px;">
                Nie wyrażamy zgody na kosztorysowe rozliczenie szkody. Udziały własne zostały wykupione,&nbsp;&nbsp;prosimy o ich nie potrącanie.
            </p>
            <p style=" margin-top: 20px;">
                Upoważnienie dotyczy szkody częściowej.
            </p>
            <p style="margin-top: 10px;">
                {{ checkIfEmpty('description', $inputs) }}
            </p>
            <p style=" margin-top: 40px; font-weight: bold;">
                Decyzję o wypłacie odszkodowania prosimy przesłać do: Dział Likwidacji Szkód<br/>
                {{ checkIfEmpty('1', $ideaA) }} {{ checkIfEmpty('2', $ideaA) }}<br/>
                {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}<br/>
            </p>
            <p style=" margin-top: 40px; ">
                W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. {{ checkIfEmpty('5', $ideaA) }} lub na adres {{ checkIfEmpty('4', $ideaA) }}
            </p>
        </div>
        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>

    </div>
</div>

</body>
</html>
