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
    <?php $vehicle = $injury->vehicle()->first();?>
    <div class="page"  id="content" style="margin-top: 30px;">

        <table style="width: 100%; font-size: 9pt; font-weight:bold; ">
            <tr>
                <td style="text-align: center;">Informacje o uszkodzonym pojeździe</td>
            </tr>
            <tr>
                <td style="text-align: center; padding-top: 30px;">Zlecenie dla przewoźnika</td>
            </tr>
        </table>


        <div style ="margin-top:60px; font-size:9pt; text-align:justify;text-justify:inter-word;  line-height: 10pt;">
            <p>
                data przekazania informacji {{ $inputs['info_date'] }}
            </p>
            <table style="width: 100%; font-size: 9pt; margin-top:40px;" class="bordered-all pad-medium" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="middle" style="width: 30px; text-align: center;">1</td>
                    <td class="middle" style="width: 40%;">nr umowy</td>
                    <td class="middle">{{ $vehicle->nr_contract }}</td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">2</td>
                    <td class="middle" style="width: 40%;">typ samochodu i rodzaj uszkodzenia</td>
                    <td class="middle">
                        {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}<br/>
                        @if($injury->remarks != 0)
                            {{ $injury->getRemarks->content }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">3</td>
                    <td class="middle" style="width: 40%;">miejsce postoju pojazdu i jego stan prawny</td>
                    <td class="middle">{{ $inputs['vehicle_location'] }}</td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">4</td>
                    <td class="middle" style="width: 40%;">dodatkowe koszty związane z naprawą, parkingiem, itp. oraz sposób zapłaty</td>
                    <td class="middle">{{ $inputs['extra_costs'] }}</td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">5</td>
                    <td class="middle" style="width: 40%;">kontakt z klientem lub osobą uprawnioną do wydania pojazdu</td>
                    <td class="middle">
                        {{ $vehicle->client->name }}<br/>
                        {{ $vehicle->client->phone }}<br/>
                        {{ $vehicle->client->email }}<br/>
                    </td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">6</td>
                    <td class="middle" style="width: 40%;">termin odbioru pojazdu</td>
                    <td class="middle">{{ $inputs['receive_date'] }}</td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">7</td>
                    <td class="middle" style="width: 40%;">miejsce przechowywania dowodu rejestracyjnego oraz kluczyków do pojazdu</td>
                    <td class="middle">{{ $inputs['documents_location'] }}</td>
                </tr>
                <tr>
                    <td class="middle" style="width: 30px;text-align: center;">8</td>
                    <td class="middle" style="width: 40%;">orientacyjne koszty transportu</td>
                    <td class="middle">{{ $inputs['transport_costs'] }}</td>
                </tr>
            </table>
        </div>

    </div>
</div>

</body>
</html>
