@extends('layouts.main')

@section('header')
    Rozliczenia - weryfikacja
@stop

@include('commissions.nav-left')

@section('main')
<div class="col-sm-12">
    <div class="pull-left">
        <ul  class="nav nav-pills nav-injuries btn-sm">
            <li class="<?php if(Request::segment(2) == 'new') echo 'active'; ?> ">
                <a href="{{ url('commissions/new') }}">Nowe</a>
            </li>

            <li class="<?php if(Request::segment(2) == 'verification') echo 'active'; ?> ">
                <a href="{{ url('commissions/verification') }}" >Weryfikacja</a>
            </li>

            <li class="<?php if(Request::segment(2) == 'settled') echo 'active'; ?> ">
                <a href="{{ url('commissions/settled') }}" >Rozliczone</a>
            </li>

            <li class="separated">|</li>

            <li class="<?php if(Request::segment(2) == 'omitted') echo 'active'; ?>">
                <a href="{{ url('commissions/omitted') }}">Nie liczone do prowizji</a>
            </li>

            <li class="separated">|</li>

            <li class="<?php if(Request::segment(2) == 'not-included') echo 'active'; ?>">
                <a href="{{ url('commissions/not-included') }}">Faktury bez oznaczenia prowizji</a>
            </li>
        </ul>
    </div>
</div>
<div id="table-container">
    <table class="table table-hover  table-condensed" id="users-table">
        <thead>
        <Th style="width:30px;">#</th>
        <th></th>
        <th></th>
        <th>nr raportu</th>
        <th>faktury</th>
        <th>grupowy</th>
        <th>data wygenerowania</th>
        <th></th>
        <th></th>
        </thead>
		<?php $lp = (($reports->getCurrentPage()-1)*$reports->getPerPage()) + 1;?>
        @foreach ($reports as $report)
            <tr class="vertical-middle @if(! $report->is_uptodate) bg-warning @endif">
                <td>{{$lp++}}.</td>
                <td>
                    @if(! $report->is_uptodate)
                        <span class="label label-warning">
                            potencjalnie nieaktualny
                        </span>
                    @endif
                </td>
                <td>
                    <a href="{{ url('commissions/download', [$report->filename]) }}" class="btn btn-primary btn-xs" off-disable>
                        <i class="fa fa-floppy-o fa-fw"></i>
                        pobierz
                    </a>
                </td>
                <td>{{ $report->report_number }}</td>
                <td>
                    <a href="{{ url('commissions/report-invoices', [$report->id]) }}" class="btn btn-primary btn-xs">
                        <i class="fa fa-eye fa-fw"></i>
                        {{ $report->commissions->count() }}
                        faktury
                    </a>
                </td>
                <td>
                    <i class="fa {{ ($report->is_individual == 1) ? 'fa-check' : 'fa-minus' }}"></i>
                </td>
                <td>
                    {{ $report->created_at->format('Y-m-d H:i') }}
                </td>
                <td>
                    <span class="btn btn-primary btn-xs modal-open" target="{{ url('commissions/regenerate-trial-report', [$report->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-fw fa-rotate-left"></i>
                        przelicz raport
                    </span>
                    <span class="btn btn-success btn-xs modal-open" target="{{ url('commissions/accept-settlement', [$report->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-check fa-fw"></i>
                        potwierdź rozliczenie
                    </span>
                </td>
                <td>
                    <span class="btn btn-danger btn-xs modal-open" target="{{ url('commissions/rollback-report', [$report->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-ban fa-fw"></i>
                        wycofaj raport
                    </span>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $reports->appends(Input::query())->links() }}</div>
</div>

@stop

@section('headerJs')
    @parent
    <script>

    </script>
@stop
