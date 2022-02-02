<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Numer karty pojazdu:</label>
        {{ Form::text('vehicle_card_number', '' , array('class' => 'form-control required',  'placeholder' => 'Numer karty pojazdu', 'required')) }}
    </div>
    <div class="col-sm-12 marg-btm">
        <label >Numer fakturę zakupu uszkodzonego pojazdu:</label>
        {{ Form::text('invoice_number', '' , array('class' => 'form-control required',  'placeholder' => 'Numer fakturę zakupu uszkodzonego pojazdu', 'required')) }}
    </div>
</div>