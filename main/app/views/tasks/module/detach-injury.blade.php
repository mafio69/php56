<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Odpinanie</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('tasks/detach-injury', [$task_id, $injury_id]) }}" method="post" id="dialog-form">
            <p>Potwierdź odpięcię szkody od zadania.</p>
            <div class="form-group">
                <label for="">Powód:</label>
                {{ Form::text('content', null, ['class' => 'form-control required', 'required']) }}
            </div>
            {{ Form::token() }}
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Trwa wykonywanie..."  id="set">Potwierdź</button>
</div>
