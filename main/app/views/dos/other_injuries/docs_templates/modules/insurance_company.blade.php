<table style="width:100%; margin-top:50px;  font-weight:normal;" >
    <tr>
        <td></td>
        <td style="text-align: right; width: 8.2cm;">{{ ($injury->object->insurance_company()->first()) ? $injury->object->insurance_company()->first()->name : ''}}</td>
    </tr>
    <tr>
        <Td></Td>
        <td style="text-align: right;width: 8.2cm;">{{ ($injury->object->insurance_company()->first()) ? $injury->object->insurance_company()->first()->street : ''}}</td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: right;width: 8.2cm;">
            {{ ($injury->object->insurance_company()->first()) ? $injury->object->insurance_company()->first()->post : ''}}
            {{ ($injury->object->insurance_company()->first()) ? $injury->object->insurance_company()->first()->city : ''}}
        </td>
    </tr>
</table>
