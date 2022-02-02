@if( Settings::get('idea_getin_activated') == 'enabled' )
    <div id="footer" style="border-top: none; margin: auto 30px;">
        <table style="width: 100%; color:grey;" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="3"  style="font-size: 6pt;line-height: 10px;">{{ checkIfEmpty('1', $ideaA) }}</td>
            </tr>
            <tr>
                <Td width="85px" style="font-size: 6pt; padding-right: 5px; border-right: 0.1pt solid #0096D6;line-height: 8px;">{{ checkIfEmpty('2', $ideaA) }}</td>
                <Td width="85px" style="font-size: 6pt; padding-right: 5px; padding-left: 5px; border-right: 0.1pt solid #0096D6;line-height: 8px;">T: {{ checkIfEmpty('5', $ideaA) }}</td>
                <Td rowspan="3"  style="font-size: 5pt; padding-right: 5px; padding-left: 5px;line-height: 8px; text-align: justify;">
                    {{ checkIfEmpty('1', $ideaA) }} z siedzibą we Wrocławiu {{ checkIfEmpty('2', $ideaA) }}, {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}, wpisana do Rejestru Przedsiębiorców Krajowego Rejestru Sądowego prowadzonego przez Sąd Rejonowy dla Wrocławia - Fabrycznej we Wrocławiu, VI Wydział Gospodarczy Krajowego Rejestru Sądowego pod nr KRS: {{ checkIfEmpty('7', $ideaA) }}, NIP: {{ checkIfEmpty('8', $ideaA) }}, REGON:  {{ checkIfEmpty('15', $ideaA) }}, kapitał zakładowy: {{ checkIfEmpty('9', $ideaA) }} opłacony w całości.
                </td>
            </tr>
            <tr>
                <Td width="85px" style="font-size: 6pt; padding-right: 5px; border-right: 0.1pt solid #0096D6;line-height: 8px;"> {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}</td>
                <Td width="85px" style="font-size: 6pt; padding-right: 5px; padding-left: 5px; border-right: 0.1pt solid #0096D6;line-height: 8px;">F: {{ checkIfEmpty('6', $ideaA) }}</td>
            </tr>
            <tr>
                <Td width="85px" style="font-size: 5pt; padding-right: 5px; border-right: 0.1pt solid #0096D6; line-height: 8px;"><img src="templates-src/world.png" style="height: 10px;  margin-top: 3px;"/> {{ checkIfEmpty('12', $ideaA) }}</td>
                <Td width="85px" style="font-size: 5pt; padding-right: 5px; padding-left: 5px; border-right: 0.1pt solid #0096D6;line-height: 8px;"><img src="templates-src/phone.png" style="height: 10px; margin-top: 3px;"/> {{ checkIfEmpty('11', $ideaA) }}</td>
            </tr>

        </table>

    </div>
@else
<div id="footer" class="footer-vb" >
    <table style="width: 100%; color:grey;">
        <tr>
            <td colspan="3" style="color:#0096D6; font-size: 8pt;">
                <img src="templates-src/phone.png" style="height: 14px; margin-top: 3px;"/>
                {{ checkIfEmpty('11', $ideaA) }}
                <img src="templates-src/world.png" style="height: 14px; margin-top: 3px;"/>
                {{ checkIfEmpty('12', $ideaA) }}
            </td>
        </tr>
        <tr style="font-size: 5pt;">
            <td colspan="3" style="padding-top: 13px;" ><strong>{{ checkIfEmpty('1', $ideaA) }}</strong></td>
        </tr>
        <tr style="font-size: 5pt;">
            <Td>{{ checkIfEmpty('2', $ideaA) }}</td>
            <Td>T: {{ checkIfEmpty('5', $ideaA) }}</td>
            <Td>Sąd Rejonowy dla Wrocławia-Fabrycznej, VI Wydział Gospodarczy Krajowego Rejestru Sądowego </td>
        </tr>
        <tr style="font-size: 5pt;">
            <Td width="75px">{{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}</td>
            <Td width="85px">F: {{ checkIfEmpty('6', $ideaA) }}</td>
            <Td >KRS {{ checkIfEmpty('7', $ideaA) }}, NIP: {{ checkIfEmpty('8', $ideaA) }}, REGON: {{ checkIfEmpty('15', $ideaA) }}</td>
        </tr>
        <tr style="font-size: 5pt;">
            <Td colspan="2"></td>
            <Td >Kapitał zakładowy {{ checkIfEmpty('9', $ideaA) }}, opłacony w całości</td>
        </tr>
    </table>
</div>

@endif