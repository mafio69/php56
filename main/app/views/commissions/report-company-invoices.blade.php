@extends('layouts.main')

@section('header')
    Rozliczenia - raport {{ $report->report_number }}
@stop

@include('commissions.nav-left')

@section('main')
    @if(! $report->is_uptodate)
        <div class="alert alert-warning" role="alert">
            Wprowadzone zmiany mogły spowodować nieakualne wyliczenia w raporcie
            <span class="btn btn-primary btn-sm  modal-open" target="{{ url('commissions/regenerate-trial-report', [$report->id]) }}" data-toggle="modal" data-target="#modal">
                <i class="fa fa-fw fa-rotate-left"></i>
                przelicz raport
            </span>
        </div>
    @endif
    <div class="col-sm-12">
        <div class="pull-left">
            <ul  class="nav nav-pills nav-injuries btn-sm">
                <li class="<?php if(Request::segment(2) == 'report-invoices') echo 'active'; ?> ">
                    <a href="{{ url('commissions/report-invoices', [$report->id]) }}">Faktury</a>
                </li>

                <li class="<?php if(Request::segment(2) == 'report-companies' || Request::segment(2) == 'report-company-invoices') echo 'active'; ?> ">
                    <a href="{{ url('commissions/report-companies', [$report->id]) }}">Firmy</a>
                </li>
            </ul>
        </div>
        <div class="pull-right">
            <a href="{{ url('commissions/report-companies', [$report->id]) }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left fa-fw"></i>
                powrót
            </a>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Faktury dla firmy <i>{{ $company->name }}</i></h4>
                <div class="col-sm-12">
                    <nav class="navbar navbar-default navbar-sm marg-top-min" >
                        <div class="container-fluid">
                            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                                <form class="navbar-form navbar-left" role="search">
                                    {{ Form::token() }}
                                    <div class="form-group form-group-sm text-center ">
                                        <label>Filtrowanie faktur</label><br/>
                                        <button type="submit" class="btn btn-xs btn-primary">
                                            <i class="fa fa-search fa-fw"></i> filtruj <span class="badge">{{ $commissions->getTotal() }}</span>
                                        </button>
                                        <a class="btn btn-xs btn-danger" href="{{ Request::url() }}">
                                            <i class="fa fa-remove fa-fw"></i> usuń filtry
                                        </a>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <div class="divider">|</div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label>Nr faktury:</label><br/>
                                        <input name="invoice_number" value="{{ Request::get('invoice_number') }}" type="text" class="form-control " placeholder="numer faktury" autocomplete="off">
                                    </div>

                                    <div class="form-group form-group-sm">
                                        <div class="divider">|</div>
                                    </div>
                                    <div class="btn-group" data-toggle="buttons">
                                        <label>&nbsp;</label><br/>
                                        <label class="btn btn-xs btn-primary off-disable @if(Request::get('empty_commission', 1) == 1)) active @endif">
                                            <input type="radio" name="empty_commission" autocomplete="off" value="1" @if(Request::get('empty_commission', 1) == 1) checked @endif>  <i class="fa fa-remove fa-fw"></i>
                                        </label>
                                        <label class="btn btn-xs btn-primary off-disable @if(Request::get('empty_commission', 1) == 0) active @endif">
                                            <input type="radio" name="empty_commission" autocomplete="off" value="0" @if(Request::get('empty_commission', 1) == 0) checked @endif> z brakami
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </nav>
                </div>
                <div id="table-container">
                    <form id="commissions-form">
                        {{ Form::token() }}
                        {{ Form::hidden('commission_report_id', $report->id) }}
                        <table class="table table-hover  table-condensed">
                            <thead>
                                @if($report->commission_step_id < 3)
                                <th>
                                    {{ Form::checkbox('checkall', 1, null, ['class' => 'check-all']) }}
                                </th>
                                @endif
                                <Th style="width:30px;">#</th>
                                <th>prowizja netto</th>
                                <th>nr szkody</th>
                                <th>nr faktury</th>
                                <th>data wystawienia</th>
                            </thead>
                            <?php $lp = (($commissions->getCurrentPage()-1)*$commissions->getPerPage()) + 1;?>
                            @foreach ($commissions as $commission)
                                <tr class="vertical-middle @if(! $commission->commission) bg-danger @endif">
                                    @if($report->commission_step_id < 3)
                                    <td>
                                        {{ Form::checkbox('commissions[]', $commission->id, null, ['class' => 'chkbox']) }}
                                    </td>
                                    @endif
                                    <td>{{$lp++}}.</td>
                                    <td>
                                        @if($commission->commission)
                                            {{ money_format("%.2n",$commission->commission).' zł'}}
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('injuries/info', [$commission->invoice->injury->id]) }}" class="btn btn-primary btn-xs" target="_blank">
                                            <i class="fa fa-eye fa-fw"></i>
                                            {{ $commission->invoice->injury->injury_nr }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $commission->invoice->invoice_nr }}
                                    </td>
                                    <td>
                                        {{ $commission->invoice->invoice_date }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </form>
                    @if($report->commission_step_id < 3)
                    <div class="pull-left">
                        <div class="btn btn-primary btn-sm modal-open btn-commission-action" target="{{ url('commissions/remove-from-report') }}" data-toggle="modal" data-target="#modal">
                            Pomiń <span class="badge remove">0</span>
                        </div>
                        <div class="btn btn-primary btn-sm modal-open btn-commission-action" target="{{ url('commissions/omit') }}" data-toggle="modal" data-target="#modal">
                            Nie licz do prowizji <span class="badge omit">0</span>
                        </div>
                    </div>
                    @endif
                    <div class="pull-right" style="clear:both;">{{ $commissions->appends(Input::query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('headerJs')
    @parent
    <script>
        countCommissions()

        var lastChecked = null;
        var checkedAll = false;
        function countCommissions()
        {
            var size = $('.chkbox:checked').length;
            $('.badge.remove').html(size);
            $('.badge.omit').html(size);

            if(size == '0')
            {
                $('.btn-commission-action').prop('disabled', true).addClass('disabled');
            }else{
                $('.btn-commission-action').prop('disabled', false).removeClass('disabled');
            }
        }
        $('.check-all').on('click', function(){
            if(!checkedAll){
                $('.chkbox').each(function(){
                    $(this).prop('checked', true);
                });
            }else{
                $('.chkbox').each(function(){
                    $(this).prop('checked', false);
                });
            }
            checkedAll = !checkedAll;
            countCommissions();
        });

        $('.chkbox').on('click', function(e) {
            if(!lastChecked) {
                lastChecked = this;
                countCommissions();
                return;
            }

            if(e.shiftKey) {
                var start = $('.chkbox').index(this);
                var end = $('.chkbox').index(lastChecked);

                $('.chkbox').slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);

            }

            lastChecked = this;
            countCommissions();
        });

        $('body').on('click', '.modal #remove', function () {
            $(this).button('loading');
            $.ajax({
                type: "POST",
                url: "{{ url('commissions/remove-from-report') }}",
                data: $('#commissions-form').serialize(),
                assync: false,
                cache: false,
                success: function (data) {
                    location.reload();
                }
            });
            return false;
        });

        $('body').on('click', '.modal #omit', function () {
            $(this).button('loading');
            $.ajax({
                type: "POST",
                url: "{{ url('commissions/omit') }}",
                data: $('#commissions-form').serialize() + "&" + $('#omission-form').serialize(),
                assync: false,
                cache: false,
                success: function (data) {
                    location.reload();
                }
            });
            return false;
        });
    </script>
@stop
