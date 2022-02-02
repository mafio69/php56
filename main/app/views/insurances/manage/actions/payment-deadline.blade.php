<div class="form-group">
    <label class="col-sm-4 control-label">Termin płatności {{ $i }} raty</label>
    <div class="col-sm-8">
        <input value="{{ (isset($payment)) ? $payment->deadline : '' }}" class="form-control date required" name="date_payment_deadline[{{ $i }}]" id="date_payment_deadline_{{ $i }}" placeholder="termin płatności {{ $i }} raty" >
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Kwota {{ $i }} raty</label>
    <div class="col-sm-8">
        <input value="{{ (isset($payment)) ? $payment->amount : '' }}" class="form-control number currency_input required" name="date_payment_amount[{{ $i }}]" id="date_payment_amount{{ $i }}" placeholder="kwota {{ $i }} raty" >
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Zapłacono {{ $i }} ratę</label>
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-4 col-md-2">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('paid['.$i.']', $i, null, ['class' => 'payment-paid', 'data-id' => $i, (isset($payment) && !is_null($payment->date_of_payment)) ? 'checked' : '' ]) }}
                    </label>
                </div>
            </div>
            <div class="col-sm-8 col-md-10" id="date_of_payment_{{ $i }}">
                <input value="{{ (isset($payment)) ? $payment->date_of_payment : '' }}" class="form-control date" name="date_of_payment[{{ $i }}]"  placeholder="termin dokonania zapłaty {{ $i }} raty" >
            </div>
        </div>
    </div>
</div>


    <script>
        setTimeout(function(){
            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd",
                onClose: function( selectedDate ) {
                    if($(this).attr('date-opt') == 'from'){
                        $( '#date_to' ).datepicker( "option", "minDate", selectedDate );
                    }else if( $(this).attr('date-opt') == 'to' ) {
                        $( '#date_from' ).datepicker("option", "maxDate", selectedDate);
                    }
                }
            });
        }, 500);
    </script>


