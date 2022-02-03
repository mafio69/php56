<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zamknięcie sprawy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('tasks/complete-without-action') }}" method="post"  id="dialog-form">
        {{Form::token()}}
        {{ Form::hidden('task_instance_id', $taskInstance->id) }}
        <p>
            <strong>
                Potwierdź zamknięcie sprawy.
            </strong>
        </p>

        <div class="form-group">
            <label>Opis</label>
            {{ Form::text('description', null, ['class' => 'form-control', 'required']) }}
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>
