<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('tasks/address-book/store') }}" method="post" id="dialog-form">
            {{ Form::token() }}
            <div class="form-group">
                <label>Mail:</label>
                {{ Form::text('email', null, ['class' => 'form-control required', 'required']) }}
            </div>
            <div class="form-group">
                <label>Nazwa:</label>
                {{ Form::text('name', null, ['class' => 'form-control']) }}
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Trwa dodawanie..."  id="set">Dodaj</button>
</div>
