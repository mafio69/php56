<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Odblokowanie konta</h4>
</div>
<div class="modal-body" style="overflow:hidden;">

    <form action="{{ url('settings/users/unlock-account', [ $user->id ])}}" method="post"  id="dialog-form">
        {{ Form::token() }}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    Czy chesz odblokować konto użytkownika?
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
      <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa wykonywanie">Potwierdź</button>
</div>
