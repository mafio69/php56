 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana statusu szkody</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ $url }}" method="post" role="form"  id="dialog-form">

        {{Form::token()}}

        Zmienić status sprzedaży na <i>{{ $status->name }}</i>?

  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zmień</button>
  </div>