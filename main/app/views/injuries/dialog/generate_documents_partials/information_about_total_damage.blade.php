<div class="row">
    <div class="col-sm-12 marg-btm">
        <div class="form-group">
            @if($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1)
                Potwierdź wygenerowanie dokumentu.
            @else
                <label>Termin dostarczenia deklaracji</label>
                {{ Form::text('sending_date', Date('Y-m-d', strtotime("+3 days")), ['class' => 'form-control date datepicker required']) }}
            @endif
        </div>
    </div>
</div>