<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Numer decyzji wyrejestrowania pojazdu:</label>
        {{ Form::text('decision_number', '' , array('class' => 'form-control required',  'placeholder' => 'Numer decyzji wyrejestrowania pojazdu', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label>Wskaż załączniki</label>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[0]" value="Oryginał decyzji wyrejestrowania pojazdu"> Oryginał decyzji wyrejestrowania pojazdu
            </label>
            <input type="text" name="description[0]" class="form-control input-sm" style="width: auto; display: inline;" value="" placeholder="numer decyzji"/>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[1]" value="Umowa przeniesienia własności ( dwa egzemplarze)"> Umowa przeniesienia własności ( dwa egzemplarze)
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[2]" value="Pełnomocnictwo"> Pełnomocnictwo
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[3]" value="Oryginał Karty pojazdu"> Oryginał Karty pojazdu
            </label>
            <input type="text" name="description[3]" class="form-control input-sm" style="width: auto; display: inline;" value="" placeholder="seria i numer"/>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[4]" value="Oryginał Dowodu rejestracyjnego"> Oryginał Dowodu rejestracyjnego
            </label>
            <input type="text" name="description[4]" class="form-control input-sm" style="width: auto; display: inline;" value="" placeholder="seria i numer"/>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[5]" value="Upoważnienie do odbioru odszkodowania"> Upoważnienie do odbioru odszkodowania
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[6]" value="Klucze"> Klucze
            </label>
            <input type="text" name="description[6]" class="form-control input-sm" style="width: auto; display: inline;" value="" placeholder="ilość sztuk"/>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[7]" value="Dokumenty pochodzenia pojazdu"> Dokumenty pochodzenia pojazdu
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[8]" value="Kserokopie postanowienia o umorzeniu dochodzenia"> Kserokopie postanowienia o umorzeniu dochodzenia
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="attachments[9]" value="Inne"> Inne
            </label>
            <input type="text" name="description[9]" class="form-control input-sm" style="width: auto; display: inline;" value="" placeholder="treść"/>
        </div>
    </div>

</div>