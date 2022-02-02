@extends('layouts.main')

@section('header')
    Zadania nowe
@stop

@section('main')
    @include('tasks.nav')
    <div class="panel panel-default">
    <table class="table table-hover  table-condensed">
        <thead>
        <Th style="width:30px;">lp.</th>
        <th>nr sprawy</th>
        <th>źródło</th>
        <th>grupa</th>
        <th>nadawca wiadomości</th>
        <th>temat wiadomości</th>
        <th>data wpływu</th>
        <th></th>
        </thead>
        <?php $lp = (($tasks->getCurrentPage()-1)*$tasks->getPerPage()) + 1;?>
        @foreach ($tasks as $task)
            <tr class="vertical-middle"
                @if( $task->id == Session::get('tasks.card'))
                    style="background-color: honeydew;"
                @endif
            >
                <td>{{$lp++}}.</td>

                <td>
                    <a href="{{ url('tasks/list/show', [$task->id]) }}" class="btn btn-info btn-xs">
                        <i class="fa fa-eye fa-fw"></i>
                        {{ $task->case_nb }}
                    </a>
                </td>
                <td>{{ $task->sourceType->name }}</td>
                <td>{{ $task->group ? $task->group->name : ''  }}</td>
                <td>{{ $task->from_name }} {{ $task->from_email ? '('.$task->from_email.')' : '' }}</td>
                <td>{{ $task->subject }}</td>
                <td>{{ $task->task_date ? $task->task_date->format('Y-m-d H:i') : '' }}</td>
                <td>
                    <form action="{{ url('tasks/assign') }}" method="POST">
                        {{ Form::token() }}
                        {{ Form::hidden('task_id', $task->id) }}
                        <button type="submit" class="btn btn-xs btn-primary">
                            <i class="fa fa-random fa-fw"></i> uruchom przydział
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $tasks->appends(Input::query())->links() }}</div>
    </div>
@stop

