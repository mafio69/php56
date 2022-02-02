<div class="row">
    <div class="col-sm-12 marg-btm">
        <div class="form-group">
            <label>Termin dostarczenia</label>
            {{ Form::text('sending_date', Date('Y-m-d', strtotime("+3 days")), ['class' => 'form-control date datepicker required']) }}
        </div>
    </div>
</div>