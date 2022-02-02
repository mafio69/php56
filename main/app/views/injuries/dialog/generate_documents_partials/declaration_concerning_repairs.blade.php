<div class="row">
    @if($injury->step == 10 || $injury->step == 0)
        <div class="col-sm-12 col-md-8 marg-btm">
            <label >Wartość pojazdu przed zdarzeniem:</label>
            {{ Form::text('value_undamaged', null, array('class' => 'form-control required', 'id'=>'value_undamaged',  'placeholder' => 'wartość pojazdu przed zdarzeniem')) }}
        </div>
        <div class="col-sm-12 col-md-4 marg-btm">
            <label>Netto/brutto</label>
            {{ Form::select('value_undamaged_net_gross', Config::get('definition.net_gross'), 1, array('class' => 'form-control')) }}
        </div>

        <div class="col-sm-12 col-md-8 marg-btm">
            <label >Wartość pojazdu po zdarzeniu:</label>
            {{ Form::text('value_repurchase', null, array('class' => 'form-control required', 'id'=>'value_repurchase',  'placeholder' => 'wartość pojazdu po zdarzeniu')) }}
        </div>
        <div class="col-sm-12 col-md-4 marg-btm">
            <label>Netto/brutto</label>
            {{ Form::select('value_repurchase_net_gross', Config::get('definition.net_gross'), 1, array('class' => 'form-control')) }}
        </div>

        <div class="col-sm-12 col-md-8 marg-btm">
            <label >Wysokość odszkodowania:</label>
            {{ Form::text('value_compensation', null, array('class' => 'form-control required', 'id'=>'value_compensation',  'placeholder' => 'wysokość odszkodowania')) }}
        </div>
        <div class="col-sm-12 col-md-4 marg-btm">
            <label>Netto/brutto</label>
            {{ Form::select('value_compensation_net_gross', Config::get('definition.net_gross'), 1, array('class' => 'form-control')) }}
        </div>

        <div class="col-sm-12 marg-btm">
            <label >Oferta jest ważna do dnia:</label>
            {{ Form::text('expire_tenderer', null, array('class' => 'form-control datepicker required', 'id'=>'expire_tenderer',  'placeholder' => 'oferta jest ważna do dnia')) }}
        </div>
    @endif
    <div class="col-sm-12 marg-btm">
        <label>
            Potwierdź wygenerowanie dokumentu.
        </label>
    </div>
</div>