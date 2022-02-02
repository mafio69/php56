@extends('layouts.main')

@section('header')

    Wprowadzanie nowej polisy do umowy nr {{ $leasingAgreement->nr_contract }}

    <div class="pull-right">
        <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/manage-actions/store-after-refund', [$leasingAgreement->id]), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="page-header marg-top-min">Dane polisy</h4>
                        </div>
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
                                    {{ Form::input('text', '', $insuranceTypes[$insurance->leasing_agreement_insurance_type_id], ['class'  => 'form-control', 'id' => 'insurance_type_id', 'readonly']) }}
                                    {{ Form::hidden('leasing_agreement_insurance_type_id', $insurance->leasing_agreement_insurance_type_id)}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Liczba rat</label>
                                <div class="col-sm-8">
                                    <input value="{{ $leasingAgreement->installments }}" class="form-control number" name="installments" id="installments" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Okres ubezpieczenia w miesiącach</label>
                                <div class="col-sm-8">
                                    @if($leasingAgreement->installments == $intervals['months'])
                                        <input value="{{ $intervals['months']}}" class="form-control number" name="months" id="months" readonly>
                                    @else
                                        {{ Form::select('months', ['' => '--- wybierz ilość miesięcy ---', $leasingAgreement->installments => $leasingAgreement->installments, $intervals['months'] => $intervals['months']], $insurance->months, ['class' => 'form-control', 'id' => 'months']) }}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Data polisy</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->date_to }}" class="form-control date" name="insurance_date" id="insurance_date" placeholder="data polisy" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Polisa od</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->date_to }}" class="form-control date  required" name="date_from" id="date_from"   placeholder="polisa od" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Polisa do</label>
                                <div class="col-sm-8">
                                    <input value="" class="form-control   required" name="date_to" id="date_to"    placeholder="polisa do" readonly>
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
                                    <p class="help-block">{{ $leasingAgreement->client->name }}</p>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group">
                                <p class="text-center col-sm-offset-4 col-sm-8 help-block">{{ ($insurance->insurance_company) ? $insurance->insurance_company->name : null }}</p>
                                <label class="col-sm-4 control-label">Ubezpieczyciel</label>
                                <div class="col-sm-8">
                                    {{ Form::select('insurance_company_id', $insuranceCompanies, $insurance->insurance_company_id, array('class' => 'form-control', 'id' => 'insurance_company_id') ) }}
                                </div>
                            </div>

                            <div class="form-group foreign-policy">
                                <label class="col-sm-4 control-label">Tabela stawek</label>
                                <div class="col-sm-8">
                                    {{ Form::select('group_id', [], null, array('class' => 'form-control ', 'aria-describedby' => 'inputWarning2Status', 'id' => 'group')) }}
                                </div>
                            </div>
                            <div class="form-group foreign-policy">
                                <label class="col-sm-4 control-label">Grupa ubezpieczenia</label>
                                <div class="col-sm-8">
                                    {{ Form::select('leasing_agreement_insurance_group_row_id', [], null, array('class' => 'form-control ', 'aria-describedby' => 'inputWarning2Status', 'id' => 'group_rate')) }}
                                </div>
                            </div>
                            <div id="packages-container">

                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 control-label"></div>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input name="if_foreign_policy" value="1" type="checkbox" @if($insurance->if_foreign_policy == 1) checked @endif> Polisa obca
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
                            <div class="form-group foreign-policy">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input name="if_full_year" id="if_full_year" value="1" type="checkbox"> Zaokrąglaj składki do pełnych lat
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group foreign-policy" id="contribution-container">
                                <label class="col-sm-4 control-label">Składka leasingodawcy</label>
                                <div class="col-sm-8">
                                    <input value="" class="form-control number currency_input required" name="contribution"  placeholder="składka leasingodawcy" id="contribution" require>
                                </div>
                            </div>
                            <div class="form-group foreign-policy">
                                <label class="col-sm-4 control-label">Stawka leasingodawcy</label>
                                <div class="col-sm-8">
                                    <input value="" class="form-control number currency_input required" name="rate"  placeholder="stawka leasingodawcy" id="rate" require>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Płatn. ubezp. przez leasingobiorcę</label>
                                <div class="col-sm-8">
                                    {{ Form::input('text', '', ($leasingAgreement->leasing_agreement_payment_way_id) ? $paymentWays[$leasingAgreement->leasing_agreement_payment_way_id] : '---', array('class' => 'form-control', 'readonly') ) }}
                                    {{ Form::hidden('leasing_agreement_payment_way_id', $leasingAgreement->leasing_agreement_payment_way_id)}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Stawka leasingobiorcy</label>
                                <div class="col-sm-8">
                                    <input value="" class="form-control  foreign-policy-input number currency_input required" name="rate_lessor"  placeholder="stawka leasingobiorcy" id="rate_lessor" require>
                                </div>
                            </div>
                            <div class="form-group"  id="contribution_lessor-container">
                                <label class="col-sm-4 control-label">Składka leasingobiorcy</label>
                                <div class="col-sm-8">
                                    <input value="{{$insurance->contribution_lessor}}" class="form-control  foreign-policy-input number currency_input required" name="contribution_lessor"  placeholder="składka leasingobiorcy" id="contribution_lessor" require>
                                </div>
                            </div>

                            <div class="form-group" id="last_year_lessor_contribution_container" @if($insurance->last_year_lessor_contribution == '0.00' || is_null($insurance->last_year_lessor_contribution))style="display: none;" @endif>
                                <label class="col-sm-4 control-label">Składka leasingobiorcy w ostatnim roku</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->last_year_lessor_contribution }}" class="form-control  foreign-policy-input number currency_input required" name="last_year_lessor_contribution"  placeholder="składka leasingobiorcy w ostatnim roku" id="last_year_lessor_contribution" require>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Stawka vbl</label>
                                <div class="col-sm-8">
                                    <input value="{{ $insurance->rate_vbl }}" class="form-control number " name="rate_vbl"  placeholder="stawka vbl">
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
                            {{ Form::submit('Wprowadź polisę',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa wprowadzanie polisy...'))  }}
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
                    $('#rate_lessor').parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                }else{
                    $('.foreign-policy').show();
                    $('.foreign-policy-input').addClass('required').attr('require', true);
                }
            }).change();

            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd",
                onClose: function( selectedDate ) {
                    if($(this).attr('date-opt') == 'from'){
                        $( '#date_to' ).datepicker( "option", "minDate", selectedDate );
                    }else if( $(this).attr('date-opt') == 'to' ) {
                        $( '#date_from' ).datepicker("option", "maxDate", selectedDate);
                    }
                }
            });

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

                            if (data.if_full_year == '1') {
                                $('#if_full_year').prop("checked", true);
                            } else {
                                $('#if_full_year').prop("checked", false);
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
            }).change();

            $('#group_rate, #months, #if_rounding, #if_full_year').on('change',function(){
                $.ajax({
                    url: "{{ URL::to('insurances/manage-actions/calculate-new-contribution', [$leasingAgreement->id]) }}",
                    data: {
                        insurance_company_id: $('#insurance_company_id').val(),
                        group_id: $('#group').val(),
                        leasing_agreement_insurance_group_row_id: $('#group_rate').val(),
                        months: $('#months').val(),
                        leasing_agreement_payment_way_id: $('input[name="leasing_agreement_payment_way_id"]').val(),
                        if_rounding: $('#if_rounding').is(':checked'),
                        if_full_year: $('#if_full_year').is(':checked'),
                        insurance_id: "{{ $insurance->id }}",
                        _token: $('input[name="_token"]').val()
                    },
                    dataType: "json",
                    type: "POST",
                    success: function( data ) {
                        if(isset(data.error)){
                            $('#contribution').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#rate').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#contribution_lessor').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#rate_lessor').val('').parent().parent().addClass('has-error has-feedback tips').attr('title', data.error);
                            $('#last_year_lessor_contribution').val('').parent().addClass('has-error has-feedback tips').attr('title', data.error);
                        }else {
                            $('#contribution').val(data.contribution).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#rate').val(data.rate).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#contribution_lessor').val(data.contribution_lessor).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            $('#rate_lessor').val(data.rate_lessor).parent().parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                            if(data.last_year_lessor_contribution != '0.00'){
                                $('#last_year_lessor_contribution').val(data.last_year_lessor_contribution).parent().removeClass('has-error has-feedback tips').removeAttr('title').tooltip('hide');
                                $('#last_year_lessor_contribution_container').show();

                            }else{
                                $('#last_year_lessor_contribution_container').hide();
                                $('#last_year_lessor_contribution').val('');
                            }

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

                            $('.package').each(function(){
                                if($(this).is(':checked'))
                                    $(this).change();
                            });
                        }
                    }
                });
            }).change();

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
                                if (data.element2 != '')
                                    $('#' + data.element2).val(data.value2);
                            }
                        }
                    });
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

        });
    </script>

    @include('insurances.manage.partials.check-owner')
@stop