<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Data przekazania informacji:</label>
        {{ Form::text('info_date', '' , array('class' => 'form-control datepicker',  'placeholder' => 'data przekazania informacji')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Miejsce postoju pojazdu i jego stan prawny:</label>
        {{ Form::text('vehicle_location', '' , array('class' => 'form-control ',  'placeholder' => 'miejsce postoju pojazdu i jego stan prawny')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Dodatkowe koszty związane oraz sposób zapłaty:</label>
        {{ Form::text('extra_costs', '' , array('class' => 'form-control ',  'placeholder' => 'dodatkowe koszty związane oraz sposób zapłaty')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Termin odbioru pojazdu:</label>
        {{ Form::text('receive_date', '' , array('class' => 'form-control datepicker',  'placeholder' => 'termin odbioru pojazdu')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Miejsce przechowywania dowodu rejestracyjnego oraz kluczyków do pojazdu:</label>
        {{ Form::text('documents_location', '' , array('class' => 'form-control ',  'placeholder' => 'miejsce przechowywania dowodu rejestracyjnego oraz kluczyków do pojazdu')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Orientacyjne koszty transportu:</label>
        {{ Form::text('transport_costs', '' , array('class' => 'form-control ',  'placeholder' => 'orientacyjne koszty transportu')) }}
    </div>
</div>