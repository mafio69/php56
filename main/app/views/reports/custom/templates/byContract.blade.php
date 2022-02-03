<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    .thead-cell{
        text-align:center; font-weight: bold;
    }
</style>
<table >
    <tr>
        <td align="left"  colspan="22"  style="font-weight:bold; font-size:10px;">Raport przedmiotów umów wg {{ $term }}, {{ $from }} - {{ $to }}</td>
    </tr>
    <tr>
        <td align="left" colspan="22"></td>
    </tr>
    <tr>
        <td class="thead-cell">Lp</td>
        <td class="thead-cell">Nr umowy leasingu</td>
        <td class="thead-cell">Przedmiot</td>
        <td></td>
    </tr>
    @foreach($injuries as $k => $injury)
        <tr>
            <td>{{ ++$k }}</td>
            <td>{{ $injury->vehicle->nr_contract }}</td>
            <td>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) }} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model) }}</td>
            <td>{{ $injury->created_at }}</td>
        </tr>
    @endforeach
</table>
</html>