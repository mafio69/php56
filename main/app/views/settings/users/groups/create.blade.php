<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie grupy użytkowników</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/user/groups/store') }}" method="post"  id="dialog-form">

            <fieldset>
                <div class="form-group">
                    <label>Nazwa grupy:</label>
                    {{ Form::text('name', '', array('class' => 'form-control required')) }}
                </div>
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Zapisywanie..."   id="set">Wprowadź</button>
</div>

