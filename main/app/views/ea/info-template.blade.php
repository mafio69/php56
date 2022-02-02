<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
</head>
<body style="margin: 1cm 0;">

<h3>Dane zgłoszenia {{ $eaInjury->case_number }}</h3>
<div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
    <table cellspacing="0" cellpadding="0" style="width: 50%">
        <thead>
            <th colspan="2" style="text-align: center;">Dane pojazdu z EA:</th>
        </thead>
        <tr>
            <td style="font-weight: bold;">Nr VIN:</td>
            <td>
               {{ $eaInjury->vehicle_vin }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nr rej.:</td>
            <td>
                {{ $eaInjury->vehicle_registration }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Marka:</td>
            <td>
                {{ $eaInjury->vehicle_brand }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Model:</td>
            <td>
                {{ $eaInjury->vehicle_model }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pojemnoś silnika:</td>
            <td>
                {{ $eaInjury->vehicle_engine_capacity }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Rok produkcji:</td>
            <td>
                {{ $eaInjury->vehicle_year_production }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Data pierwszej rejestracji:</td>
            <td>
                {{ $eaInjury->vehicle_first_registration }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Przebieg:</td>
            <td>
                {{ $eaInjury->vehicle_mileage }}
            </td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="0" style="width: 50%">
        <thead>
        <th colspan="2" style="text-align: center;">Dane właściciela i klienta z EA:</th>
        </thead>
        <tr>
            <td style="font-weight: bold;">Nazwa właściciela:</td>
            <td>
                {{ $eaInjury->owner_name }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nazwa klienta:</td>
            <td>
                {{ $eaInjury->client_name }}
            </td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="0" style="width: 50%">
        <thead>
        <th colspan="2" style="text-align: center;">Status umowy z EA:</th>
        </thead>
        <tr>
            <td style="font-weight: bold;">Nr umowy:</td>
            <td>
                {{ $eaInjury->contract_number }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Data końca umowy:</td>
            <td>
                {{ $eaInjury->contract_end_leasing }}
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Status umowy:</td>
            <td>
                {{ $eaInjury->contract_status }}
            </td>
        </tr>
    </table>
</div>



</body>
</html>
