<h4 class="marg-top-min">Dane nabywcy:</h4>
<div class="form-group row">
    <div class="col-sm-12 col-md-6">
        <table class="table table-hover table-condensed">
            <tr>
                <td><label>Nazwa:</label></td>
                <td>{{ $buyer->name }}</td>
            </tr>
            <tr>
                <td><label>NIP:</label></td>
                <td>{{ $buyer->nip }}</td>
            </tr>
            <tr>
                <td><label>REGON:</label></td>
                <td>{{ $buyer->regon }}</td>
            </tr>
        </table>
    </div>
    <div class="col-sm-12 col-md-6">
        <table class="table table-hover table-condensed">
            <tr>
                <td><label>Kod pocztowy:</label></td>
                <td>{{ $buyer->address_code }}</td>
            </tr>
            <tr>
                <td><label>Miasto:</label></td>
                <td>{{ $buyer->address_city }}</td>
            </tr>
            <tr>
                <td><label>Ulica:</label></td>
                <td>{{ $buyer->address_street }}</td>
            </tr>
        </table>
    </div>
    <div class="col-sm-12 col-md-6 col-md-offset-3">
        <table class="table table-hover table-condensed">
            <tr>
                <td><label>Telefon:</label></td>
                <td>{{ $buyer->phone }}</td>
            </tr>
            <tr>
                <td><label>Email:</label></td>
                <td>{{ $buyer->email }}</td>
            </tr>
            <tr>
                <td><label>Osoba kontaktowa:</label></td>
                <td>{{ $buyer->contact_person }}</td>
            </tr>
        </table>
    </div>
</div>