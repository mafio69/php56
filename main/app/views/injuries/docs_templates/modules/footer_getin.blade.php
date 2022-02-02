<div id="footer" class="footer-getin" >
    <div>
        <img src="templates-src/getin-pasek.jpg" alt="Logo" style="width: 680px;"/>
    </div>
    <div>
        <p style="color: #000;">
            {{ checkIfEmpty('1', $ideaA) }} z siedzibą we Wrocławiu,
            {{ checkIfEmpty('2', $ideaA) }}, {{ checkIfEmpty('3', $ideaA) }} {{ checkIfEmpty('13', $ideaA) }},
            nr KRS {{ checkIfEmpty('7', $ideaA) }}, Sąd Rejonowy dla Wrocławia-Fabrycznej we Wrocławiu, VI Wydział Gospodarczy Krajowego Rejestru Sądowego,
            NIP: {{ checkIfEmpty('8', $ideaA) }}, REGON: {{ checkIfEmpty('15', $ideaA) }}, wysokość kapitału: {{ checkIfEmpty('9', $ideaA) }} złotych (wpłacony w całości).
        </p>
    </div>
</div>