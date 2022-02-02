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
    @import url('https://fonts.googleapis.com/css?family=Fira+Sans:300i,400,700&display=swap');
    * {
        /*font-family: "Times New Roman", "Times", serif;*/
        /*font-family: Times-Roman, sans-serif;*/
        /*font-family: Courier, sans-serif;*/
        font-family: 'Fira Sans', sans-serif; 
        font-size: 11pt;
        text-align: justify;
        text-justify: inter-word;
    }

    p {
        font-size: 11pt;
        line-height: 1.1;
        margin-top: 5px;
    }

    b {
        font-size: 11pt;
    }
</style>


<body>

<div class="right">Wrocław, {{date("d.m.Y")}}<br><br><br><br>
</div>
<div class="left">
    <p>Dotyczy szkody nr {{$injury->injury_nr}}<br>
        z dnia {{$injury->date_event}}<br>
        na pojeździe 
        {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}}
        {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}
        {{ $vehicle->registration}}<br>
        Umowa nr: {{$vehicle->nr_contract}} .
        <br></p>
</div>
    
    
<div class="right">
    <span>
        {{$injury->insuranceCompany->name}}<br>
        {{$injury->insuranceCompany->street}}<br>
        {{$injury->insuranceCompany->post.' '.$injury->insuranceCompany->city}}<br><br><br><br><br>
    </span>
</div>
    

<div class="center" style="margin-bottom: 1cm;"><b style="font-size: 12pt">Upoważnienie do odbioru odszkodowania
    {{isset($inputs['description'])?'<br>'.$inputs['description']:'' }}
</b></div>

<p>
    {{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)->first()->value : '---'}}
    z siedzibą we
    Wrocławiu,
    przy {{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()->value: '---'}}
    upoważnia do odbioru przyznanego
    odszkodowania firmę:<br>
</p>

@if($injury->receive_id==1 && !is_null($branch))
    {{--            serwis--}}
    <div class="center"><span>{{$branch->short_name}}<br>
            {{$branch->street}}<br>
            {{$branch->code.' '.$branch->city}}
            <br><br></span></div>
@elseif($injury->receive_id==3 && !is_null($injury->client))
    {{--            leasingobiorca--}}
    <div class="center"><span>{{$injury->client->name}}<br>
            {{$injury->client->registry_street}}<br>
            {{$injury->client->registry_post.' '.$injury->client->registry_city}}
            <br><br></span></div>
@endif

<p>
    Nie wyrażamy zgody na kosztorysowe rozliczenie szkody.<br>
    Wypłata odszkodowania powinna nastąpić na podstawie faktur potwierdzających naprawę pojazdu.<br>
    Faktury powinny zostać wystawione na Korzystającego.<br>
    Wynikające z warunków ubezpieczenia różnice pomiędzy wartością złożonych faktur, a kwotą należnego odszkodowania
    pokrywa Leasingobiorca.<br><br>

    <span style="font-size: 11pt; text-decoration: underline">Upoważnienie dotyczy szkody częściowej.</span><br><br></p>

<i style="font-size: 10pt;">Jednocześnie informujemy, że w przypadku zakwalifikowania
        szkody jako
        całkowitej,
        uprawnionym do
        odbioru
        odszkodowania pozostaje wyłącznie {{$owner->name.', '.$owner->street.', '.$owner->post.' '
                .$owner->city}}.</i> <br><br>
<p>
    Uwaga:<br>
    Przyznane odszkodowanie może służyć wyłącznie likwidacji szkody i nikt nie jest uprawniony do jego wypłaty osobom
    trzecim.<br><br>

    Zawiadomienie o wypłacie przyznanego odszkodowania proszę przesłać na adres:<br>
    <a style="text-decoration: underline">szkody@ideagetin.pl</a>

</p>

<table style="width: 100%; font-size: 9pt; font-weight:normal;  ">
    <tbody>
    <tr>
        <td style="width:50%; "></td>
        <td style="text-align:center;">Z poważaniem</td>
    </tr>
    <tr>
        <td style="width:50%; "></td>
        <td style="text-align:center;">
            @include('modules.signatures')
        </td>
    </tr>
    </tbody>

</table>

</body>
</html>