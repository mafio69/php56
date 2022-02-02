<div class="list-group">
    @foreach(\Idea\Tasker\Tasker::completeTasks() as $taskInstance)
        <a data-task="{{ $taskInstance->id }}" class="list-group-item pointer task-show-details @if(Session::get('task.id') == $taskInstance->id) list-group-item-info @endif">
            <span class="pull-right small">{{ $taskInstance->date_complete->format('Y-m-d H:i') }}</span>

            <span class="label label-{{ $taskInstance->task->sourceType->style }}">{{ $taskInstance->task->sourceType->name }}</span>
            <em>{{ $taskInstance->task->case_nb }} -</em>
            {{ $taskInstance->task->group ? $taskInstance->task->group->name : '' }}
            @if($taskInstance->task->type)
             - {{ $taskInstance->task->type->name }}
            @endif
        </a>
    @endforeach
</div>