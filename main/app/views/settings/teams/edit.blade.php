<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ url('settings/teams/update', $team->id) }}" method="post"  id="dialog-form">
            {{Form::token()}}
            <fieldset>
                <div class="form-group">
                  <label>Nazwa zespołu</label>
                    {{ Form::text('name', $team->name, array('class' => 'form-control required', 'placeholder' => 'Nazwa zespołu')) }}
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..."  id="set">Zapisz</button>
</div>
