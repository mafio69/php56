@extends('layouts.main')

@section('header')
    Nieobecności pracownika {{$absence_user->name}}
    <a class="btn btn-default btn-xs pull-right" href="{{ URL::to('tasks/excludes') }}" >
        <i class="fa fa-arrow-circle-o-left fa-fw"></i>
        anuluj
    </a>
@stop

@section('main')

    <div class="row">
        <div class="panel panel-default col-sm-12 col-md-10">
            <table class="table table-hover  table-condensed">
                <thead>
                <Th style="width:30px;">lp.</th>
                <th>nr sprawy</th>
                <th>źródło</th>
                <th>grupa</th>
                <th>nadawca wiadomości</th>
                <th>temat wiadomości</th>
                <th>data przydzielenia</th>
                <th>data wpływu</th>
                <th></th>
                </thead>
                @foreach ($absence_user->taskInstances as $k => $taskInstance)
                    <tr class="vertical-middle">
                        <td>{{++$k}}.</td>
                        <td>
                            <a href="{{ url('tasks/list/show', [$taskInstance->task->id]) }}" class="btn btn-info btn-xs">
                                <i class="fa fa-eye fa-fw"></i>
                                {{ $taskInstance->task->case_nb }}
                            </a>
                        </td>
                        <td>{{ $taskInstance->task->sourceType->name }}</td>
                        <td>{{ $taskInstance->task->group ? $taskInstance->task->group->name : ''}}</td>
                        <td>{{ $taskInstance->task->from_name }} {{ $taskInstance->task->from_email ? '('.$taskInstance->task->from_email.')' : '' }}</td>
                        <td>{{ $taskInstance->task->subject }}</td>
                        <td>{{ $taskInstance->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $taskInstance->task->task_date ? $taskInstance->task->task_date->format('Y-m-d H:i') : '' }}</td>
                        <td>
                            <div class="btn btn-primary btn-xs modal-open" target="{{ url('tasks/excludes/distribute-single', [$absence_user->id, $taskInstance->id ]) }}" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-fw fa-exchange"></i> rozdaj zadanie
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop

