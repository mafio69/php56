<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana hasła użytkownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/password', array($user_db->id)) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Hasło:</label>
                    {{   Form::password('password', array('class' => 'form-control required', 'placeholder' => 'hasło', 'id'=>'password')) }}
                </div>
                <div class="form-group">
                    <label>Powtórz hasło:</label>
                    {{   Form::password('password_confirmation', array('class' => 'form-control required', 'equalto' => '#password', 'placeholder' => 'powtórz hasło', 'id'=>'password_repeat'))  }}
                </div>
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Zapisywanie..."  id="set">Zapisz zmiany</button>
</div>
