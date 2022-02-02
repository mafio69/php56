@extends('layouts.main')

@section('header')
    Generowanie raportów
@stop

@section('main')
    @include('insurances.reports.nav')
    {{-- Raport pełny --}}
    <div class="row marg-btm">
        <div class="col-sm-12 col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/generate'), 'id' => 'page-form' )) }}
                        <div class="row marg-btm">
                            <div class="col-sm-12 col-md-8 col-md-offset-2 text-center">
                                <label class="radio-inline"><strong>Polisy obce:</strong></label>
                                <label class="radio-inline">
                                    <input type="radio" name="if_foreign_policy"  value="1"> TAK
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="if_foreign_policy"  value="0" checked> NIE
                                </label>
                            </div>
                        </div>
                        <div class="row marg-btm">
                            <div class="col-sm-12 col-md-8 col-md-offset-2 text-center">
                                <label class="radio-inline">
                                    <input type="radio" name="refunds_type"  value="1" checked> zwroty EDB (polisy od 2015-01-20)
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="refunds_type"  value="2"> zwroty Greco (polisy do 2015-01-19)
                                </label>
                            </div>
                        </div>
                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Rodzaj raportu:</label>
                                <select class="form-control" name="report_type">
                                    <option value="complex" selected>Raport wg. ubezpieczyciela</option>
                                    <option value="re-invoices">Zestawienie refaktur</option>
                                    <option value="complex-refund">Raport wg. ubezpieczyciela - zwroty</option>
                                </select>
                            </div>
                        </div>
                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Ubezpieczyciel:</label>
                                {{ Form::select('insurance_company_id', $insuranceCompanies, 7, ['class' => 'form-control'])}}
                            </div>
                        </div>

                        <div class="row marg-btm" id="general-contract-container" style="display: none;">
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Umowa generalna:</label>
                                {{ Form::select('general_contract', $general_contracts, null, ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Spółka:</label>
                                {{ Form::select('owner_id', $owners, 3, ['class' => 'form-control'])}}
                            </div>
                        </div>
                        <div class="row marg-btm">
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label class="radio-inline">
                                    <input type="radio" name="if_sk"  value="0" checked> <strong>Wszystkie</strong>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="if_sk"  value="1"> <strong>Tylko /SK</strong>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="if_sk"  value="2"> <strong>Bez /SK</strong>
                                </label>
                            </div>
                        </div>

                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Raportowany miesiąc:</label>
                                <input value="{{ date('Y-m') }}" type="text" name="date" class="form-control required date" placeholder="wybierz datę" required >
                            </div>
                        </div>

                        <div class="row marg-btm">
                            <div class="col-sm-12 col-md-8 col-md-offset-2 text-center">
                                <label class="radio-inline">
                                    <input type="radio" name="if_trial"  value="1" checked> raport próbny
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="if_trial"  value="0"> raport docelowy
                                </label>
                            </div>
                        </div>

                        <div class="row marg-top">
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::submit('Generuj raport',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie raportu...', 'off-disable'))  }}
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
        $(document).ready(function(){
            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm",
                showButtonPanel: true,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                    $('.form_submit').button('reset');
                }
            });

            $('select[name="report_type"], select[name="if_foreign_policy"], select[name="insurance_company_id"], select[name="owner_id"], input[name="if_trial"], input[name="refunds_type"], select[name="general_contract"]').on('change', function(){
                $('.form_submit').button('reset');
            });

            $('select[name="insurance_company_id"], select[name="report_type"]').on('change', function(){
                var $insurance_company = $('select[name="insurance_company_id"]');
                var $report_type = $('select[name="report_type"]');

                var $insurance_company_text = $insurance_company.find("option:selected").text();

                if( ( $insurance_company_text.search('hestia') != '-1' || $insurance_company_text.search('Hestia') != '-1' || $insurance_company_text.search('HESTIA') != '-1') && $report_type.val() == 'complex')
                {
                    $('#general-contract-container').show();
                }else{
                    $('#general-contract-container').hide();
                }
            });
        });
    </script>
@stop


