<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('tasks/black-list/update', [$item->id]) }}" method="post" id="dialog-form">
            {{ Form::token() }}
            <div class="form-group">
                <label>Mail nadawcy:</label>
                {{ Form::text('email', $item->email, ['class' => 'form-control required', 'required']) }}
            </div>
            <div class="form-group">
                <label>Temat wiadomości</label>
                {{ Form::text('topic', $item->topic, ['class' => 'form-control']) }}
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Trwa zapisywanie..."  id="set">Zapisz</button>
</div>
