<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usunięcie szkody</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.set', array('setDelete', $id)) }}" method="post"  id="dialog-injury-form">
  	  Potwierdź usunięcie.
        {{Form::token()}}
    </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
  </div>