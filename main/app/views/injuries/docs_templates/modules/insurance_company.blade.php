            <table style="width:100%; font-size:9pt; margin-top:40pt;  font-weight:normal;" >
                <tr>
                    <td></td>
                    <td style="text-align: right; width: 8.2cm;">{{ ($injury->insuranceCompany) ? $injury->insuranceCompany->name : ''}}</td>
                </tr>
                <tr>
                    <Td></Td>
                    <td style="text-align: right;width: 8.2cm;">{{ ($injury->insuranceCompany) ? $injury->insuranceCompany->street : ''}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: right;width: 8.2cm;">{{ ($injury->insuranceCompany) ? $injury->insuranceCompany->post : ''}} {{ ($injury->insuranceCompany) ? $injury->insuranceCompany->city : ''}}</td>
                </tr>
            </table>
