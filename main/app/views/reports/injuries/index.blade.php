@extends('layouts.main')

@section('header')
    Generowanie raportów DLS Pojazdy
@stop

@section('main')
    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport zleceń</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('orders')), 'class' => 'page-form', 'id' => 'orders' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'order_date_from', 'datepicker_id_to' => 'order_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport faktur</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('invoices')), 'class' => 'page-form', 'id' => 'injuries' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'invoice_date_from', 'datepicker_id_to' => 'invoice_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport CFM</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('cfm')), 'class' => 'page-form', 'id' => 'cfm' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'cfm_date_from', 'datepicker_id_to' => 'cfm_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport zleceń serwisy</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('ordersGarages')), 'class' => 'page-form', 'id' => 'orders_garages' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'order_garages_date_from', 'datepicker_id_to' => 'order_garages_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport Idea Bank</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('ideaBank')), 'class' => 'page-form', 'id' => 'ideaBank' )) }}
                    <div class="row marg-btm" >
                        <div class="col-sm-12 ">
                            <label>NIP klienta:</label>
                            <input type="text" name="nip"  class="form-control  required" required placeholder="podaj nr NIP klienta dla spraw"  >
                        </div>
                    </div>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'order_ideaBank_date_from', 'datepicker_id_to' => 'order_ideaBank_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport pożyczek konsumenckich Idea Bank</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('ideaBankLoan')), 'class' => 'page-form', 'id' => 'ideaBankLoan' )) }}
                    <div class="row marg-btm" >
                        <div class="col-sm-12 ">
                            <label>NIP klienta:</label>
                            <input type="text" name="nip"  class="form-control  required" required placeholder="podaj nr NIP klienta dla spraw"  >
                        </div>
                    </div>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'order_ideaBankLoan_date_from', 'datepicker_id_to' => 'order_ideaBankLoan_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport przekierowań</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('redirections')), 'class' => 'page-form', 'id' => 'redirections' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'redirections_date_from', 'datepicker_id_to' => 'redirections_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Zestawienie wystawionych upoważnień do naliczenia opłaty</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('attachment_12')), 'class' => 'page-form', 'id' => 'attachment_12' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'attachment_12_date_from', 'datepicker_id_to' => 'attachment_12_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">zał 12 C - raport szkód całkowitych i kradzieżowych</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('attachment_12c')), 'class' => 'page-form', 'id' => 'attachment_12c' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'attachment_12c_date_from', 'datepicker_id_to' => 'attachment_12c_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">zał nr 13 - raport szkód częściowych</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('attachment_13')), 'class' => 'page-form', 'id' => 'attachment_13' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'attachment_13_date_from', 'datepicker_id_to' => 'attachment_13_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">zestawienie dokumentów przy szkodach</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('docs')), 'class' => 'page-form', 'id' => 'docs' )) }}
                        @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'docs_date_from', 'datepicker_id_to' => 'docs_date_to'))
                        @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport zaległości</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('outdated')), 'class' => 'page-form', 'id' => 'outdated' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'outdated_date_from', 'datepicker_id_to' => 'outdated_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport parametrów podczas rejestracji szkody</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('original_parameters')), 'class' => 'page-form', 'id' => 'original_parameters' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'parameters_date_from', 'datepicker_id_to' => 'parameters_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">

                <div class="panel panel-primary ">
                    <div class="panel-heading">
                        <h3 class="panel-title">Raport zleceń zakończonych</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                {{ Form::open(array('url' => URL::route('reports.injuries.post', array('completed_orders')), 'class' => 'page-form', 'id' => 'completed_orders' )) }}
                                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'completed_orders_date_from', 'datepicker_id_to' => 'completed_orders_date_to'))
                                    @include('reports.partials.submit')
                                {{ Form::close() }}
                            </div>
                            <div class="col-md-4 text-center">
                                <a href="/reports/injuries/excluded" class="btn btn-info btn-block">
                                    <i class="fa fa-search fa-fw"></i>
                                    szkody wykluczone z raportu
                                    <span class="badge">{{ $skipped }}</span>
                                </a>

                                <span class="btn btn-primary marg-top fileinput-button let_disable">
                                    <i class="fa fa-upload"></i> Wgraj zestawienie</a>
                                    <form id="fileupload" method="POST">
                                        {{ Form::token() }}
                                        <input type="file" name="file" >
                                    </form>
                                </span>

                                <div id="progress" class="progress marg-top" >
                                    <div id="progress-bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport faktur rozliczonych</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('invoices_settled')), 'class' => 'page-form', 'id' => 'injuries_settled' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'injuries_settled_date_from', 'datepicker_id_to' => 'injuries_settled_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport warsztatów w grupie</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('branches')), 'class' => 'page-form', 'id' => 'branches' )) }}
                        @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport warsztatów z opiekunem</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('companiesWithGuardians')), 'class' => 'page-form', 'id' => 'guardians_with_branches' )) }}
                        @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport nabywcw</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('injuries_buyer')), 'class' => 'page-form', 'id' => 'injuries_buyer' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'injuries_buyer_date_from', 'datepicker_id_to' => 'injuries_buyer_date_to'))
                    @include('reports.partials.submit')
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
            $('form').each(function(e){
                $(this).validate().cancelSubmit = true;
            });

            $(".page-form").submit(function(e) {
                var self = this;

                e.preventDefault();

                if($(this).valid()){
                    self.submit();
                }else{
                    $('.form_submit').button('reset');
                }

                return false; //is superfluous, but I put it here as a fallback
            });

            $('.monthdate').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm",
                onClose: function(dateText, inst) {
                    $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                }
            });

            $('#fileupload').fileupload({
                singleFileUploads: true,
                url: "{{ URL::route('reports.injuries.post', array('uploadList')) }}",
                dataType: 'json',
                add: function (e, data) {
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    if (data.autoUpload || (data.autoUpload !== false &&
                        $(this).fileupload('option', 'autoUpload'))) {
                        data.process().done(function () {
                            data.submit();
                        });
                    }

                },
                done: function (e, data) {
                    var response = data.result;
                    if(response.status == 'success') location.reload();
                    else alert(response.msg);
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress-bar').css(
                        'width',
                        progress + '%'
                    ).attr('aria-valuenow', progress).html(progress + '%');
                }
            });
        });
    </script>
@stop
