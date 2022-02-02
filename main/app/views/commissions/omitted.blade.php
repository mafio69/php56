@extends('layouts.main')

@section('header')
    Rozliczenia - nie liczone do prowizji
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
                        <label>Data faktury:</label><br/>
                        <input name="invoice_date_to" value="{{ Request::get('invoice_date_to') }}" type="text" class="form-control date" placeholder="data faktury do" autocomplete="off">
                    </div>
                    <div class="form-group form-group-sm">
                        <div class="divider">|</div>
                    </div>
                    <div class="btn-group" data-toggle="buttons">
                        <label>&nbsp;</label><br/>
                        <label class="btn btn-xs btn-primary off-disable @if(Request::get('empty_invoice_date', 1) == 1)) active @endif">
                            <input type="radio" name="empty_invoice_date" autocomplete="off" value="1" @if(Request::get('empty_invoice_date', 1) == 1) checked @endif>  <i class="fa fa-remove fa-fw"></i>
                        </label>
                        <label class="btn btn-xs btn-primary off-disable @if(Request::get('empty_invoice_date', 1) == 0) active @endif">
                            <input type="radio" name="empty_invoice_date" autocomplete="off" value="0" @if(Request::get('empty_invoice_date', 1) == 0) checked @endif> nie uwzględniaj bez daty faktury
                        </label>
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
        <th>data faktury</th>
        <th>nr faktury</th>
        <th>wartość faktury</th>
        <Th>serwis</Th>
        <th>NIP</th>
        </thead>
		<?php $lp = (($commissions->getCurrentPage()-1)*$commissions->getPerPage()) + 1;?>
        @foreach ($commissions as $commission)
            <tr class="vertical-middle">
                <td>{{$lp++}}.</td>
                <th>

                    @if($commission->omission_attachment)
                        <a href="{{ url('commissions/download-file', [$commission->omission_attachment]) }}" class="btn btn-primary btn-xs" off-disable>
                            <i class="fa fa-floppy-o fa-fw"></i>
                            pobierz
                        </a>
                    @endif

                        @if($commission->omission_reason)
                            <span class="label label-info pointer tips" title="{{ $commission->omission_reason }}">
                                komentarz <i class="fa fa-info-circle fa-fw "></i>
                            </span>
                        @endif
                </th>
                <td>{{ ($commission->invoice_date && $commission->invoice_date != '-0001-11-30 00:00:00') ? $commission->invoice_date->format('Y-m-d') : '---' }}</td>
                <td>{{ $commission->invoice->invoice_nr }}</td>
                <td>{{ $commission->invoice->base_netto }} PLN [podstawa netto]</td>
                <td>{{ ($commission->invoice->injury->branch) ? $commission->invoice->injury->branch->company->name : '---' }}</td>
                <td>{{ ($commission->invoice->injury->branch) ? $commission->invoice->injury->branch->company->nip : '---' }}</td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $commissions->appends(Input::query())->links() }}</div>
</div>

@stop

@section('headerJs')
    @parent
    <script>
        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
    </script>
@stop
