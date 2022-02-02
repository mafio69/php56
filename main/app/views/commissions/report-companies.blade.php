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

                <li class="<?php if(Request::segment(2) == 'report-companies') echo 'active'; ?> ">
                    <a href="{{ url('commissions/report-companies', [$report->id]) }}">Serwisy</a>
                </li>
            </ul>
        </div>
        <div class="pull-right">
            <a href="{{ url('commissions/verification') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left fa-fw"></i>
                powrót
            </a>
        </div>
    </div>
    <hr>
    <div class="col-sm-12">
        <nav class="navbar navbar-default navbar-sm marg-top-min" >
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                    <form class="navbar-form navbar-left" role="search">
                        {{ Form::token() }}
                        <div class="form-group form-group-sm text-center ">
                            <label>Filtrowanie serwisów</label><br/>
                            <button type="submit" class="btn btn-xs btn-primary">
                                <i class="fa fa-search fa-fw"></i> filtruj <span class="badge">{{ $companies->getTotal() }}</span>
                            </button>
                            <a class="btn btn-xs btn-danger" href="{{ Request::url() }}">
                                <i class="fa fa-remove fa-fw"></i> usuń filtry
                            </a>
                        </div>
                        <div class="form-group form-group-sm">
                            <div class="divider">|</div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label>Nazwa serwisu:</label><br/>
                            <input name="company_name" value="{{ Request::get('company_name') }}" type="text" class="form-control " placeholder="nazwa serwisu" autocomplete="off">
                        </div>

                        <div class="form-group form-group-sm">
                            <div class="divider">|</div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label>NIP serwisu:</label><br/>
                            <input name="nip" value="{{ Request::get('nip') }}" type="text" class="form-control " placeholder="NIP serwisu" autocomplete="off">
                        </div>
                    </form>
                </div>
            </div>
        </nav>
    </div>
    <div id="table-container">
        <table class="table table-hover  table-condensed" id="users-table">
            <thead>
                <Th style="width:30px;">#</th>
                <th></th>
                <th>prowizja netto</th>
                <th>faktury</th>
                <th>serwis</th>
                <th>nip serwisu</th>
            </thead>
			<?php $lp = (($companies->getCurrentPage()-1)*$companies->getPerPage()) + 1;?>
            @foreach ($companies as $company)
                <tr class="vertical-middle @if($company->commissions->count() == 0) bg-danger @endif">
                    <td>{{$lp++}}.</td>
                    <td>
                        <form action="{{ url('commissions/generate-company-report', [$report->id, $company->id]) }}" target="_blank" method="post">
                            {{ Form::token() }}
                            <button type="submit" class="btn btn-primary btn-xs" off-disable>
                                <i class="fa fa-file-excel-o fa-fw"></i>
                                generuj raport
                            </button>
                        </form>
                    </td>
                    <td>
                        {{ money_format("%.2n", $company->invoiceCommissions->sum('commission')).' zł'}}
                    </td>
                    <td>
                        <a href="{{ url('commissions/report-company-invoices', [$report->id, $company->id]) }}" class="btn btn-primary btn-xs">
                            <i class="fa fa-eye fa-fw"></i>
                            {{ $company->invoiceCommissions->count() }}
                            faktury
                        </a>
                    </td>
                    <td>
                        {{ $company->name }}
                    </td>
                    <td>
                        {{ $company->nip }}
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="pull-right" style="clear:both;">{{ $companies->appends(Input::query())->links() }}</div>
    </div>

@stop

@section('headerJs')
    @parent
    <script>

    </script>
@stop
