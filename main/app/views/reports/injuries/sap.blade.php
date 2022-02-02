@extends('layouts.main')

@section('header')
    Raporty komunikatów o braku polisy w SAP
@stop

@section('main')
    <table class="table  table-hover table-auto table-condensed" >
        <thead>
            <Th style="width:30px;">lp.</th>
            <th>raport na dzień</th>
            <th></th>
        </thead>
        <tbody>
            <?php $lp = (($reports->getCurrentPage()-1)*$reports->getPerPage()) + 1; ?>

            @foreach($reports as $index => $report)
                <tr>
                    <td>
                        {{ $lp++ }}
                    </td>
                    <td>
                        {{ $report->report_date->format('Y-m-d') }}
                    </td>
                    <td>
                        <a href="{{ URL::route('reports.injuries.get', ['downloadSap']) }}?report_id={{ $report->id }}" class="btn btn-primary btn-xs">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            pobierz
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection