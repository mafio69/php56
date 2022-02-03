<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Data poinformowania brokera:</label>
        {{ Form::text('broker_date', '', array('class' => 'form-control datepicker ',   'placeholder' => 'Data poinformowania brokera')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Data ważności oferty:</label>
        {{ Form::text('expire_date', '', array('class' => 'form-control datepicker ',   'placeholder' => 'Data ważności oferty')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Wartość pojazdu przed zdarzeniem:</label>
        {{ Form::text('value_before', '', array('class' => 'form-control  ', 'id'=>'value_before',  'placeholder' => 'wartość pojazdu przed zdarzeniem')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Wartość pojazdu po zdarzeniu:</label>
        {{ Form::text('value_after', '', array('class' => 'form-control  ', 'id'=>'value_after',  'placeholder' => 'wartość pojazdu po zdarzeniu')) }}
    </div>
</div>
<div class="row">
    <div class="col-sm-12 marg-btm">
        <label >Wysokość odszkodowania:</label>
        {{ Form::text('value_compensation', '', array('class' => 'form-control  ', 'id'=>'value_compensation',  'placeholder' => 'wysokość odszkodowania')) }}
    </div>
</div>