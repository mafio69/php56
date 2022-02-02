<table style="width: 100%; font-size: 10pt; font-weight:normal; ">
    <tr>
        <td style="text-align: right;">{{ checkIfEmpty('13', $ideaA) }},
            @if(isset($inputs['date']))
                {{ $inputs['date'] }}
            @else
                {{ date('d-m-Y') }}
            @endif
        </td>
    </tr>
</table>