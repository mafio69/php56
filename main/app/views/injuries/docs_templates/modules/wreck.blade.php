@if($injury->wreck)
<table style=" width: 100%; font-size:10pt; margin-top:30pt; font-weight:normal;">

    <tr>
        <td style="text-align: right;">{{$injury->wreck->buyer_name}}</td>
    </tr>
    <tr>
        <td style="text-align: right;">{{$injury->wreck->buyer_address_street}}</td>
    </tr>
    <tr>
        <td style="text-align: right;">{{$injury->wreck->buyer_address_code.' '.$injury->wreck->buyer_address_city}}</td>
    </tr>

</table>
@endif
