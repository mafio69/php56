<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie przypisania pracownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.processes.deleteUser', array($user->id)) }}" method="post"  id="edit-form"> 
            
            <div class="form-group">
                Potwierdź usunięcie przypisania pracownika.
            </div>
            {{Form::token()}}	
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save">Usuń</button>
</div>