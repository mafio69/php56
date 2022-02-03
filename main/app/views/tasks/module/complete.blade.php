<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zamknięcie sprawy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('tasks/complete') }}" method="post"  id="dialog-form">
        {{Form::token()}}
        {{ Form::hidden('task_instance_id', $taskInstance->id) }}
        Potwierdź zamknięcie sprawy.
        <hr>
        <label >Potwierdź typ sprawy:</label>
        <select name="task_type_id" class="form-control" required>
            <?php $previous_type = null; ?>
            @foreach($types as $type)
            @if($previous_type && $previous_type->task_group_id != $type->task_group_id)
            </optgroup>
            <optgroup label="{{ $type->group->name }}">
                @elseif(! $previous_type)
                    <optgroup label="{{ $type->group->name }}">
                        @endif
                        <option @if($taskInstance->task->task_type_id == $type->id) selected="selected" @endif value="{{ $type->id }}">
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
