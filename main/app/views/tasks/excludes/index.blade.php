@extends('layouts.main')

@section('header')
    Nieobecności pracowników
    <a class="btn btn-primary btn-xs pull-right" href="{{ URL::to('tasks/excludes/create') }}" >
        <i class="fa fa-plus fa-fw"></i>
        wprowadź pracownika
    </a>
@stop

@section('main')

    <div class="row">
        <div class="panel panel-default col-sm-12 col-md-10">
            <table class="table table-hover table-condensed table-auto">
                <thead>
                    <Th style="width:30px;">lp.</th>
                    <th>Pracownik</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                @foreach ($users as $k => $absence_user )
                    <tr class="vertical-middle">
                        <td>{{ ++ $k }}.</td>
                        <td>
                            {{ $absence_user->name }}
                        </td>
                        <td>
                            <a href="{{ url('tasks/excludes/absences', [$absence_user->id]) }}" class="btn btn-info btn-xs">
                                nieobecności <span class="badge">{{ $absence_user->taskExcludes->count() }}</span>
                            </a>
                        </td>
                        <td>
                            @if($absence_user->taskInstances->count() > 0 )
                                <a class="btn btn-default btn-xs" href="{{ url('tasks/excludes/tasks', [$absence_user->id]) }}">
                                    <i class="fa fa-fw fa-search"></i> zadania pracownika
                                    <span class="badge">{{ $absence_user->taskInstances->count() }}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if($absence_user->taskInstances->count() > 0 )
                                <div class="btn btn-primary btn-xs modal-open" target="{{ url('tasks/excludes/distribute', [$absence_user->id]) }}" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-fw fa-exchange"></i> rozdaj zadania pracownika
                                    <span class="badge">{{ $absence_user->taskInstances->count() }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="pull-right" style="clear:both;">{{ $users->appends(Input::query())->links() }}</div>
        </div>
    </div>
@stop

