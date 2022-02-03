@extends('layouts.main')

@section('header')
    Sprawa nr {{ $task->case_nb }}
    @if($task->currentInstance && $task->currentInstance->task_step_id == 1)
    <span class="btn btn-xs btn-primary task-collect marg-left" data-task="{{ $task->currentInstance->id }}">
        <i class="fa fa-check fa-fw"></i> Pobierz sprawę
    </span>
    @endif

    <div class="pull-right">
        <a href="{{{ Session::get('tasks.previous_url', URL::to('tasks/list/unassigned')) }}}" class="btn btn-default">
            <i class="fa fa-arrow-left fa-fw"></i>
            Powrót
        </a>
    </div>
@stop

@section('main')
    <div class="row">
        @if(! $task->currentInstance)
            <div class="col-sm-12 text-center marg-btm">
                <form action="{{ url('tasks/assign') }}" method="POST">
                    {{ Form::token() }}
                    {{ Form::hidden('task_id', $task->id) }}
                    <button type="submit" class="btn btn-xs btn-primary">
                        <i class="fa fa-random fa-fw"></i> uruchom przydział
                    </button>
                </form>
            </div>
        @endif
        <div class="col-md-6">
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane sprawy</div>
                <table class="table">
                    <tr>
                        <td><label>Grupa:</label></td>
                        <td>
                            {{ $task->group ? $task->group->name : ''  }}
                        </td>
                    </tr>
                    <tr>
                        <td><label>Typ sprawy:</label></td>
                        <td>
                            @if($task->type) {{ $task->type->name }}  @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Temat:</label></td>
                        <Td>{{ $task->subject }}</td>
                    </tr>
                    <tr>
                        <td><label>Źródło:</label></td>
                        <td>{{ $task->sourceType->name }}</td>
                    </tr>
                    <tr>
                        <th>Od:</th>
                        <td>
                            {{ $task->from_email }}
                            @if($task->from_name && $task->from_name != '')
                                ({{$task->from_name }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Do:</th>
                        <td>
                            {{ $task->to_email }}
                            @if($task->to_name && $task->to_name != '')
                                ({{$task->to_name }})
                            @endif
                        </td>
                    </tr>
                    @if($task->cc_email)
                        <tr>
                            <th>DW:</th>
                            <td>
                                {{ $task->cc_email }}
                                @if($task->cc_name && $task->cc_name != '')
                                    ({{$task->cc_name }})
                                @endif
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td><label>Treść:</label></td>
                        <td style="padding: 0;">
                            <iframe id="iframe" style="width: 100%; border: none;">

                            </iframe>
                            <pre id="iframe-content" style="display: none;">
                                {{ closeHtmlTags($task->content) }}
                            </pre>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Źródło:</label></td>
                        <td>{{ $task->sourceType->name }}</td>
                    </tr>
                    <tr>
                        <td><label>Aktualny status:</label></td>
                        <td>{{ $task->currentInstance ? $task->currentInstance->step->name : '---'}}</td>
                    </tr>
                </table>
            </div>

            <div class="panel panel-default small">
                <div class="panel-heading ">Załączniki</div>
                <table class="table">
                    @foreach($task->files as $file)
                        <tr>
                            <td><label> {{ $file->original_filename }} </label></td>
                            <td>
                                <a class="btn btn-xs btn-info off-disable" href="{{ url('tasks/attachment', [$file->id]) }}">
                                    <i class="fa fa-floppy-o fa-fw"></i> pobierz
                                </a>

                                <span class="btn btn-xs btn-default modal-open-lg off-disable" target="{{ url('tasks/preview', [$file->id]) }}" data-toggle="modal" data-target="#modal-lg">
                                    <i class="fa fa-search fa-fw"></i> podgląd
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="panel panel-default small">
                <div class="panel-heading ">Powiązane szkody</div>
                <table class="table">
                    @foreach($task->injuries as $injury)
                        <tr>
                            <td>
                                <a class="list-group-item" href="{{ url('/injuries/info', [$injury->id]) }}">
                                    <i class="fa fa-arrow-circle-right fa-fw "></i>

                                    {{ $injury->case_nr }} <strong> | </strong> {{ $injury->vehicle->registration }} <strong> | </strong> {{ $injury->vehicle->nr_contract }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default small">
                <div class="panel-heading ">Obsługa zadania</div>
                <div class="list-group">
                @foreach($task->instances()->latest()->get() as $k => $instance)
                    <a href="#" class="list-group-item @if($k == 0) list-group-item-info @endif">
                        <h5 class="list-group-item-heading">
                            {{ $instance->user->name }}
                            <div class="list-statuses pull-right">
                            @if($instance->date_complete)
                                <span class="label label-primary d-block marg-btm">
                                    zakończenie
                                    {{ $instance->date_complete->format('Y-m-d H:i') }}
                                </span>
                            @else
                                @if($instance->date_collect)
                                    <span class="label label-success d-block marg-btm">
                                        pobrano
                                        {{ $instance->date_collect->format('Y-m-d H:i') }}
                                    </span>
                                @else
                                    <span class="label label-warning d-block marg-btm">
                                        oczekujące na pobranie
                                    </span>
                                @endif
                            @endif
                            </div>
                        </h5>
                        <p class="list-group-item-text">
                            <hr>
                            @foreach($instance->history as $history)
                                <p>
                                    <span class="small">{{ $history->created_at->format('Y-m-d H:i') }}</span>
                                    -
                                    {{ $history->step->name }}
                                    @if($history->description && $history->description != '')
                                        <i class="fa fa-info-circle fa-fw"></i>
                                        <em>{{ $history->description }}</em>
                                    @endif
                                </p>
                            @endforeach
                        </p>
                    </a>
                @endforeach
                </div>
            </div>
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    Komentarze
                    <div class="btn btn-xs btn-primary pull-right modal-open" target="{{ url('tasks/add-comment', [$task->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-fw fa-plus"></i> dodaj
                    </div>
                </div>
                <div class="list-group">
                    @foreach($task->comments as $comment)
                        <a href="#" class="list-group-item">
                            <p class="list-group-item-text">
                                {{ $comment->content }}
                            </p>
                            <p class="text-right">
                                <small class="text-muted">
                                    <i class="fa fa-clock-o fa-fw"></i> {{ $comment->created_at->format('Y-m-d H:i') }}
                                    {{ $comment->user->name }}
                                </small>
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="panel panel-default small">
                <div class="panel-heading ">
                    Odpowiedzi
                </div>
                <div class="list-group">
                    @foreach($task->replies as $reply)
                        <div class="list-group-item">
                            <div class="pull-right">
                                <small class="text-muted">
                                    <i class="fa fa-clock-o fa-fw"></i> {{ $reply->created_at->format('Y-m-d H:i') }}
                                    {{ $reply->user->name }}
                                </small>
                            </div>
                            <p class="list-group-item-text">
                                <a class="btn btn-xs btn-info off-disable" href="{{ url('tasks/attachment', [$reply->task_file_id]) }}">
                                    <i class="fa fa-floppy-o fa-fw"></i>
                                </a>
                                {{ $reply->receivers }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@stop


@section('headerJs')
    @parent
    <script>
        $(document).ready(function() {
            var frame = $("#iframe").contents().find('body');
            frame.html( $('#iframe-content').html() );

            $('#iframe-content').remove();

            var context = $('#iframe')[0].contentWindow.document;

            let iframe_height = context.body.scrollHeight;
            if(iframe_height > 500) { iframe_height = 500; }
            $('#iframe').css('height', iframe_height);

            $(document).on('click', '.task-collect', function(){
                let taskId = $(this).data('task');

                $.ajax({
                    type: "POST",
                    url: "{{ url('tasks/collect') }}",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        task_id: taskId
                    },
                    assync: false,
                    cache: false,
                    success: function (data) {
                        location.reload();
                    },
                    dataType: 'text'
                });
            });
        });
    </script>
@endsection