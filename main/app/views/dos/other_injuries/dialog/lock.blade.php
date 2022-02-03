 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Blokowywanie szkody</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.set', array('setLock', $id)) }}" method="post"  id="dialog-form">
  		
        {{Form::token()}}
        Potwierdź zablokowanie zarządzaniem szkody.
        
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary let_disable" id="set">Potwierdź</button>
  </div>