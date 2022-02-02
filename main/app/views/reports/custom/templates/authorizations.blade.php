<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

    <table >
        <tr>
            <td></td>
            <td colspan="10"  class="thead-cell" style="font-size:10px; text-align:center; font-weight: bold; background-color: #99CCFF;">OPŁATA ZA WYSTAWIENIE UPOWAŻNIENIA (UBEZP. PAKIET.) OD {{ $from }} DO {{ $to }}</td>
        </tr>
        <tr>
            <td colspan="11"></td>
        </tr>
        <tr>
            <Td></Td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Lp</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Nr umowy leasingu</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Typ upoważnienia</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Numer rejestracyjny pojazdu</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Nazwa klienta</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Adres klienta</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">nr szkody ZU</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Typ szkody</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Data wysłania upoważnienia do odbioru odszkodowania</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Kwota obciążenia za upoważnienie do wypłaty</td>
            <td style="text-align:center; font-weight: bold; background-color: #99CCFF;">Data szkody</td>
        </tr>
        <?php $amount = 0;?>
        @foreach($authorizations as $k => $authorization)
            <tr>
                <td></td>
                <td>{{ ++$k }}. </td>
                <td>{{ $authorization->injury->vehicle->nr_contract }}</td>
                <td>{{ $authorization->document_type->name }}</td>
                <td>{{ $authorization->injury->vehicle->registration }} </td>
                <td>{{ $authorization->injury->client->name }}</td>
                <td>{{ $authorization->injury->client->registry_post }} {{ $authorization->injury->client->registry_city }}, {{ $authorization->injury->client->registry_street }}</td>
                <td>{{ $authorization->injury->injury_nr }}</td>
                <td>{{ $authorization->injury->injuries_type->name }}</td>
                <Td>{{ substr($authorization->created_at, 0, -3) }}</Td>
                <td>
                    @if($authorization->injury->issue_fee > 1)
                        {{ $authorization->injury->issue_fee }}
                        <?php $amount += $authorization->injury->issue_fee; ?>
                    @else
                        0
                    @endif
                </td>
                <td>{{ $authorization->injury->date_event }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4"></td>
            <td colspan="6" style="background-color: #00CCFF; font-weight: bold;">ŁĄCZNA KWOTA</td>
            <td style="background-color: #00CCFF; font-weight: bold;">{{ $amount }}</td>
        </tr>
    </table>
</body>
</html>