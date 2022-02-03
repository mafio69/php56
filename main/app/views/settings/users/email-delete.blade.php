<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie adresu email</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/delete-email', [$email->id]) }}" method="post" id="dialog-form">
            <p>Potwierdź usunięcie adresu email {{ $email->email }}</p>
            {{ Form::token() }}
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Trwa usuwanie..."  id="set">Usuń</button>
</div>