{{ Form::hidden('insurance_id', $insurance->id) }}
<div class="form-group">
    <div class="row marg-btm">
        <div class="form-group">
            <label class="col-sm-12 control-label">Data zwrotu:</label>
            <div class="col-sm-12">
                {{ Form::text('date_to', (count($refund)>0)?date('Y-m-d'):$date_to, array('class' => 'form-control required', 'readonly', 'placeholder' => 'data zwrotu', 'required', 'id' => 'date_to')) }}
            </div>
        </div>
    </div>
    <div class="row marg-btm">
        @if(isset($refund['error']))
            <div class="form-group has-error has-feedback">
                <label class="col-sm-12 control-label">Zwrot składki [zł]:</label>
                <div class="col-sm-12">
                    {{ Form::text('refund', '', array('class' => 'form-control required number currency_input tips', 'title' => $refund['error'], 'placeholder' => 'zwrot składki', 'required', 'id' => 'refund')) }}
                    <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                </div>
            </div>
        @else
            <div class="form-group" id = "refund_group">
                <label class="col-sm-12 control-label">Zwrot składki [zł]:</label>
                <div class="col-sm-12">
                    {{ Form::text('refund', checkIfEmpty('value', $refund, ''), array('class' => 'form-control required number currency_input tips', 'placeholder' => 'zwrot składki', 'required', 'id' => 'refund')) }}
                </div>
            </div>
        @endif
    </div>
    <div class="row marg-btm">
        <div class="form-group" >
            <label class="col-sm-12 control-label">Zwrot prowizji [zł]:</label>
            <div class="col-sm-12">
                {{ Form::text('commission_refund_value', null, array('class' => 'form-control required number currency_input ', 'placeholder' => 'zwrot prowizji')) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="text-center col-md-8 col-md-offset-2" >
            {{ Form::submit('Wykonaj zwrot',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa wykonywanie zwrotu...'))  }}
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        var date = new Date("{{ $date_to }}");
        var currentMonth = date.getMonth();
        var currentDate = date.getDate();
        var currentYear = date.getFullYear();

        $('#date_to').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            changeMonth: true, changeYear: true, maxDate: new Date(currentYear, currentMonth, currentDate), dateFormat: "yy-mm-dd",
            onClose: function (selectedDate) {
                $.ajax({
                    url: "{{ URL::to('insurances/manage-actions/calculate-yacht-refund', [$insurance->id]) }}/"+selectedDate,
                    data: {
                        _token: $('input[name="_token"]').val()
                    },
                    dataType: "json",
                    type: "POST",
                    success: function( data ) {
                        if(isset(data.value)){
                            $('#refund').val(data.value);
                            $('#refund_group').removeClass('has-error').removeClass('has-feedback');
                            $('#refund').removeAttr('title', data.error);
                            $("#refund").removeAttr('data-original-title');
                        }else{
                            $('#refund').val('');
                            $('#refund_group').addClass('has-error').addClass('has-feedback');
                            $('#refund').attr('title', data.error);
                            $("#refund").attr('data-original-title', data.error );
                        }
                    }
                });
            }
        });
    });
</script>
