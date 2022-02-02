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
<?php $vehicle = $injury->vehicle()->first();?>
<div id="body">

    <div class="page"  id="content">

        <div style="font-size: 7pt;">

            <table style="width: 100%; font-size: 9pt; font-weight:normal; ">
                <tr>
                    <td style="text-align: left;"><i>{{ checkIfEmpty('13', $ideaA) }}, dnia {{date('d-m-Y')}}</i></td>
                </tr>
            </table>

            <table style=" width: 100%; font-size:9pt; margin-top:30pt; font-weight:bold;">
                <tr>
                    <td style="text-align: right;">{{ checkIfEmpty('1', $ideaA) }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">{{ checkIfEmpty('2', $ideaA) }}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">{{ checkIfEmpty('13', $ideaA) }}</td>
                </tr>
            </table>

        </div>

        <div style="margin-top: 50pt; font-size: 9pt; line-height: 10pt; ">
            <p>
                Dział Księgowości<br/>
                Dział Ubezpieczeń
            </p>
            <p style="max-resolution: 20px;">
                Dotyczy: Szkoda całkowitej: {{$injury->injury_nr}} o nr rej. {{ $vehicle->registration }}
            </p>
        </div>

        <div style ="margin-top:40px; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p>
                Leasingobiorca: {{ $injury->client->name }}<br/>
                Umowa Leasingu: {{ $vehicle->nr_contract }}<br/>
                Data zdarzenia: {{ $injury->date_event }}
            </p>
            <p style="margin-top: 40px;">
                Witam,
            </p>
            <p style="text-indent:30px;  margin-top: 20px; ">
                Informujemy, iż w dniu {{ $inputs['broker_date'] }} firma {{ Config::get('definition.broker_data.name') }} została poinformowana przez Ubezpieczyciela {{ $injury->insuranceCompany->name }} o zakwalifikowaniu szkody jako całkowitej.<br/>
                Prosimy o zablokowanie umowy leasingu/pożyczki.
            </p>
            <p style=" margin-top: 40px;">
                W załączeniu przesyłam ofertę na zakup pozostałości. <b>Oferta ważna do dnia {{ $inputs['expire_date'] }}.</b>
            </p>
            <p style="margin-top: 40px;">
                <table style=" width: 100%; ">
                    <tr>
                        <td style="text-align: left; width: 40%;">Wartość pojazdu przed zdarzeniem:</td>
                        <td style="text-align: left;">{{ $inputs['value_before'] }} netto(brutto)</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 40%;">Wartość pojazdu po zdarzeniu:</td>
                        <td style="text-align: left;">{{ $inputs['value_after'] }} netto(brutto)</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 40%;">Wysokość odszkodowania:</td>
                        <td style="text-align: left;">{{ $inputs['value_compensation'] }} netto(brutto)</td>
                    </tr>
                </table>
            </p>
        </div>
        <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:25px; ">
            @include('injuries.docs_templates.modules.regards')
        </table>
    </div>
</div>

</body>
</html>
