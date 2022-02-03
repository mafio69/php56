<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuń opiekuna</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ url('companies/delete-guardian/'.$company->id) }}" method="post" role="form"  id="dialog-form">

        {{Form::token()}}

      Usunąć <b>{{$company->guardian->name()}}</b> jako opiekuna serwisu <b>{{$company->name}}</b>?

  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-danger" id="set">Usuń</button>
  </div>