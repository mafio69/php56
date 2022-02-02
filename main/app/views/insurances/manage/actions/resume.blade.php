@extends('layouts.main')

@section('header')

    Polisa wznowienia do umowy nr {{ $leasingAgreement->nr_contract }}

    <div class="pull-right">
        <a href="{{ URL::previous() }}#insurances-data" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a role="button" data-toggle="collapse" href="#collapseLastInsurance" aria-expanded="true" aria-controls="collapseLastInsurance">
                    Dane wznawianej polisa <i class="fa fa-arrows-v"></i>
                    </a>
                </div>
                <div id="collapseLastInsurance" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingLastInsurance">
                    <div class="panel-body">
                    <div class="col-sm-12 col-sm-6">
                        <table class="table table-hover table-condensed">
                            <tr>
                                <td><label>Nr polisy:</label></td>
                                <Td>{{ $lastInsurance->insurance_number }}</td>
                            </tr>
                            <tr>
                                <td><label>Nr zgłoszenia:</label></td>
                                <Td>{{ $lastInsurance->notification_number }}</td>
                            </tr>
                            <tr>
                                <td><label>Typ polisy:</label></td>
                                <Td>
                                    @if($lastInsurance->insuranceType)
                                        {{ $lastInsurance->insuranceType->name }}
                                    @else
                                        ---
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Liczba miesięcy:</label></td>
                                <Td>{{ $lastInsurance->months }}</td>
                            </tr>
                            <tr>
                                <td><label>Data polisy:</label></td>
                                <Td>
                                    @if($lastInsurance->insurance_date == '0000-00-00')
                                        ---
                                    @else
                                        {{ $lastInsurance->insurance_date }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Polisa od:</label></td>
                                <Td
                                        @if($lastInsurance->date_from == '0000-00-00')
                                        class="red"
                                        @endif
                                >
                                    {{ $lastInsurance->date_from }}
                                </td>
                            </tr>
                            <tr>
                                <td><label>Polisa do:</label></td>
                                <Td
                                        @if($lastInsurance->date_to == '0000-00-00')
                                        class="red"
                                        @endif
                                >
                                    {{ $lastInsurance->date_to }}
                                </td>
                            </tr>
                            <tr>
                                <td><label>Typ płatności:</label></td>
                                <Td>
                                    @if($lastInsurance->leasingAgreementPaymentWay)
                                        {{ $lastInsurance->leasingAgreementPaymentWay->name }}
                                    @else
                                        ---
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-12 col-sm-6">
                        <table class="table table-hover table-condensed">
                            <tr>
                                <td><label>Ubezpieczyciel:</label></td>
                                <Td>
                                    @if($lastInsurance->insuranceCompany)
                                        {{ $lastInsurance->insuranceCompany->name }}
                                    @else
                                        ---
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Składka leasingodawcy:</label></td>
                                <Td>{{ number_format($lastInsurance->contribution,2,"."," ") }} zł</td>
                            </tr>
                            <tr>
                                <td><label>Stawka leasingodawcy:</label></td>
                                <Td>{{ number_format($lastInsurance->rate,2,"."," ") }} %</td>
                            </tr>
                            <tr>
                                <td><label>Stawka leasingobiorcy:</label></td>
                                <Td>{{ number_format($lastInsurance->rate_lessor,2,"."," ") }} %</td>
                            </tr>
                            <tr>
                                <td><label>Składka leasingobiorcy:</label></td>
                                <Td>{{ number_format($lastInsurance->contribution_lessor,2,"."," ") }} zł</td>
                            </tr>
                            @if($lastInsurance->last_year_lessor_contribution != '0.00' && ! is_null($lastInsurance->last_year_lessor_contribution))
                                <tr>
                                    <td><label>Składka leasingobiorcy w ostatnim roku:</label></td>
                                    <Td>{{ number_format($lastInsurance->last_year_lessor_contribution,2,"."," ") }} zł</td>
                                </tr>
                            @endif
                            <tr>
                                <td><label>Stawka vbl:</label></td>
                                <Td>{{ number_format($lastInsurance->rate_vbl,2,"."," ") }} %</td>
                            </tr>

                            <tr>
                                <td><label>Zwrot składki:</label></td>
                                <Td>
                                    @if($lastInsurance->if_refund_contribution == '0')
                                        NIE
                                    @else
                                        {{ number_format($lastInsurance->refund,2,"."," ") }} zł
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Czy kontynuacja:</label></td>
                                <Td>
                                    @if($lastInsurance->if_continuation == '0')
                                        NIE
                                    @else
                                        TAK
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Decyzja obciążenia:</label></td>
                                <Td>
                                    @if($lastInsurance->if_load_decision == '0')
                                        NIE
                                    @else
                                        TAK
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/manage-actions/store-resume', [$leasingAgreement->id]), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="page-header marg-top-min">Dane polisy wznowienia</h4>
                        </div>
                        {{ Form::hidden('if_continuation', '1') }}
                        {{ Form::hidden('leasing_agreement_id', $leasingAgreement->id) }}
                        {{ Form::hidden('active', 1) }}
                        {{ Form::hidden('user_id', Auth::user()->id) }}
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nr polisy</label>
                                <div class="col-sm-8">
                                    <input value="" class="form-control " name="insurance_number"  placeholder="numer polisy">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nr zgłoszenia</label>
                                <div class="col-sm-8">
                                    <input value="{{ Auth::user()->insurances_global_nr }}" class="form-control" name="notification_number" disabled  placeholder="numer zgłoszenia">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Typ polisy</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insuranceType->name }}" class="form-control" readonly/>
                                    {{ Form::hidden('leasing_agreement_insurance_type_id', $insuranceType->id)}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Liczba miesięcy</label>
                                <div class="col-sm-8">
                                    @if($insuranceType->id != 14)
                                    <input value="{{ $insuranceType->months }}" class="form-control number" name="months" readonly id="months">
                                    @else
                                    <input type="text" class="form-control required" name="months" id="months" list="months_list" />
                                    <datalist id="months_list">
                                    @foreach($monthsRange as $month)
                                        <option value="{{$month}}">{{$month}}</option>
                                    @endforeach
                                    </datalist>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Data polisy</label>
                                <div class="col-sm-8">
                                    <input value="{{ $dates['from'] }}" class="form-control date" name="insurance_date" id="insurance_date" placeholder="data polisy">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Polisa od</label>
                                <div class="col-sm-8">
                                    <input value="{{ $dates['from'] }}" class="form-control date from required" date-opt="from"  name="date_from" id="date_from"  placeholder="polisa od">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Polisa do</label>
                                <div class="col-sm-8">
                                    <input value="{{ $dates['to'] }}" class="form-control date to required" date-opt="to" name="date_to" id="date_to"  placeholder="polisa do">
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
                                <label class="col-sm-4 control-label">Ubezpieczyciel</label>
                                <div class="col-sm-8">
                                    {{ Form::select('insurance_company_id', $insuranceCompanies, $lastInsurance->insurance_company_id, array('class' => 'form-control', 'id' => 'insurance_company_id') ) }}
                                </div>
                            </div>

                            <div class="form-group foreign-policy
                                @if($groups['status'] == 'error')
                                    has-error has-feedback tips" title="{{ $groups['msg']}}
                                @endif
                            ">
                                <label class="col-sm-4 control-label">Tabela stawek</label>
                                <div class="col-sm-8">
                                    {{ Form::select('group_id', $groups['groups'], $groups['group'], array('class' => 'form-control ', 'aria-describedby' => 'inputWarning2Status', 'id' => 'group')) }}
                                    @if($groups['status'] == 'error')
                                        <span class="glyphicon glyphicon-warning-sign warning_feedback form-control-feedback marg-right" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group foreign-policy
                                @if($groups['status'] == 'error')
                                    has-error has-feedback tips" title="{{ $groups['msg']}}
                                @endif
                            ">
                                <label class="col-sm-4 control-label">Grupa ubezpieczenia</label>
                                <div class="col-sm-8">
                                    {{ Form::select('leasing_agreement_insurance_group_row_id', $groups['rates'], (isset($groups['currentRate']) ? $groups['currentRate'] : null), array('class' => 'form-control ', 'aria-describedby' => 'inputWarning2Status', 'id' => 'group_rate')) }}
                                    @if($groups['status'] == 'error')
                                        <span class="glyphicon glyphicon-warning-sign warning_feedback form-control-feedback marg-right" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </div>
                            <div id="packages-container">

                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 control-label"></div>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input name="if_foreign_policy" value="1" type="checkbox"> Polisa obca
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group foreign-policy">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input name="if_rounding" id="if_rounding" value="1" type="checkbox" @if($if_rounding == 1) checked @endif> Zaokrąglaj wartości stawek i składek
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">    
                               
                                <div class="form-group foreign-policy">
                                    <label class="col-sm-4 control-label">Stawka leasingodawcy</label>
                                    <div class="col-sm-8">
                                        <input value="" class="form-control number currency_input required" name="rate"  placeholder="stawka leasingodawcy" id="rate" require>
                                    </div>
                                </div>
                                <div class="form-group foreign-policy">        
                                <label class="col-sm-4 control-label">Składka leasingodawcy</label>
                                <div class="col-sm-8">
                                    <table class="table-responsive">
                                <td style="padding-right: 5px"> 
                                    <div class="input-group">
                                        <input value="" class="form-control number currency_input required" name="contribution"  placeholder="składka leasingodawcy" id="contribution" require>
                                        <span class="input-group-btn">
                                            <span title="Kopiuj do leasingobiorcy" class="btn btn-warning btn-copy" id="copy_to_lessor"><i class="fa fa-copy"></i></span>
                                        </span>
                                    </div>
                                </td>
                                <td style="padding-right: 5px;">
                                    <div class="input-group">
                                    <input title="Prowizja" value="" style="margin: 0px" class="form-control number required" name="commission"  placeholder="Prowizja" id="commission">
                                    <span class="input-group-addon tips">%</span>
                                    </div>
                                </td> 
                                <td>
                                    <input  title="Wartość prowizji" value="" class="form-control number currency_input required" name="contribution_commission"  placeholder="Wartość prowizji" id="contribution_commission"> 
                                </td> 
                                </table>
                                </div>    
                            </div>                      

                            <div class="form-group foreign-policy
                             @if(isset($contribution['error']))
                                has-error has-feedback tips" title="{{ $contribution['error'] }}
                             @endif
                             "
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Płatn. ubezp. przez leasingobiorcę</label>
                                <div class="col-sm-8">
                                    @if($insuranceType->id != 14)
                                    {{ Form::input('text', '', $paymentWays[2], array('class' => 'form-control', 'readonly') ) }}
                                    {{ Form::hidden('leasing_agreement_payment_way_id', 2, array('id' => 'leasing_agreement_payment_way_id'))}}
                                    @else
                                    {{ Form::select('leasing_agreement_payment_way_id', $paymentWays, 2, array('class' => 'form-control', 'id' => 'leasing_agreement_payment_way_id') ) }}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group
                            @if(isset($contribution['error']))
                                has-error has-feedback tips" title="{{ $contribution['error'] }}
                            @endif
                            "  id="contribution_lessor-container">
                                <label class="col-sm-4 control-label">Składka leasingobiorcy</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input value="{{ checkIfEmpty('contribution_lessor', $contribution, 0) }}" class="form-control foreign-policy-input number currency_input required" name="contribution_lessor"  placeholder="składka leasingobiorcy" id="contribution_lessor" require>
                                        <span class="input-group-btn foreign-policy">
                                            <span title="Kopiuj do leasingodawcy" class="btn btn-warning btn-copy" id="copy_to_contribution">Kopiuj do leasingodawcy</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group
                            @if(isset($contribution['error']))
                                has-error has-feedback tips" title="{{ $contribution['error'] }}
                            @endif
                            ">
                                <label class="col-sm-4 control-label">Stawka leasingobiorcy</label>
                                <div class="col-sm-8">
                                    <input value="{{ checkIfEmpty('rate_lessor', $contribution, 0) }}" class="form-control foreign-policy-input  number currency_input required" name="rate_lessor"  placeholder="stawka leasingobiorcy" id="rate_lessor" require>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Stawka vbl</label>
                                <div class="col-sm-8">
                                    <input value="0" class="form-control number " name="rate_vbl"  placeholder="stawka vbl">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Czy kontynuacja</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="if_continuation" value="1" aria-label="..." checked disabled>
                                            {{ Form::hidden('if_continuation', 1)}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Decyzja obciążenia</label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="if_load_decision" value="1" aria-label="...">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row marg-top" id="acceptation-container" style="display: none;">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            <label class="checkbox-inline text-danger">
                                <input type="checkbox" name="acceptation" value="acceptation" class="required" required> Potwierdź świadomość, iż zakres polisy nie pokrywa się z numerem zgłoszenia
                            </label>
                        </div>
                    </div>
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Wprowadź wznowienie',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa zapisywanie wznowienia...'))  }}
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
        var acceptation_required = false;
        $(document).ready(function() {
            $( "form#page-form" ).submit(function(e) {
                var $btn = $('#form_submit').button('loading');
                if(! $('#page-form').valid() || (acceptation_required && !$('[name="acceptation"]').is(':checked'))) {
                    e.preventDefault();
                    $btn.button('reset');
                    return false;
                }
                return true;
            });

            $('input[name="if_foreign_policy"]').on('change', function(){
                if($(this).is(':checked') )
                {
                    $('.foreign-policy').hide();
                    $('.foreign-policy-input').removeClass('required').removeAttr('require');
                    $('#contribution').parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                    $('#rate').parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                    $('#contribution_lessor').parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                    $('#contribution_lessor').parent().parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                    $('#rate_lessor').parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                }else{
                    $('.foreign-policy').show();
                    $('.foreign-policy-input').addClass('required').attr('require', true);
                }
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

            $('#date_from').on('change', function(){
                var date_from = $(this).val();
                date_from = date_from.split('-');

                var notification_number = $('input[name="notification_number"]').val();
                notification_number = notification_number.split('/');

                if(date_from[0] != notification_number[1] || date_from[1] != notification_number[0])
                {
                    acceptation_required = true;
                    $('#acceptation-container').show();
                }else{
                    acceptation_required = false;
                    $('#acceptation-container').hide();
                }

            }).change();

            $('#group').on('change', function(){
                $.ajax({
                    url: "{{ URL::to('insurances/manage-actions/change-insurances-group') }}",
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

                        $('#group_rate').change();
                    }
                });
            });

            $('#insurance_company_id').on('change', function(){
                if(! $('input[name="if_foreign_policy"]').is(':checked') ) {
                    $.ajax({
                        url: "{{ URL::to('insurances/manage-actions/match-insurance-group', [$leasingAgreement->id]) }}/" + $(this).val(),
                        data: {
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function (data) {
                            $('#group').empty();
                            $.each(data.groups, function (i, group) {
                                $('#group').append('<option value="' + i + '">' + group + '</option>');
                            });
                            $('#group_rate').empty();
                            $.each(data.rates, function (i, rate) {
                                $('#group_rate').append('<option value="' + i + '">' + rate + '</option>');
                            });
                            if (data.if_rounding == '1') {
                                $('#if_rounding').prop("checked", true);
                            } else {
                                $('#if_rounding').prop("checked", false);
                            }
                            if (data.status == 'error') {
                                $('#group').parent().parent().addClass('has-error has-feedback tips');
                                $('#group_rate').parent().parent().addClass('has-error has-feedback tips');
                                $.notify({
                                    icon: "fa fa-warning",
                                    message: data.msg
                                }, {
                                    type: 'danger',
                                    placement: {
                                        from: 'bottom',
                                        align: 'right'
                                    },
                                    delay: 5000,
                                    timer: 500
                                });
                            } else {
                                $('#group_rate option[value=' + data.currentRate + ']').attr('selected', 'selected');
                                $('#group option[value=' + data.group + ']').attr('selected', 'selected');
                                $('#group').parent().parent().removeClass('has-error has-feedback tips');
                                $('#group_rate').parent().parent().removeClass('has-error has-feedback tips');
                                $('.warning_feedback').remove();
                            }
                            $('#group_rate').change();
                        }
                    });
                }
            });
            
            calcNewContribution();
            $('#group_rate, #if_rounding').on('change',function(){
                calcNewContribution();
            });

            $('#rate, #contribution, #rate_lessor, #contribution_lessor').on('change', function(){
                if(! $('input[name="if_foreign_policy"]').is(':checked') ) {
                    $.ajax({
                        url: "{{ URL::to('insurances/manage-actions/recalculate-rates', [$leasingAgreement->id]) }}",
                        data: {
                            element_value: $(this).val(),
                            element_name: $(this).attr('name'),
                            months: $('#months').val(),
                            if_rounding: $('#if_rounding').is(':checked'),
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function (data) {
                            if (!isset(data.error)) {
                                $('#' + data.element).val(data.value);
                            }
                        }
                    });
                }
            });


            $('#copy_to_lessor').on('click', function(){
                $('#contribution_lessor').val($('#contribution').val()).change();
            });
            $('#copy_to_contribution').on('click', function(){
                $('#contribution').val($('#contribution_lessor').val()).change();
            });

            $('#packages-container').on('change','.package_percentage', function(){
                var $percentage = $(this).data('percentage');
                $percentage = parseFloat($percentage) / 100;

                var $contribution = $('input[name="contribution"]').val();
                $contribution = parseFloat($contribution);

                var $contribution_lessor = $('input[name="contribution_lessor"]').val();
                $contribution_lessor = parseFloat($contribution_lessor);

                if($(this).is(":checked"))
                {
                    $contribution = ( $contribution * (1 + $percentage ) );
                    $contribution_lessor = ( $contribution_lessor * (1 + $percentage ) );
                }else{
                    $contribution = ( $contribution / (1 + $percentage)  );
                    $contribution_lessor = ( $contribution_lessor / (1 + $percentage)  );
                }
                $contribution = parseFloat($contribution).toFixed(2);
                $contribution_lessor = parseFloat($contribution_lessor).toFixed(2);

                $('input[name="contribution"]').val($contribution);
                $('input[name="contribution_lessor"]').val($contribution_lessor);
            });

            $('#packages-container').on('change','.package_amount', function(){
                var $amount = $(this).data('amount');
                $amount = parseFloat($amount);

                var $contribution = $('input[name="contribution"]').val();
                $contribution = parseFloat($contribution);

                var $contribution_lessor = $('input[name="contribution_lessor"]').val();
                $contribution_lessor = parseFloat($contribution_lessor);

                if($(this).is(":checked"))
                {
                    $contribution = ( $contribution  + $amount );
                    $contribution_lessor = ( $contribution_lessor + $amount  );
                }else{
                    $contribution = ( $contribution - $amount );
                    $contribution_lessor = ( $contribution_lessor - $amount );
                }
                $contribution = parseFloat($contribution).toFixed(2);
                $contribution_lessor = parseFloat($contribution_lessor).toFixed(2);

                $('input[name="contribution"]').val($contribution);
                $('input[name="contribution_lessor"]').val($contribution_lessor);
            });

            $('#contribution, #commission').on('change', function () {
                $('#contribution_commission').val(($('#contribution').val()*($('#commission').val()/100)).toFixed(2));
            })

            $('#months').on('change', function () {
                $.ajax({
                        url: "{{ URL::to('insurances/manage-actions/calculate-date-to') }}",
                        data: {
                            date_from: $('#date_from').val(),
                            months: $('#months').val(),
                        },
                        dataType: "json",
                        type: "POST",
                        success: function (data) {
                            if (!isset(data.error)) {
                                $('#date_to').val(data.date_to);
                            }
                        }
                    });
                calcNewContribution();
            })

            $('#months').keyup(function(e){
                console.log($(this).val());
                if ($(this).val() > 120) {            
                    $(this).val(120);              
                }
            });
        });

        function calcNewContribution(){
            $.ajax({
                    url: "{{ URL::to('insurances/manage-actions/calculate-new-contribution', [$leasingAgreement->id]) }}",
                    data: {
                        insurance_company_id: $('#insurance_company_id').val(),
                        group_id: $('#group').val(),
                        leasing_agreement_insurance_group_row_id: $('#group_rate').val(),
                        months: $('#months').val(),
                        leasing_agreement_payment_way_id: $("#leasing_agreement_payment_way_id").val(),
                        if_rounding: $('#if_rounding').is(':checked'),
                        multi_month: "{{$insuranceType->id != 14 ? 0 : 1}}",
                        _token: $('input[name="_token"]').val()
                    },
                    dataType: "json",
                    type: "POST",
                    success: function( data ) {
                        if(isset(data.error)){
                            $('#contribution').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#commission').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#contribution_commission').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#rate').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#contribution_lessor').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#rate_lessor').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);

                        }else {
                            $('#commission').val(data.commission).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#contribution').val(data.contribution).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#contribution_commission').val((data.contribution*(data.commission/100)).toFixed(2)).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#rate').val(data.rate).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#contribution_lessor').val(data.contribution_lessor).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#rate_lessor').val(data.rate_lessor).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');

                            $('#packages-container').html(data.packages);

                            if(isset(data.if_minimal.contribution))
                            {
                                $('#contribution-container').addClass('has-warning tips').attr('title', 'nie osiągnięto wartości minimalnej');
                                $('#contribution').change();
                            }else{
                                $('#contribution-container').removeClass('has-warning tips').removeAttr('title');
                            }

                            if(isset(data.if_minimal.contribution_lessor))
                            {
                                $('#contribution_lessor-container').addClass('has-warning tips').attr('title', 'nie osiągnięto wartości minimalnej');
                                $('#contribution_lessor').change();
                            }else{
                                $('#contribution_lessor-container').removeClass('has-warning tips').removeAttr('title');
                            }
                        }
                    }
                });
        }
    </script>

    @include('insurances.manage.partials.check-owner')
@stop

