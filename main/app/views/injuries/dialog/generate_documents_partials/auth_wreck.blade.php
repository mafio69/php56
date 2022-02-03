<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Osoba upoważniona:</label>
        {{ Form::text('name', '' , array('class' => 'form-control required',  'placeholder' => 'Osoba upoważniona', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Seria dowodu osobistego:</label>
        {{ Form::text('id_series', '' , array('class' => 'form-control required',  'placeholder' => 'Seria dowodu osobistego', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Numer dowodu osobistego:</label>
        {{ Form::text('id_number', '' , array('class' => 'form-control required',  'placeholder' => 'Numer dowodu osobistego', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Pesel:</label>
        {{ Form::text('pesel', '' , array('class' => 'form-control required',  'placeholder' => 'Pesel', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Adres warsztatu (umiejscowienie wraku):</label>
        {{ Form::text('address', '' , array('class' => 'form-control required',  'placeholder' => 'Adres warsztatu (umiejscowienie wraku)', 'required')) }}
    </div>
</div>