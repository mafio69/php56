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
                    {{ Form::open(array('url' => URL::to('insurances/info-insurances/update', [$insurance->id]), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Nr polisy</label>
                                    <div class="col-sm-8">
                                        <input value="{{ $insurance->insurance_number }}" class="form-control" name="insurance_number"  placeholder="numer polisy">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Nr zgłoszenia</label>
                                    <div class="col-sm-8">
                                        <input value="{{ $insurance->notification_number }}" class="form-control" name="notification_number" disabled placeholder="numer zgłoszenia">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Typ polisy</label>
                                    <div class="col-sm-8">
                                        {{ Form::select('leasing_agreement_insurance_type_id', $insuranceTypes, $insurance->leasing_agreement_insurance_type_id, array('class' => 'form-control')) }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Liczba miesięcy</label>
                                    <div class="col-sm-8">
                                        <input value="{{ $insurance->months }}" class="form-control number" name="months"  placeholder="liczba miesięcy">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Data polisy</label>
                                    <div class="col-sm-8">
                                        <input value="{{ checkIfEmpty($insurance->insurance_date, null, '') }}" class="form-control date" name="insurance_date" id="insurance_date" placeholder="data polisy">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Polisa od</label>
                                    <div class="col-sm-8">
                                        <input value="{{ checkIfEmpty($insurance->date_from, null, '') }}" class="form-control date from" date-opt="from"  name="date_from" id="date_from"  placeholder="polisa od">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Polisa do</label>
                                    <div class="col-sm-8">
                                        <input value="{{ checkIfEmpty($insurance->date_to, null, '') }}" class="form-control date to" date-opt="to" name="date_to" id="date_to"  placeholder="polisa do">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Typ płatności</label>
                                    <div class="col-sm-8">
                                        {{ Form::select('leasing_agreement_payment_way_id', $paymentWays, $insurance->leasing_agreement_payment_way_id, array('class' => 'form-control') ) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Ubezpieczyciel</label>
                                    <div class="col-sm-8">
                                        {{ Form::select('insurance_company_id', $insuranceCompanies, $insurance->insurance_company_id, array('class' => 'form-control') ) }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Składka</label>
                                    <div class="col-sm-8">
                                        <input value="{{ $insurance->contribution }}" class="form-control number currency_input" name="contribution"  placeholder="składka">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Stawka</label>
                                    <div class="col-sm-8">
                                        <input value="{{ $insurance->rate }}" class="form-control number " name="rate"  placeholder="stawka">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Stawka vbl</label>
                                    <div class="col-sm-8">
                                        <input value="{{ $insurance->rate_vbl }}" class="form-control number " name="rate_vbl"  placeholder="stawka vbl">
                                    </div>
                                </div>
                                @if($insurance->if_refund_contribution == 1)
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Zwrot składki</label>
                                    <div class="col-sm-8">
                                        <input class="form-control input-sm input-inline number currency_input" id="refund" name="refund" type="text" placeholder="kwota zwrotu" value="{{ $insurance->refund }}" >
                                    </div>
                                </div>
                                @endif
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Czy kontynuacja</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="if_continuation" value="1" aria-label="..."
                                                @if($insurance->if_continuation == 1)
                                                    checked
                                                @endif
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Decyzja obciążenia</label>
                                    <div class="col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="if_load_decision" value="1" aria-label="..."
                                                @if($insurance->if_load_decision == 1)
                                                   checked
                                                @endif
                                                >
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row marg-top">
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::submit('Zapisz zmiany',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa zapisywanie zmian...'))  }}
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

        });
    </script>

@stop