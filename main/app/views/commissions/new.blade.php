@extends('layouts.main')

@section('header')
    Rozliczenia - nowe
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
                <form class="navbar-form navbar-left" role="search" id="page-form">
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
                        <label>Data faktury w systemie:</label><br/>
                        <input name="invoice_date_to" value="{{ Request::get('invoice_date_to') }}" type="text" class="form-control date" placeholder="data faktury do" autocomplete="off">
                    </div>
                    <div class="form-group form-group-sm">
                        <div class="divider">|</div>
                    </div>
                    <div class="btn-group" data-toggle="buttons">
                        <label>&nbsp;</label><br/>
                        <label class="btn btn-xs btn-warning off-disable @if(Request::get('empty_invoice_date', 1) == 1)) active @endif">
                            <input type="radio" name="empty_invoice_date" autocomplete="off" value="1" @if(Request::get('empty_invoice_date', 1) == 1) checked @endif>  <i class="fa fa-remove fa-fw"></i>
                        </label>
                        <label class="btn btn-xs btn-info off-disable @if(Request::get('empty_invoice_date', 1) == 0) active @endif">
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
    <form id="list-form">
        {{ Form::token() }}
        <table class="table table-hover  table-condensed" id="users-table">
            <thead>
            <th></th>
            <Th style="width:30px;">#</th>
            <th>data faktury w systemie</th>
            <th>data faktury</th>
            <th>nr faktury</th>
            <th>wartość faktury</th>
            <th>nr szkody</th>
            <Th>serwis</Th>
            <th>NIP</th>
            </thead>
            <?php $lp = (($commissions->getCurrentPage()-1)*$commissions->getPerPage()) + 1;?>
            @foreach ($commissions as $commission)
                <tr class="vertical-middle @if(! $commission->company) danger @endif ">
                    <td>
                        {{ Form::checkbox('commissions[]', $commission->id, null, ['class' => 'chkbox']) }}
                    </td>
                    <td>{{$lp++}}.</td>
                    <td>
                        {{ $commission->created_at->format('Y-m-d') }}
                    </td>
                    <td>{{ ($commission->invoice_date && $commission->invoice_date != '-0001-11-30 00:00:00') ? $commission->invoice_date->format('Y-m-d') : '---' }}</td>

                    <td>{{ $commission->invoice->invoice_nr }}</td>
                    <td>{{ $commission->invoice->base_netto }} PLN [podstawa netto]</td>
                    <td>
                        <a href="{{ url('injuries/info', [$commission->invoice->injury->id]) }}" class="btn btn-primary btn-xs" target="_blank" off-disable>
                            <i class="fa fa-eye fa-fw"></i>
                            {{ $commission->invoice->injury->injury_nr }}
                        </a>
                    </td>
                    <td>{{ ($commission->company) ? $commission->company->name : '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>' }}</td>
                    <td>{{ ($commission->company) ? $commission->company->nip : '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i>' }}</td>
                </tr>
            @endforeach
        </table>
    </form>
    <div class="pull-right" style="clear:both;">{{ $commissions->appends(Input::query())->links() }}</div>

    <div  class="btn btn-xs btn-primary modal-open" target="{{ url('commissions/generate-trial-reports') }}" data-toggle="modal" data-target="#modal">
        <i class="fa fa-file-excel-o fa-fw"></i>
        generuj raporty próbny dla wszystkich <span class="badge">{{ $commissions->getTotal() }}</span>
    </div>
    <div  class="btn btn-xs btn-primary modal-open btn-commission-action" target="{{ url('commissions/generate-trial-reports/selected') }}" data-toggle="modal" data-target="#modal">
        <i class="fa fa-file-excel-o fa-fw"></i>
        generuj raporty próbny dla oznaczonych <span class="badge">0</span>
    </div>

</div>

@stop

@section('headerJs')
    @parent
    <script>
        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
        $('#modal').on('click', '#generate', function(){
            var $btn = $(this).button('loading');
            $.ajax({
                type: "POST",
                url: "{{ url('commissions/generate-trial-reports') }}",
                data: $('#page-form').serialize(),
                assync: false,
                cache: false,
                success: function (data) {
                   location.reload();
                }
            });
            return false;
        });
        $('#modal').on('click', '#generateselected', function(){
            var $btn = $(this).button('loading');
            $.ajax({
                type: "POST",
                url: "{{ url('commissions/generate-trial-reports') }}",
                data: $('#list-form').serialize(),
                assync: false,
                cache: false,
                success: function (data) {
                    location.reload();
                }
            });
            return false;
        });

        var lastChecked = null;
        function countCommissions()
        {
            var size = $('.chkbox:checked').length;
            $('.btn-commission-action .badge').html(size);

            if(size == '0')
            {
                $('.btn-commission-action').prop('disabled', true).addClass('disabled');
            }else{
                $('.btn-commission-action').prop('disabled', false).removeClass('disabled');
            }
        }

        countCommissions();

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
    </script>
@stop
