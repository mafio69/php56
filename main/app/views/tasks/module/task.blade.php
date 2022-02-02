<?php $referer = explode('/', Request::server('HTTP_REFERER'));?>
<div class="panel panel-default">
    <div class="panel-heading text-center" style="padding: 10px 15px 20px 15px;">
        <div class="pull-left text-left">
            <span class="small">
                {{ $taskInstance->task->task_date ? $taskInstance->task->task_date->format('Y-m-d H:i') : $taskInstance->task->created_at->format('Y-m-d H:i') }}
            </span>
            <br>
            <span class="label label-{{ $taskInstance->task->sourceType->style }}">
                {{ $taskInstance->task->sourceType->name }}
            </span>
        </div>
        <small class="text-center">
            {{ $taskInstance->task->case_nb }} | {{ $taskInstance->step->name }}
            @if( $taskInstance->latestHistory->description)
                <span class="btn btn-info btn-xs">
                    <i class="fa fa-info-circle" data-toggle="tooltip" title="{{ $taskInstance->latestHistory->description }}"></i>
                </span>
            @endif
        </small>
        <span class="pull-right small btn btn-xs btn-default switch-tab" data-tab="{{ $taskInstance->step->section }}" >
           <i class="fa fa-arrow-left fa-fw"></i> powrót
        </span>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-11">
                <table class="table table-condensed">
                    <tr>
                        <th class="text-right">Grupa:</th>
                        <td>{{ $taskInstance->task->group ? $taskInstance->task->group->name : '' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Typ sprawy:</th>
                        <td>
                            @if($taskInstance->task->type)
                                {{ $taskInstance->task->type->name }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Temat:</th>
                        <td>
                            {{ $taskInstance->task->subject }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Źródło:</th>
                        <td>{{ $taskInstance->task->sourceType->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Od:</th>
                        <td>
                            {{ $taskInstance->task->from_email }}
                            @if($taskInstance->task->from_name && $taskInstance->task->from_name != '')
                                ({{$taskInstance->task->from_name }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Do:</th>
                        <td>
                            {{ $taskInstance->task->to_email }}
                            @if($taskInstance->task->to_name && $taskInstance->task->to_name != '')
                                ({{$taskInstance->task->to_name }})
                            @endif
                        </td>
                    </tr>
                    @if($taskInstance->task->cc_email)
                        <tr>
                            <th class="text-right">DW:</th>
                            <td>
                                {{ $taskInstance->task->cc_email }}
                                @if($taskInstance->task->cc_name && $taskInstance->task->cc_name != '')
                                    ({{$taskInstance->task->cc_name }})
                                @endif
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-sm-12 col-md-1" style="padding-left: 5px; padding-right: 5px;" >
                @if($taskInstance->ongoing)
                    <span class="btn btn-xs btn-success btn-block  modal-open tips" title="zakończ" data-container="body" target="{{ url('tasks/complete', [$taskInstance->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-check-square"></i>
                    </span>
                    <span class="btn btn-xs  btn-default btn-block  modal-open tips" title="zakończ bez czynności" data-container="body" target="{{ url('tasks/complete-without-action', [$taskInstance->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-ban"></i>
                    </span>
                    <span class="btn btn-xs btn-default btn-block  modal-open tips" title="przekaż sprawę" data-container="body" target="{{ url('tasks/pass-task', [$taskInstance->id]) }}" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-transgender"></i>
                    </span>
                    @if($taskInstance->task->from_email)
                        <a class="btn btn-xs btn-block  btn-info tips" title="odpowiedz" data-container="body" href="{{ url('tasks/reply', [$taskInstance->id]) }}">
                            <i class="fa fa-mail-reply"></i>
                        </a>
                    @endif
                    <a class="btn btn-xs btn-block  btn-info tips" title=" przekaż dalej" data-container="body" href="{{ url('tasks/forward', [$taskInstance->id]) }}">
                        <i class="fa fa-mail-forward"></i>
                    </a>
                    @if(
                            Auth::user()->can('zlecenia_(szkody)#zarzadzaj') 
                            && $taskInstance->task->source_type == 'MobileInjury' 
                            
                        )
                        @if($taskInstance->task->source)
                            <a class="btn btn-xs btn-block  btn-primary tips" title="przyjmij szkodę" data-container="body" href="{{ URL::to('injuries/make/create-new-entity-mobile', array($taskInstance->task->source_id)) }}" >
                                <i class="fa fa-check"></i>
                            </a>
                        @else
                            <span class="label label-danger tips" title="Zgłoszenie zostało usunięte " data-container="body">
                                <i class="fa fa-trash-o"></i>

                            </span>
                        @endif
                    @endif
                @elseif(! $taskInstance->completed)
                    <span class="btn btn-xs btn-block  btn-primary tips" title="Pobierz sprawę" data-container="body" id="task-collect" data-task="{{ $taskInstance->id }}">
                        <i class="fa fa-check"></i>
                    </span>
                @endif
            </div>
        </div>
        <hr>
        <h5>Treść:</h5>
        <iframe id="task-module-iframe" style="width: 100%;">
        </iframe>
        <pre id="task-module-iframe-content" style="display: none;">
            {{ closeHtmlTags($taskInstance->task->content) }}
        </pre>
        <hr>
        <h5>Załączniki <span class="badge">{{ $taskInstance->task->files->count() }}</span></h5>
        <ul class="list-group">
        @foreach($taskInstance->task->files as $file)
            <li class="list-group-item">
                @if(
                    (Request::segment(1) == 'injuries' && Request::segment(2) == 'info' && $taskInstance->task->injuries->contains( Request::segment(3) ))
                    ||
                    (
                        isset($referer[3]) && $referer[3] == 'injuries'
                        &&
                        isset($referer[4]) && $referer[4] == 'info'
                        &&
                        isset($referer[5]) && $taskInstance->task->injuries->contains( $referer[5] )
                    )
                )
                    <div class="checkbox" style="display: inline-block">
                        <label>
                            <input type="checkbox" name="taskFiles[]" value="{{ $file->id }}">
                        </label>
                    </div>
                @endif
                <a class="btn btn-xs btn-info off-disable" href="{{ url('tasks/attachment', [$file->id]) }}">
                    <i class="fa fa-floppy-o fa-fw"></i>
                </a>
                <span class="btn btn-xs btn-default modal-open-lg off-disable" target="{{ url('tasks/preview', [$file->id]) }}" data-toggle="modal" data-target="#modal-lg">
                    <i class="fa fa-search fa-fw"></i>
                </span>

                {{ $file->original_filename }}

                @if(
                    isset($injury) &&
                    (
                        (Request::segment(1) == 'injuries' && Request::segment(2) == 'info' && $taskInstance->task->injuries->contains( Request::segment(3) ))
                        ||
                        (
                            isset($referer[3]) && $referer[3] == 'injuries'
                            &&
                            isset($referer[4]) && $referer[4] == 'info'
                            &&
                            isset($referer[5]) && $taskInstance->task->injuries->contains( $referer[5] )
                        )
                    )
                )
                    @if($injury->documents()->where('file', $file->filename)->count() > 0)
                        <span class="pull-right tips" title="dołączony">
                            <i class="fa fa-check fa-fw"></i>
                        </span>
                    @else
                        <span class="pull-right">
                            <i class="fa fa-fw"></i>
                        </span>
                    @endif
                    <span class="btn btn-primary btn-xs pull-right modal-open-lg marg-right tips" target="{{ url('tasks/injury-file-type', [$file->id, (
                            (Request::segment(1) == 'injuries' && Request::segment(2) == 'info') ? Request::segment(3)
                            : $referer[5]
                        )]) }}" data-toggle="modal" data-target="#modal-lg" title="dołącz">
                        <i class="fa fa-file fa-fw"></i>
                    </span>
                    <span class="btn btn-info btn-xs marg-right pull-right modal-open-lg tips" target="{{ url('tasks/injury-image-type', [$file->id, (
                            (Request::segment(1) == 'injuries' && Request::segment(2) == 'info') ? Request::segment(3)
                            : $referer[5]
                        )]) }}" data-toggle="modal" data-target="#modal-lg" title="dołącz">
                        <i class="fa fa-photo fa-fw"></i>
                    </span>

                @endif
            </li>
        @endforeach
        @if(
            (Request::segment(1) == 'injuries' && Request::segment(2) == 'info' && $taskInstance->task->injuries->contains( Request::segment(3) ))
                    ||
            (
                isset($referer[3]) && $referer[3] == 'injuries'
                &&
                isset($referer[4]) && $referer[4] == 'info'
                &&
                isset($referer[5]) && $taskInstance->task->injuries->contains( $referer[5] )
            )
        )
            <li class="list-group-item">
                <span class="btn btn-primary btn-xs modal-open-lg" target="{{ url('tasks/injury-file-types', [(
                            (Request::segment(1) == 'injuries' && Request::segment(2) == 'info') ? Request::segment(3)
                            : $referer[5]
                        )]) }}" data-toggle="modal" data-target="#modal-lg">
                    <i class="fa fa-file fa-fw"></i> dołącz jako dokument
                </span>
                <span class="btn btn-info btn-xs modal-open-lg" target="{{ url('tasks/injury-image-types', [(
                            (Request::segment(1) == 'injuries' && Request::segment(2) == 'info') ? Request::segment(3)
                            : $referer[5]
                        )]) }}" data-toggle="modal" data-target="#modal-lg">
                    <i class="fa fa-photo fa-fw"></i> dołącz jako zdjęcie
                </span>
            </li>
        @endif
        </ul>
        <hr>

        <h5>
            @if(
                $taskInstance->ongoing &&
                (
                    (Request::segment(1) == 'injuries' && Request::segment(2) == 'info' && ! $taskInstance->task->injuries->contains( Request::segment(3) ))
                    ||
                    (
                        isset($referer[3]) && $referer[3] == 'injuries'
                        &&
                        isset($referer[4]) && $referer[4] == 'info'
                        &&
                        isset($referer[5]) && ! $taskInstance->task->injuries->contains( $referer[5] )
                    )
                )
            )
                <form action="{{ url('tasks/attach-injury', [$taskInstance->task->id]) }}" method="POST" >
                    {{ Form::token() }}
                    {{ Form::hidden('injury_id', (
                        (Request::segment(1) == 'injuries' && Request::segment(2) == 'info') ? Request::segment(3)
                        : $referer[5]
                    )) }}
                    <button type="submit" class="btn btn-primary btn-xs pull-right" onclick="Pace.start()">
                        <i class="fa fa-chain fa-fw"></i> połącz obecną
                    </button>
                </form>
            @endif
            Powiązane szkody
            <span class="badge">{{ $taskInstance->task->injuries->count() }}</span>
        </h5>
        <ul class="list-group">
            @foreach($taskInstance->task->injuries as $injury)
                <a class="list-group-item @if(Request::segment(1) == 'injuries' && Request::segment(2) == 'info' && $injury->id == Request::segment(3)) active @endif " href="{{ url('/injuries/info', [$injury->id]) }}">
                    <i class="fa fa-arrow-circle-right fa-fw "></i>

                    {{ $injury->case_nr }} <strong> | </strong> {{ $injury->vehicle->registration }} <strong> | </strong> {{ $injury->vehicle->nr_contract }}
                </a>
            @endforeach
        </ul>
        <hr>

        <h5>
            <div class="btn btn-xs btn-primary pull-right modal-open" target="{{ url('tasks/add-comment', [$taskInstance->task->id]) }}" data-toggle="modal" data-target="#modal">
                <i class="fa fa-fw fa-plus"></i> dodaj
            </div>
            Komentarze
            <span class="badge">{{ $taskInstance->task->comments->count() }}</span>
        </h5>

        <ul class="list-group">
            @foreach( $taskInstance->task->comments as $comment)
                <div class="list-group-item">
                    <p class="list-group-item-text">
                        {{ $comment->content }}
                    </p>
                    <p class="text-right">
                        <small class="text-muted">
                            <i class="fa fa-clock-o fa-fw"></i> {{ $comment->created_at->format('Y-m-d H:i') }}
                            {{ $comment->user->name }}
                        </small>
                    </p>
                </div>
            @endforeach
        </ul>
        @if($taskInstance->task->replies->count() > 0)
            <hr>
            <h5>
                Odpowiedzi
                <span class="badge">{{ $taskInstance->task->replies->count() }}</span>
            </h5>
            <ul class="list-group">
                @foreach( $taskInstance->task->replies as $reply)
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
            </ul>
        @endif
        @if($taskInstance->task->forwards->count() > 0)
            <hr>
            <h5>
                Przekazania
                <span class="badge">{{ $taskInstance->task->forwards->count() }}</span>
            </h5>
            <ul class="list-group">
                @foreach( $taskInstance->task->forwards as $forward)
                    <div class="list-group-item">
                        <div class="pull-right">
                            <small class="text-muted">
                                <i class="fa fa-clock-o fa-fw"></i> {{ $forward->created_at->format('Y-m-d H:i') }}
                                {{ $forward->user->name }}
                            </small>
                        </div>
                        <p class="list-group-item-text">
                            <a class="btn btn-xs btn-info off-disable" href="{{ url('tasks/attachment', [$forward->task_file_id]) }}">
                                <i class="fa fa-floppy-o fa-fw"></i>
                            </a>
                            {{ $forward->receivers }}
                        </p>

                    </div>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@if(!isset($loadInSection) || $loadInSection)
    @section('headerJs')
        @parent
        <script>
            $(document).ready(function(){
                $('#modal-lg').on('click', '#set-files', function(){
                    var $btn = $(this).button('loading');
                    if($('#dialog-form').valid()) {
                        $.ajax({
                            type: "POST",
                            url: $('#dialog-form').prop('action'),
                            data: $('#dialog-form').serialize()+'&'+$.param($('input[name="taskFiles[]"').serializeArray()),
                            assync: false,
                            cache: false,
                            beforeSend: function() {
                                $('#task-loader').show();
                            },
                            success: function (data) {
                                $('#task-loader').hide();
                                if (data.code == '0') location.reload();
                                else if (data.code == '1'){
                                    window.location = data.url;
                                    location.reload();
                                }
                                else if (data.code == '2') {
                                    $('#modal-lg .modal-body').html(data.error);
                                    $('#modal-lg .modal-footer').html('<button type="button" onclick="location.reload();" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                                }else {
                                    $('#modal-lg .modal-body').html(data.error);
                                    if (isset(data.url) && data.url != '') {
                                        $btn.button('reset');
                                        $('#modal-lg').on('hidden.bs.modal', function (e) {
                                            window.location = data.url;
                                            location.reload();
                                        });
                                    }
                                }
                            },
                            dataType: 'json'
                        });
                    }else {
                        $btn.button('reset');
                    }
                    return false;
                });

                var frame = $("#task-module-iframe").contents().find('body');
                frame.html( $('#task-module-iframe-content').html() );

                $('#task-module-iframe-content').remove();

                var context = $('#task-module-iframe')[0].contentWindow.document;

                let iframe_height = context.body.scrollHeight;
                if(iframe_height > 500) { iframe_height = 500; }
                $('#task-module-iframe').css('height', iframe_height);

                $('[data-toggle="tooltip"]').tooltip();

            });
        </script>
    @endsection
@else
    <script>
        $(document).ready(function(){
            $('#modal-lg').on('click', '#set-files', function(){
                var $btn = $(this).button('loading');
                if($('#dialog-form').valid()) {
                    $.ajax({
                        type: "POST",
                        url: $('#dialog-form').prop('action'),
                        data: $('#dialog-form').serialize()+'&'+$.param($('input[name="taskFiles[]"').serializeArray()),
                        assync: false,
                        cache: false,
                        success: function (data) {
                            if (data.code == '0') location.reload();
                            else if (data.code == '1') {
                                self.location = data.url;
                                location.reload();
                            }
                            else if (data.code == '2') {
                                $('#modal-lg .modal-body').html(data.error);
                                $('#modal-lg .modal-footer').html('<button type="button" onclick="location.reload();" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                            }else {
                                $('#modal-lg .modal-body').html(data.error);
                                if (isset(data.url) && data.url != '') {
                                    $btn.button('reset');
                                    $('#modal-lg').on('hidden.bs.modal', function (e) {
                                        self.location = data.url;
                                        location.reload();
                                    });
                                }
                            }
                        },
                        dataType: 'json'
                    });
                }else {
                    $btn.button('reset');
                }
                return false;
            });

            var frame = $("#task-module-iframe").contents().find('body');
            frame.html( $('#task-module-iframe-content').html() );

            $('#task-module-iframe-content').remove();

            var context = $('#task-module-iframe')[0].contentWindow.document;

            let iframe_height = context.body.scrollHeight;
            if(iframe_height > 500) { iframe_height = 500; }
            $('#task-module-iframe').css('height', iframe_height);

            $('[data-toggle="tooltip"]').tooltip();

        });
    </script>
@endif