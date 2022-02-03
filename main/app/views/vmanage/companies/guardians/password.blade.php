<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana hasła opiekuna floty</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::action('VmanageCompanyGuardiansController@postPassword', [$guardian_id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="form-group col-sm-12 marg-btm">
                    <label>Nowe hasło:</label>
                    {{ Form::password('password', array( 'class' => 'form-control required'))  }}
                </div>
                <div class="form-group col-sm-12 marg-btm">
                    <label>Powtórz nowe hasło:</label>
                    {{ Form::password('password_confirm', array('class' => 'form-control required'))  }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..." id="set">Zapisz</button>
</div>

