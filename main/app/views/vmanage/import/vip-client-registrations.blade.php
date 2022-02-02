@extends('layouts.main')


@section('header')

    Rejestracje VIP dla importu z dnia {{ $import->created_at->format('Y-m-d H:i') }}
    <a href="{{ URL::to('vehicle-manage/import/vip-clients') }}" class="btn btn-sm btn-default pull-right">
        <i class="fa fa-arrow-left"></i> powrót
    </a>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead>
                                <th>lp.</th>
                                <th>rejestracja</th>
                                <th></th>
                            </thead>
                            <?php
                            $lp = (($registrations->getCurrentPage()-1)*$registrations->getPerPage()) + 1;
                            ?>
                            @foreach ($registrations as $registration)
                                <tr class="vertical-middle">
                                    <td width="20px">{{ $lp++ }}.</td>
                                    <td>{{ $registration->registration }}</td>
                                    <td>
                                        <span class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('vehicle-manage/import/detach-registration', [$registration->id]) }}">
                                            <i class="fa fa-trash fa-fw"></i>
                                            usuń z bazy
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div style="clear:both;">{{ $registrations->appends(Input::all())->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
