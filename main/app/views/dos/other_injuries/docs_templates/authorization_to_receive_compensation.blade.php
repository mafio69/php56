<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{public_path()}}/templates-src/css/notification.css" rel="stylesheet">
    <title></title>
    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
        }
        .page {
            background-color: white;
            padding: 70px 20px 20px 20px;
        }

        @page {
            margin: 0.35in 0.35in 0.35in 0.35in;
        }
    </style>
</head>
<body>
@include('dos.other_injuries.docs_templates.modules.header')
@include('dos.other_injuries.docs_templates.modules.footer')
<div id="body">
    <div class="page"  id="content">
        @include('dos.other_injuries.docs_templates.modules.place')

        @include('dos.other_injuries.docs_templates.modules.insurance_company')

        <p style="font-weight: bold; margin-top: 50px; font-size:9pt; line-height: 1.2;">
            Dotyczy: szkoda numer {{ $injury->injury_nr }} z dnia {{ $injury->date_event }}.
            <br>
            Przedmiot: {{ $injury->object->description }}.
            <br>
            nr umowy leasingu/pożyczki: {{ $injury->object->nr_contract }}.
        </p>

        <p style="text-align: center; margin-top: 50px; font-weight: bold; font-size:9pt;">
            Upoważnienie
        </p>

        <p style="margin-top: 30px; font-size:9pt; line-height: 1.2;">
            Idea Getin Leasing S.A. z siedzibą we Wrocławiu przy ul. Strzegomskiej 42b będąca właścicielem w/w przedmiotu upoważnia
            <br>
            @if($injury->receive_id == 2)
                {{ $injury->object->owner->name }}
                <br>
                {{ $injury->object->owner->street }}
                <br>
                {{ $injury->object->owner->post }} {{ $injury->object->owner->city }}
            @elseif($injury->receive_id == 3)
                {{ $injury->client->name }}
                <br>
                {{ $injury->client->correspond_street }}
                <br>
                {{ $injury->client->correspond_post }} {{ $injury->client->correspond_city }}
            @else
                {{ $injury->receiver_name }}
                <br>
                {{ $injury->receiver_address }}
            @endif
            <br>
            do odbioru przyznanego odszkodowania.
        </p>

        <p style="margin-top: 30px; font-size:9pt; line-height: 1.2;">
            Odszkodowanie powinno zostać przekazane na podstawie faktur potwierdzających naprawę w/w przedmiotu.
        </p>

        <p style="margin-top: 30px; font-size:9pt; line-height: 1.2;">
            <span style="border-bottom: 1px solid black;">
                Nie wyrażamy zgody na kosztorysowe rozliczenie szkody.
            </span>
            <br>
            Upoważnienie dotyczy szkody częściowej.
        </p>

        <p style="margin-top: 30px; font-weight: bold; line-height: 1.5; font-size:9pt;">
            Decyzję o wypłacie odszkodowania prosimy przesłać do:
            <br>
            Dział Likwidacji Szkód
            <br>
            Idea Getin Leasing S.A.
            <br>
            Ul. Strzegomska 42b
            <br>
            53-611 Wrocław
        </p>

        <p style="margin-top: 50px; font-size:9pt; line-height: 1.2;">
            W razie jakichkolwiek wątpliwości prosimy o kontakt telefoniczny pod nr tel. 71 33 44 807 lub na adres majatek@cas-auto.pl
        </p>

        <table style="width: 100%;  font-weight:normal; margin-top:15px; ">
            <tbody>
            <tr >
                <td style="width:50%; "></td>
                <td style="text-align:center;">Z poważaniem</td>
            </tr>
            <tr >
                <td style="width:50%; "></td>
                <td style="text-align:center;" >
                    @include('modules.signatures-dompdf')
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
