@extends('layouts.main')

@section('header')
    Słownik typów spraw
@stop

@section('main')
    @foreach ($taskGroups->chunk(3) as $taskGroup_row)
        <div class="row">
            @foreach($taskGroup_row as $taskGroup)
                <div class="col-lg-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            {{ $taskGroup->name }}
                            @if($taskGroup->taskSubGroups->count() == 0)
                                <span class="btn btn-xs btn-default pull-right modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/manage-types/create', [$taskGroup->id]) }}">
                                    <i class="fa fa-plus"></i>
                                </span>
                            @endif
                        </div>
                        <div class="panel-body">
                            @if($taskGroup->taskSubGroups->count() > 0)
                                @foreach($taskGroup->taskSubGroups as $taskSubGroup)
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            {{ $taskSubGroup->name }}
                                            <span class="btn btn-xs btn-primary pull-right modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/manage-types/create', [$taskGroup->id, $taskSubGroup->id]) }}">
                                                <i class="fa fa-plus"></i>
                                            </span>
                                        </div>
                                        <ul class="list-group">
                                            @foreach($taskSubGroup->taskTypes as $taskType)
                                                <li class="list-group-item">
                                                    {{ $taskType->name}}

                                                    <span class="btn btn-xs btn-warning pull-right  modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/manage-types/edit', [$taskType->id]) }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </span>
                                                    <span class="btn btn-xs btn-danger pull-right marg-right modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/manage-types/delete', [$taskType->id]) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            @else
                                <ul class="list-group">
                                    @foreach($taskGroup->taskTypes as $taskType)
                                        <li class="list-group-item">
                                            {{ $taskType->name}}

                                            <span class="btn btn-xs btn-warning pull-right  modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/manage-types/edit', [$taskType->id]) }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </span>
                                            <span class="btn btn-xs btn-danger pull-right marg-right modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/manage-types/delete', [$taskType->id]) }}">
                                            <i class="fa fa-trash"></i>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@stop

@section('headerJs')
    @parent
    <script>

    </script>
@endsection

