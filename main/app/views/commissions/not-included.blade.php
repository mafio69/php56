@extends('layouts.main')

@section('header')
    Faktury EDB bez oznaczenia liczenia do prowizji
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
                <a href="{{ url('commissions/not-included') }}">Faktury EDB bez oznaczenia prowizji</a>
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
                            <i class="fa fa-search fa-fw"></i> filtruj <span class="badge">{{ $invoices->getTotal() }}</span>
                        </button>
                        <a class="btn btn-xs btn-danger" href="{{ Request::url() }}">
                            <i class="fa fa-remove fa-fw"></i> usuń filtry
                        </a>
                    </div>
                    <div class="form-group form-group-sm">
                        <div class="divider">|</div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label>Data wystawienia:</label><br/>
                        <input name="invoice_date_to" value="{{ Request::get('invoice_date_to') }}" type="text" class="form-control date" placeholder="data wystawienia faktury do" autocomplete="off">
                    </div>
                    <div class="form-group form-group-sm">
                        <div class="divider">|</div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label>Data faktury w systemie od:</label><br/>
                        <input name="create_date_from" value="{{ Request::get('create_date_from') }}" type="text" class="form-control date" placeholder="data faktury w systemie od" autocomplete="off">
                    </div>
                    <div class="form-group form-group-sm">
                        <label>Data faktury w systemie do:</label><br/>
                        <input name="create_date_to" value="{{ Request::get('create_date_to') }}" type="text" class="form-control date" placeholder="data faktury w systemie do" autocomplete="off">
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
        <th>data wystawienia</th>
        <th>data faktury w systemie</th>
        <th>nr faktury</th>
        <th>wartość faktury</th>
        <Th>serwis</Th>
        <th>NIP</th>
        </thead>
		<?php $lp = (($invoices->getCurrentPage()-1)*$invoices->getPerPage()) + 1;?>
        @foreach ($invoices as $invoice)
            <tr class="vertical-middle">
                <td>{{$lp++}}.</td>
                <td>
                    <a class="btn btn-info btn-xs" href="/injuries/info/{{ $invoice->injury_id }}#settlements" target="_blank">
                        <i class="fa fa-search fa-fw"></i> kartoteka szkody
                    </a>
                </td>
                <td>{{ ($invoice->invoice_date && $invoice->invoice_date != '0000-00-00') ? $invoice->invoice_date : '---' }}</td>
                <td>{{ ($invoice->created_at && $invoice->created_at != '0000-00-00') ? $invoice->created_at->format('Y-m-d') : '---' }}</td>
                <td>{{ $invoice->invoice_nr }}</td>
                <td>
                    @if($invoice->base_netto && $invoice->base_netto > 0)
                        {{ $invoice->base_netto }} PLN [podstawa netto]
                    @else
                        {{ $invoice->netto }} PLN [kwota netto]
                    @endif
                </td>
                <td>{{ ($invoice->injury->branch) ? $invoice->injury->branch->company->name : '---' }}</td>
                <td>{{ ($invoice->injury->branch) ? $invoice->injury->branch->company->nip : '---' }}</td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $invoices->appends(Input::query())->links() }}</div>
</div>

@stop

@section('headerJs')
    @parent
    <script>
        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
    </script>
@stop
