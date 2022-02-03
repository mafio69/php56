@extends('layouts.main')

@section('header')

    Edycja danych polisy nr {{ valueIfNotNull($insurance->insurance_number) }} <br><small>nr umowy {{ $insurance->leasing_agreement->nr_contract }}</small>

    <div class="pull-right">
        <a href="{{{ URL::previous() }}}#insurances-data" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::hidden('leasing_agreement_id', $leasingAgreement->id) }}
                    {{ Form::open(array('url' => URL::to('insurances/info-insurances/update-yacht', [$insurance->id]), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="page-header marg-top-min">Dane polisy</h4>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nr polisy</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->insurance_number }}" class="form-control " name="insurance_number"  placeholder="numer polisy">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nr zgłoszenia</label>
                                <div class="col-sm-8">
                                    <input value="{{ $leasingAgreement->notification_number }}" class="form-control" name="notification_number" disabled  placeholder="numer zgłoszenia">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Okres leasingu od</label>
                                <div class="col-sm-8">
                                    <input value="{{ $leasingAgreement->insurance_from }}" class="form-control " readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Okres leasingu do</label>
                                <div class="col-sm-8">
                                    <input value="{{ $leasingAgreement->insurance_to }}" class="form-control " readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Długość polisy [msc]</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->months }}" class="form-control" name="months" id="months" placeholder="długość trwania polisy" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Data polisy</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->insurance_date }}" class="form-control date" name="insurance_date" id="insurance_date" placeholder="data polisy" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Polisa od</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->date_from }}" class="form-control date  required" name="date_from" id="date_from"  placeholder="polisa od" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Polisa do</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input value="{{ $insurance->date_to }}" class="form-control date required" name="date_to" id="date_to"   placeholder="polisa do" readonly>
                                        <span class="input-group-btn tips" title="edytuj - nie koryguje długości">
                                            <button class="btn btn-default" type="button" id="edit-date-to">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <h4 class="inline-header"></h4>

                            <div class="form-group marg-top">
                                <label class="col-sm-4 control-label">Wartość z umowy
                                    {{ ($leasingAgreement->net_gross == 2) ? '[brutto]' : '[netto]'  }}
                                </label>
                                <div class="col-sm-8">
                                    <p class="help-block form-control">{{ ($leasingAgreement->net_gross == 2) ?  $leasingAgreement->loan_gross_value : $leasingAgreement->loan_net_value}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Przedmioty umowy
                                </label>
                                <div class="col-sm-8">
                                    <ul class="list-group">
                                        @foreach($leasingAgreement->objects as $object)
                                            <li class="list-group-item">{{ $object->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-sm-4 control-label">Leasingobiorca</label>
                                <div class="col-sm-8">
                                    <p class="help-block form-control">{{ $leasingAgreement->client->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <p class="text-center col-sm-offset-4 col-sm-8 help-block">{{ $leasingAgreement->import_insurance_company }}</p>
                                <label class="col-sm-4 control-label">Ubezpieczyciel</label>
                                <div class="col-sm-8">
                                    {{ Form::select('insurance_company_id', $insuranceCompanies, $insurance->insurance_company_id, array('class' => 'form-control', 'id' => 'insurance_company_id') ) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Płatn. ubezp. przez leasingobiorcę</label>
                                <div class="col-sm-8">
                                    {{ Form::select('leasing_agreement_payment_way_id', $paymentWays, $insurance->leasing_agreement_payment_way_id, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group" id="installments-container">
                                <label class="col-sm-4 control-label">Liczba rat</label>
                                <div class="col-sm-8">
                                    {{ Form::select('leasing_agreement_installment_id', $installments, $insurance->leasing_agreement_installment_id, array('class' => 'form-control', 'installment') ) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Składka leasingobiorcy</label>
                                <div class="col-sm-6">
                                    <input value="{{ $insurance->contribution_lessor }}" class="form-control number currency_input required" name="contribution_lessor"  placeholder="składka leasingobiorcy" id="contribution_lessor" require>
                                </div>
                                <div class="col-sm-2">
                                    {{ Form::select('contribution_lessor_currency_id', Config::get('definition.currencies'), $insurance->contribution_lessor_currency_id, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <h4 class="inline-header"><span>Termin płatności:</span></h4>
                            <div id="payment-deadline-container">
                            @foreach($insurance->payments as $i => $payment)
                                @include('insurances.manage.actions.payment-deadline', compact(++$i, 'payment'))
                            @endforeach
                            </div>
                            <h4 class="inline-header"><span>Prowizje:</span></h4>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Wysokość prowizji</label>
                                <div class="col-sm-8">
                                    {{ Form::text('commission_value', $insurance->commission_value, array('class' => 'form-control currency_input number', 'placeholder' => 'wysokość prowizji') ) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Data prowizji</label>
                                <div class="col-sm-8">
                                    {{ Form::text('commission_date', $insurance->commission_date, array('class' => 'form-control date dynamic_datepicker', 'placeholder' => 'data prowizji') ) }}
                                </div>
                            </div>
                            <h4 class="inline-header"><span>Zakres ubezpieczenia:</span></h4>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">OC</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('coverages[]', 1, (isset($coverages[1])) ? 'checked' : null, ['class' => 'insurance-coverage', 'data-group' => 'oc-group']) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group oc-group" style="display: none;">
                                <label class="col-sm-4 control-label">Suma ubezpieczenia OC</label>
                                <div class="col-sm-8">
                                    <input value="{{ (isset($coverages[1])) ? $coverages[1]->amount : '' }}" class="form-control number currency_input" name="oc_insurance"  placeholder="suma ubezpieczenia oc" required>
                                </div>
                            </div>
                            <div class="form-group oc-group" style="display: none;">
                                <label class="col-sm-4 control-label">Waluta OC</label>
                                <div class="col-sm-8">
                                    {{ Form::select('oc_currency', Config::get('definition.currencies'),(isset($coverages[1])) ? $coverages[1]->currency_id : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group oc-group" style="display: none;">
                                <label class="col-sm-4 control-label">Netto/brutto</label>
                                <div class="col-sm-8">
                                    {{ Form::select('oc_net_gross', Config::get('definition.net_gross'), (isset($coverages[1])) ? $coverages[1]->net_gross : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">AC</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('coverages[]', 2, (isset($coverages[2])) ? 'checked' : null, ['class' => 'insurance-coverage', 'data-group' => 'ac-group']) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ac-group" style="display: none;">
                                <label class="col-sm-4 control-label">Suma ubezpieczenia AC</label>
                                <div class="col-sm-8">
                                    <input value="{{ (isset($coverages[2])) ? $coverages[2]->amount : '' }}" class="form-control number currency_input" name="ac_insurance"  placeholder="suma ubezpieczenia Ac" required>
                                </div>
                            </div>
                            <div class="form-group ac-group" style="display: none;">
                                <label class="col-sm-4 control-label">Waluta AC</label>
                                <div class="col-sm-8">
                                    {{ Form::select('ac_currency', Config::get('definition.currencies'), (isset($coverages[2])) ? $coverages[2]->currency_id : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group ac-group" style="display: none;">
                                <label class="col-sm-4 control-label">Netto/brutto</label>
                                <div class="col-sm-8">
                                    {{ Form::select('ac_net_gross', Config::get('definition.net_gross'), (isset($coverages[2])) ? $coverages[2]->net_gross : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">NNW</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('coverages[]', 3, (isset($coverages[3])) ? 'checked' : null, ['class' => 'insurance-coverage', 'data-group' => 'nnw-group']) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group nnw-group" style="display: none;">
                                <label class="col-sm-4 control-label">Suma ubezpieczenia NNW</label>
                                <div class="col-sm-8">
                                    <input value="{{ (isset($coverages[3])) ? $coverages[3]->amount : '' }}" class="form-control number currency_input" name="nnw_insurance"  placeholder="suma ubezpieczenia nnw" required>
                                </div>
                            </div>
                            <div class="form-group nnw-group" style="display: none;">
                                <label class="col-sm-4 control-label">Waluta NNW</label>
                                <div class="col-sm-8">
                                    {{ Form::select('nnw_currency', Config::get('definition.currencies'),(isset($coverages[3])) ? $coverages[3]->currency_id : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group nnw-group" style="display: none;">
                                <label class="col-sm-4 control-label">Netto/brutto</label>
                                <div class="col-sm-8">
                                    {{ Form::select('nnw_net_gross', Config::get('definition.net_gross'), (isset($coverages[3])) ? $coverages[3]->net_gross : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Mienie osobiste członków załogi</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('coverages[]', 4, (isset($coverages[4])) ? 'checked' : null, ['class' => 'insurance-coverage', 'data-group' => 'crew-group']) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group crew-group" style="display: none;">
                                <label class="col-sm-4 control-label">Suma ubezpieczenia mienia osobistego członków załogi</label>
                                <div class="col-sm-8">
                                    <input value="{{ (isset($coverages[4])) ? $coverages[4]->amount : '' }}" class="form-control number currency_input" name="crew_insurance"  placeholder="suma ubezpieczenia mienia osobistego członków załogi" required>
                                </div>
                            </div>
                            <div class="form-group crew-group" style="display: none;">
                                <label class="col-sm-4 control-label">Waluta ubezpieczenia mienia osobistego członków załogi</label>
                                <div class="col-sm-8">
                                    {{ Form::select('crew_currency', Config::get('definition.currencies'),(isset($coverages[4])) ? $coverages[4]->currency_id : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                            <div class="form-group crew-group" style="display: none;">
                                <label class="col-sm-4 control-label">Netto/brutto</label>
                                <div class="col-sm-8">
                                    {{ Form::select('crew_net_gross', Config::get('definition.net_gross'), (isset($coverages[4])) ? $coverages[4]->net_gross : null, array('class' => 'form-control') ) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Wprowadź zmiany',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa wprowadzanie zmian...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $( "form#page-form" ).submit(function(e) {
                var $btn = $('#form_submit').button('loading');
                if(! $('#page-form').valid()) {
                    e.preventDefault();
                    $btn.button('reset');
                    return false;
                }
                return true;
            });

            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd",
                onClose: function( selectedDate ) {
                    if($(this).attr('date-opt') == 'from'){
                        $( '#date_to' ).datepicker( "option", "minDate", selectedDate );
                    }else if( $(this).attr('date-opt') == 'to' ) {
                        $( '#date_from' ).datepicker("option", "maxDate", selectedDate);
                    }
                }
            });

            $('#months, #date_from').on('change', function(){
                if($('#months').val() == '')
                {
                    $.notify({
                        icon: "fa fa-warning",
                        message: 'proszę wybrać prawidłowy okres ubezpieczenia w miesiącach'
                    },{
                        type: 'danger',
                        placement: {
                            from: 'bottom',
                            align: 'right'
                        },
                        delay: 5000,
                        timer: 500
                    });
                    $('#date_to').val('');
                }else{
                    var date_from = $('#date_from').val()+' 00:00:00';
                    date_from = parseDateToIe(date_from);
                    var date_to = date_from;
                    date_to.setMonth(date_from.getMonth() + parseInt($('#months').val()));
                    date_to.setDate(date_from.getDate() - 1);
                    var MyDateString = (date_to.getFullYear() + '-'
                    + ('0' + (date_to.getMonth()+1)).slice(-2) + '-'
                    + ('0' + date_to.getDate()).slice(-2) );
                    $("#date_to").val(MyDateString);
                }
            }).change();

            $('select[name="leasing_agreement_payment_way_id"]').on('change', function(){
                if($(this).find('option:selected').val() == 1)
                {
                    $('#installments-container').show();
                }else{
                    $('#installments-container').hide();
                }
            }).change();

            $('select[name="leasing_agreement_payment_way_id"], select[name="leasing_agreement_installment_id"]').on('change', function() {
                $.ajax({
                    url: "{{ URL::to('insurances/manage-actions/payment-deadline') }}",
                    data: {
                        leasing_agreement_payment_way_id: $('select[name="leasing_agreement_payment_way_id"]').val(),
                        leasing_agreement_installment_id: $('select[name="leasing_agreement_installment_id"]').val(),
                        _token: $('input[name="_token"]').val()
                    },
                    dataType: "html",
                    type: "POST",
                    success: function( data ) {
                        $('#payment-deadline-container').html(data);
                    }
                });
            });

            $('.insurance-coverage').each(function(){
                var group_class = $(this).data('group');
                if($(this).is(':checked')) {
                    $("."+group_class).show();  // checked
                }else {
                    $("."+group_class).hide();
                }
            });

            $('.insurance-coverage').on('click', function(){
                var group_class = $(this).data('group');
                if($(this).is(':checked')) {
                    $("."+group_class).show();  // checked
                }else {
                    $("."+group_class).hide();
                }
            });

            $('#edit-date-to').on('click', function(){
                $('#date_to').removeAttr('readonly');
            });

            $('.payment-paid').each(function(){
                var payment_id = $(this).data('id');
                if($(this).is(':checked')) {
                    $("#date_of_payment_"+payment_id).show();  // checked
                }else {
                    $("#date_of_payment_"+payment_id).hide();
                }
            });

            $('.payment-paid').on('click', function(){
                var payment_id = $(this).data('id');
                if($(this).is(':checked')) {
                    $("#date_of_payment_"+payment_id).show();  // checked
                }else {
                    $("#date_of_payment_"+payment_id).hide();
                }
            });
        });
    </script>

    @include('insurances.manage.partials.check-owner')
@stop