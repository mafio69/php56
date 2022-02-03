<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ url('settings/teams/delete', $team->id) }}" method="post"  id="dialog-form">
            {{Form::token()}}
            <fieldset>
                <div class="form-group">
                  <label>Potwierdź usunięcie zespołu <i>{{ $team->name }}</i> </label>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..."  id="set">Usuń</button>
</div>
