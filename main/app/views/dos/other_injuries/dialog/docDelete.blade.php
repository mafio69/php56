 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie dokumentu</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setDelDoc', array($id)) }}" method="post"  id="dialog-del-doc-form">
  		
        {{Form::token()}}
        Potwierdź usunięcię dokumentu z kartoteki.
        
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="del-doc">Usuń</button>
  </div>