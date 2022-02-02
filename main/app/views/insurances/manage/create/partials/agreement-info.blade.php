<div class="col-sm-12">
    <div class="col-sm-12">
        <h4 class="page-header marg-top-min">Dane umowy</h4>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Rodzaj umowy:</label>
        <div class="col-sm-8">
            {{ Form::select('leasing_agreement_type_id', $leasingAgreementTypes, null, ['class' => 'form-control'] ) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Finansujący:</label>
        <div class="col-sm-8">
            {{ Form::select('owner_id', $owners, 0, ['class' => 'form-control'] ) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Nr umowy:</label>
        <div class="col-sm-8">
            {{ Form::text('nr_contract', '', ['class'  => 'form-control required', 'id' => 'nr_contract', 'placeholder' => 'nr umowy']) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Ilość rat:</label>
        <div class="col-sm-8">
            {{ Form::text('installments', '', array('class' => 'form-control', 'placeholder' => 'ilość rat', 'id' => 'installments')) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Okres ubezp. od:</label>
        <div class="col-sm-8">
            {{ Form::text('insurance_from', '', array('class' => 'form-control date required', 'placeholder' => 'okres ubezp. od', 'id' => 'insurance_from')) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Okres ubezp. do:</label>
        <div class="col-sm-8">
            {{ Form::text('insurance_to', '', array('class' => 'form-control date required', 'placeholder' => 'okres ubezp. do', 'id' => 'insurance_to')) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Ubezpieczenie od kwoty:</label>
        <div class="col-sm-8">
            {{ Form::select('net_gross', ['1' => 'netto', '2' => 'brutto'], 1, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Oznacz umowę jako obcą:</label>
        <div class="col-sm-8">
           {{ Form::checkbox('if_foreign', '', false, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Oznacz umowę jako obcą:</label>
        <div class="col-sm-8">
           {{ Form::checkbox('if_foreign', '', false, array('class' => 'form-control')) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Płatn. ubezp. przez leasingobiorcę:</label>
        <div class="col-sm-8">
            {{ Form::select('leasing_agreement_payment_way_id', $leasingAgreementPaymentWays, 1, array('class' => 'form-control')) }}
        </div>
    </div>

    <h4 class="inline-header col-sm-12"></h4>

    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Wart. netto pożyczki:</label>
        <div class="col-sm-8">
            {{ Form::text('loan_net_value', '0.0', array('class' => 'form-control', 'id' => 'loan_net_value', 'placeholder' => 'wartość netto pożyczki', 'readonly')) }}
        </div>
    </div>
    <div class="form-group col-sm-12 col-md-6">
        <label class="col-sm-4 control-label">Wart. brutto pożyczki:</label>
        <div class="col-sm-8">
            {{ Form::text('loan_gross_value', '0.0', array('class' => 'form-control', 'id'=>'loan_gross_value', 'placeholder' => 'wartość brutto pożyczki', 'readonly')) }}
        </div>
    </div>


    <hr class="height bg-primary"/>
</div>


@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#installments').on('change', function(){
               if( $('#insurance_from').val() != '' )
               {
                   var installments = $(this).val();
                   if(installments != '') {
                       var insurance_from = moment($('#insurance_from').val(), "YYYY-MM-DD");
                       insurance_from.add(installments, 'months').subtract(1, 'day');
                       $('#insurance_to').val(insurance_from.format("YYYY-MM-DD"));
                   }
               }
            });
            $('#insurance_to').on('change', function(){
                if( $('#insurance_from').val() != '' )
                {
                    var insurance_to = moment($('#insurance_to').val(), "YYYY-MM-DD").add(1, 'day');
                    var insurance_from = moment($('#insurance_from').val(), "YYYY-MM-DD");
                    var diff = insurance_to.diff(insurance_from, 'months');
                    $('#installments').val(diff);
                }else if( $('#installments').val() != '' ){
                    var insurance_to = moment($('#insurance_to').val(), "YYYY-MM-DD").subtract(1, 'day');
                    var installments = $('#installments').val();
                    var insurance_from = insurance_to.subtract(installments, 'months');
                    $('#insurance_from').val(insurance_from.format("YYYY-MM-DD"));
                }
            });

            $('#insurance_from').on('change', function(){
                if( $('#insurance_to').val() != '' )
                {
                    var insurance_to = moment($('#insurance_to').val(), "YYYY-MM-DD").add(1, 'day');
                    var insurance_from = moment($('#insurance_from').val(), "YYYY-MM-DD");
                    var diff = insurance_to.diff(insurance_from, 'months');
                    $('#installments').val(diff);
                }else if( $('#installments').val() != '' ){
                    var insurance_from = moment($('#insurance_from').val(), "YYYY-MM-DD").add(1, 'day');
                    var installments = $('#installments').val();
                    var insurance_to = insurance_from.add(installments, 'months');
                    $('#insurance_to').val(insurance_to.format("YYYY-MM-DD"));
                }
            });
        });
    </script>
@stop