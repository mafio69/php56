<div id="footer" class="footer-vb" >
    <table style="width: 100%; color:grey;">
        <tr>
            <td colspan="3" style="color:#0096D6; font-size: 11pt;">
                <img src="templates-src/world.png" style="height: 14px; margin-top: 3px;"/>
                {{ checkIfEmpty('12', $ideaA) }}
            </td>
        </tr>
        <tr style="font-size: 6.5pt;">
            <td style="padding-top: 13px;" width="130px"><strong>{{ checkIfEmpty('1', $ideaA) }}</strong></td>
            <td style="padding-top: 13px;"  width="130px"><strong>Biuro handlowe Wrocław</strong></td>
            <td style="padding-top: 13px;" >Sąd Rejonowy dla Wrocławia-Fabrycznej, VI Wydział Gospodarczy Krajowego Rejestru Sądowego</td>
        </tr>
        <tr style="font-size: 6.5pt;">
            <Td width="130px">{{ checkIfEmpty('2', $ideaA) }}</td>
            <Td width="130px">T: {{ checkIfEmpty('5', $ideaA) }}</td>
            <Td>KRS {{ checkIfEmpty('7', $ideaA) }}, NIP: {{ checkIfEmpty('8', $ideaA) }}, REGON: {{ checkIfEmpty('15', $ideaA) }}</td>
        </tr>
        <tr style="font-size: 6.5pt;">
            <Td width="130px">{{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }}</td>
            <Td width="130px"></td>
            <Td>Kapitał zakładowy: {{ checkIfEmpty('9', $ideaA) }}</td>
        </tr>
    </table>
</div>