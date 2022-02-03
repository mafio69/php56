@extends('layouts.main')

@section('header')
    Wykaz wygenerowanych raportów
@stop

@section('main')
    @include('insurances.reports.nav')
    <div style="display: inline-block;">
    <table class="table table-hover  table-condensed" style="width: auto">
        <thead>
            <Th style="width:30px;">lp.</th>
            <th>typ raportu</th>
            <th>czy próbny</th>
            <th>raportowany miesiąc</th>
            <th>ubezpieczyciel</th>
            <Th>spółka</Th>
            <Th>zakres umów</Th>
            <th>generujący</th>
            <th>wersja</th>
            <th></th>
        </thead>
        <?php $lp = (($reports->getCurrentPage()-1)*$reports->getPerPage()) + 1;?>
        @foreach ($reports as $report)
            <tr class="vertical-middle">
                <td>{{$lp++}}.</td>
                <td>
                    @if( $report->type == 'complex')
                        Raport wg. ubezpieczyciela
                    @elseif($report->type == 're-invoices')
                        Zestawienie refaktur
                    @else
                        Raport wg. ubezpieczyciela - zwroty
                    @endif

                </td>
                <td class="text-center">
                    @if($report->if_trial == 1)
                        <i class="fa fa-check"></i>
                    @else
                        <i class="fa fa-minus"></i>
                    @endif
                </td>
                <td>
                    {{ $report->insurances_global_nr }}
                </td>
                <td>{{ $report->insurance_company->name }}</td>
                <Td>{{ ($report->owner) ? $report->owner->name.($report->owner->old_name ? ' ('.$report->owner->old_name.')' : '') : '---'}}</Td>
                <td>
                    @if($report->if_sk == 0)
                        wszystkie
                    @elseif($report->if_sk == 1)
                        tylko /SK
                    @elseif($report->if_sk == 2)
                        bez /SK
                    @endif
                </td>
                <td>{{ $report->user->name }} - {{ $report->created_at->format('Y-m-d H:i') }}</td>
                <Td class="text-center">@if($report->version){{ $report->version }}@else <i>bazowa</i> @endif</Td>
                <td>
                    @if(!is_null($report->pathToFile) )
                        <a href="{{  URL::to('insurances/reports/download', [$report->id]) }}" class="btn btn-primary btn-sm" off-disable="true">
                            <i class="fa fa-download"></i> pobierz
                        </a>
                    @else
                        <span class="btn btn-sm btn-danger" disabled="">
                            <i class="fa fa-download"></i> plik raportu niedostępny
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $reports->links() }}</div>
    </div>
@stop



