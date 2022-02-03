@if(! isset($content))
<div class="tab-pane fade " id="tasks">
@endif
    @foreach($injury->tasks as $task)
        <p class="clearfix ">
            <btn class="btn btn-primary btn-xs task-show-details" data-task="{{ $task->currentInstance->id }}" >
                <i class="fa fa-arrow-circle-left fa-fw"></i> przejdź
            </btn>

            <strong class="marg-right">
                nr sprawy:
                {{ $task->case_nb }}
            </strong>

            <em>
                <label>status:</label> {{ $task->currentInstance->step->name }}
                @if( $task->currentInstance->latestHistory->description)
                    <span class="btn btn-info btn-xs">
                        <i class="fa fa-info-circle" data-toggle="tooltip" title="{{ $task->currentInstance->latestHistory->description }}"></i>
                    </span>
                @endif
            </em>

            <em>
                <label>typ:</label> {{ $task->group->name }} @if($task->type)- {{ $task->type->name }} @endif
            </em>

            <em>
                <label>źródło:</label> {{ $task->source ? $task->source->name : ''}}
            </em>

            <em>
                <label>data wpływu:</label> {{ $task->created_at->format('Y-m-d H:i') }}
            </em>

            <em class="pull-right">
                <span class="btn btn-danger btn-xs  modal-open" data-toggle="modal" data-target="#modal" target="{{ url('tasks/detach-injury', [$task->id, $injury->id]) }}">
                    <i class="fa fa-trash-o fa-fw"></i>
                    odepnij od szkody
                </span>
            </em>


            <em class="pull-right">
                <span class="label label-info marg-right">
                    <i class="fa fa-comments fa-fw"></i>
                    <span class="badge">
                        {{ $task->comments->count() }}
                    </span>
                </span>
            </em>

            <em class="pull-right">
                <span class="label label-info marg-right">
                    <i class="fa fa-files-o fa-fw"></i>
                    <span class="badge">
                        {{ $task->files->count() }}
                    </span>
                </span>
            </em>



        <hr class="short"/>

        </p>
    @endforeach
@if(! isset($content))
    </div>
@endif
