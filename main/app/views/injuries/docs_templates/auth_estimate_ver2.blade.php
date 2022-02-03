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
            @include('injuries.docs_templates.modules.insurance_company')

        </div>

        <?php
        if($injury->receive_id == 1 && !is_null($branch))
        {
            $company = $branch->company;
            $receiver = array(
                    'name' => $company->name,
                    'street' => $company->street,
                    'city' => $company->code.' '.$company->city
            );
        }else if($injury->receive_id == 3){
            $client = $injury->client()->first();
            $receiver = array(
                    'name' => $client->name,
                    'street' => $client->registry_street,
                    'city' => $client->registry_post.' '.$client->registry_city
            );
        }else{
            $receiver = array(
                    'name' => '',
                    'street' => '',
                    'city' => ''
            );
        }
        ?>

        <div style="margin-top: 50pt; font-size: 9pt; font-weight: bold;">
            <p>Dotyczy: szkoda komunikacyjna nr {{$injury->injury_nr}} z dnia {{ $injury->date_event }}</p>
            <p>Pojazd {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}; nr rejestracyjny {{ $vehicle->registration }}</p>
            <p>Nr umowy leasingu/pożyczki: {{ $vehicle->nr_contract }}</p>
        </div>

        <div style ="margin-top:30pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
            <p style="text-indent:30px;">
                {{ checkIfEmpty('1', $ideaA) }} z siedzibą we {{ checkIfEmpty('13', $ideaA) }} przy {{ checkIfEmpty('2', $ideaA) }} upoważnia firmę {{ $receiver['name'] }} z siedzibą przy {{ $receiver['street'] }} , {{ $receiver['city'] }}  do odbioru przyznanego odszkodowania.
            </p>
            <p style=" margin-top: 20px;">
                Wyrażamy zgodę na kosztorysowe rozliczenie szkody. Udziały własne zostały zniesione, prosimy o ich nie potrącanie. Niniejszym anulujemy poprzednie upoważnienie.
            </p>
            <p style="margin-top: 20px;">
                Upoważnienie dotyczy szkody częściowej.
            </p>
            <p style=" margin-top: 60px; font-weight: bold; ">
                Decyzję o wypłacie odszkodowania prosimy przesłać do:<br/>
                Dział Likwidacji Szkód<br/>
                {{ checkIfEmpty('1', $ideaA) }}<br/>
                {{ checkIfEmpty('2', $ideaA) }}<br/>
                {{ checkIfEmpty('3', $ideaA) }}{{ checkIfEmpty('13', $ideaA) }}<br/>
            </p>
            <p style=" margin-top: 40px; ">
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
