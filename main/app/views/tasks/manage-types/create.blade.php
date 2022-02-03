<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Tworzenie typu</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::to('tasks/manage-types/store', [$taskGroup->id, $taskSubGroup ? $taskSubGroup->id : '']) }}" method="post" id="dialog-form">
        {{ Form::text('name', null, ['class' => 'form-control required', 'required']) }}
        {{ Form::token() }}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa wykonywanie">Zapisz</button>
</div>