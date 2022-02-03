<div class="row">
    <div class="col-sm-12 marg-btm">
        <div class="col-sm-12 marg-btm">
            <label>Termin dostarczenia deklaracji:</label>
            {{ Form::text('delivery_deadline', Date('Y-m-d', strtotime("+3 days")) , array('class' => 'form-control
            required', 'id'=>'date_submit',  'placeholder' => 'Pojazd dostarczyć do', 'required')) }}
        </div>
        Potwierdź wygenerowanie dokumentu.
    </div>
</div>