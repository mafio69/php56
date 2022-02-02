<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px;">
    <tr>
        <td >Dotyczy szkody nr {{$injury->injury_nr}}</td>
    <tr>
        <td >z dnia {{$injury->date_event}}</td>
    </tr>
    <tr>
        <td >na pojeździe {{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}, {{$injury->vehicle->registration}}</td>
    </tr>
    <tr>
        <td >Umowa nr: {{$injury->vehicle->nr_contract}}</td>
    </tr>

</table>