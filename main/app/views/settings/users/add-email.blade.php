<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawawanie adresu email</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/add-email', [$user_db->id]) }}" method="post" id="dialog-form">
           <div class="form-group">
               <label for="">Adres email:</label>
               {{ Form::email('email', null, ['class' => 'form-control required', 'required']) }}
           </div>
            {{ Form::token() }}
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Trwa zapisywanie..."  id="set">Zapisz</button>
</div>
