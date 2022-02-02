<table style="width: 100%; font-size: 7pt; font-weight:normal; margin-top:40px; ">
    <tbody>
    <tr >
        <td style="text-align:left;">Do wiadomości :</td>
    </tr>
    @if($branch && $branch->id != NULL && $branch->id > 0)
        <tr >
            <td style="text-align:left;">
                {{$branch->company->name}}
            </td>
        </tr>
        <tr>
            <td>
                {{$branch->street}}, {{$branch->code}} {{$branch->city}}
            </td>
        </tr>
    @else
        <tr >
            <td style="text-align:left;">
                ...............................
            </td>
        </tr>
    @endif

    </tbody>
</table>