<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana typu sprawy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('tasks/update-task-type') }}" method="post"  id="dialog-form">
        {{ Form::hidden('task_id', $task->id) }}
        {{Form::token()}}
        <label >Wybierz nowy status:</label>
        <select name="task_type_id" class="form-control" required>
            <?php $previous_type = null; ?>
            @foreach($types as $type)
                @if($previous_type && $previous_type->task_group_id != $type->task_group_id)
                    </optgroup>
                    <optgroup label="{{ $type->group->name }}">
                @elseif(! $previous_type)
                    <optgroup label="{{ $type->group->name }}">
                @endif
                        <option @if($task->task_type_id == $type->id) selected="selected" @endif value="{{ $type->id }}">
                            @if($type->subgroup)
                                [{{ $type->subgroup->name }}]
                            @endif
                            {{ $type->name }}
                        </option>
                <?php $previous_type = $type; ?>
            @endforeach
            </optgroup>
        </select>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>
