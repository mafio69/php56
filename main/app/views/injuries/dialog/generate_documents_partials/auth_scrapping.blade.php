<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Firma upoważniona:</label>
        {{ Form::text('company', '' , array('class' => 'form-control required',  'placeholder' => 'Firma upoważniona', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Teren złomowania [PL]:</label>
        {{ Form::text('address_pl', '' , array('class' => 'form-control required',  'placeholder' => 'Teren złomowania [PL]', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Teren złomowania [EN]:</label>
        {{ Form::text('address_en', '' , array('class' => 'form-control required',  'placeholder' => 'Teren złomowania [EN]', 'required')) }}
    </div>
</div>