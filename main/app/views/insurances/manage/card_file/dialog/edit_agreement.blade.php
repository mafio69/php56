<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-dialog/update-agreement', [$agreement->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <div class="form-group col-sm-12 col-md-6">
                <label>Nr umowy</label>
                {{ Form::text('nr_contract', $agreement->nr_contract, array('class' => 'form-control required', 'placeholder' => 'nr umowy')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Finansujący <small>*</small></label>
                {{ Form::select('owner_id', $owners, $agreement->owner_id, array('class' => 'form-control required')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Ilość rat</label>
                {{ Form::text('installments', $agreement->installments, array('class' => 'form-control', 'placeholder' => 'ilość rat')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Rodzaj umowy</label>
                {{ Form::select('leasing_agreement_type_id', $leasingAgreementTypes, $agreement->leasing_agreement_type_id, array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Płatn. ubezp. przez leasingobiorcę</label>
                {{ Form::select('leasing_agreement_payment_way_id', $leasingAgreementPaymentWays, $agreement->leasing_agreement_payment_way_id, array('class' => 'form-control')) }}
            </div>
            @if(!is_null($groups) && $groups['status'] == 'success')
                <div class="form-group col-sm-12 col-md-6">
                    <label>Tabela stawek</label>
                    {{ Form::select('group_id', $groups['groups'], $groups['group'], array('class' => 'form-control ')) }}
                </div>
                <div class="form-group col-sm-12 col-md-6">
                    <label>Grupa ubezpieczenia</label>
                    {{ Form::select('leasing_agreement_insurance_group_row_id', $groups['rates'], $agreement->leasing_agreement_insurance_group_row_id, array('class' => 'form-control ')) }}
                </div>
            @elseif(!is_null($groups))
                <div class="form-group has-error has-feedback tips col-sm-12 col-md-6" title="{{ $groups['msg']}}">
                    <label class="control-label" >Tabela stawek</label>
                    {{ Form::select('group_id', $groups['groups'], $groups['group'], array('class' => 'form-control ', 'aria-describedby' => 'inputWarning2Status', 'id' => 'group')) }}
                    <span class="glyphicon glyphicon-warning-sign form-control-feedback marg-right" aria-hidden="true"></span>
                </div>
                <div class="form-group has-error has-feedback tips col-sm-12 col-md-6" title="{{ $groups['msg']}}">
                    <label class="control-label" >Grupa ubezpieczenia</label>
                    {{ Form::select('leasing_agreement_insurance_group_row_id', $groups['rates'], $agreement->leasing_agreement_insurance_group_row_id, array('class' => 'form-control ', 'aria-describedby' => 'inputWarning2Status', 'id' => 'group_rate')) }}
                    <span class="glyphicon glyphicon-warning-sign form-control-feedback marg-right" aria-hidden="true"></span>
                </div>
            @endif
            <div class="form-group col-sm-12 col-md-6">
                <label>Ubezpieczenie od kwoty</label>
                {{ Form::select('net_gross', ['1' => 'netto', '2' => 'brutto'], $agreement->net_gross, array('class' => 'form-control')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Status</label>
                {{ Form::text('status', $agreement->status, array('class' => 'form-control', 'placeholder' => 'status umowy')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Data akceptacji</label>
                {{ Form::text('date_acceptation', $agreement->date_acceptation, array('class' => 'form-control date', 'placeholder' => 'data akceptacji umowy')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Okres ubezp. od</label>
                {{ Form::text('insurance_from', $agreement->insurance_from, array('class' => 'form-control date', 'placeholder' => 'okres ubezp. od')) }}
            </div>
            <div class="form-group col-sm-12 col-md-6">
                <label>Okres ubezp. do</label>
                {{ Form::text('insurance_to', $agreement->insurance_to, array('class' => 'form-control date', 'placeholder' => 'okres ubezp. do')) }}
            </div>
        </fieldset>
        {{Form::token()}}
    </form>
    {{ Form::hidden('leasing_agreement_id', $agreement->id) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa zapisywanie..." id="set">Zapisz</button>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd"
        });

        $('#group').on('change', function(){
            $.ajax({
                url: "{{ URL::to('insurances/info-dialog/change-insurances-group') }}",
                data: {
                    group_id: $(this).val(),
                    current_rate_id: $('#group_rate').val(),
                    _token: $('input[name="_token"]').val()
                },
                dataType: "json",
                type: "POST",
                success: function( data ) {
                    $('#group_rate').empty();
                    $.each(data.rates, function(i, rate) {
                        $('#group_rate').append('<option value="'+rate.id+'">'+rate.rate_name+'</option>');
                    });

                    if($('#group_rate option[value='+data.current_id+']').length > 0) {
                        $('#group_rate option[value=' + data.current_id + ']').attr('selected', 'selected');
                    }else{
                        $.notify({
                            icon: "fa fa-warning",
                            message: "wybierz stawkę ubezpieczenia"
                        },{
                            type: 'warning',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            },
                            delay: 5000,
                            timer: 500
                        });
                    }
                }
            });
        });
    });
</script>
@include('insurances.manage.partials.check-owner')
