<table style=" width: 100%; font-size:10pt; margin-top:50pt; font-weight:normal;">
    @if($branch && $branch->id != NULL && $branch->id > 0)
        <tr>
            <td style="width:50%;"></td>
            <td style="text-align: center;">{{$branch->company->name}}</td>
        </tr>
        <tr>
            <td style="width:50%;"></td>
            <td style="text-align: center;">{{$branch->street}}</td>
        </tr>
        <tr>
            <td style="width:50%;"></td>
            <td style="text-align: center;">{{$branch->code}} {{$branch->city}}</td>
        </tr>
    @else
        <tr>
            <td style="width:50%;"></td>
            <td style="text-align: center;"><span style="color:red;"><i>nie przypisano warsztatu do szkody</i></span></td>
        </tr>
    @endif
</table>