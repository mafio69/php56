  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usunięcie serwisu</h4>
  </div>
  <div class="modal-body" style="overflow:hidden; padding-bottom:0px; ">
  	<form action="{{ URL::route('injuries-returnBranch', array($injury->id)) }}" method="post" id="dialog-form">
        {{Form::token()}}
  		<div class="row">

	  		<div class="col-sm-12 marg-btm">
	    	    <p>Czy chcesz usunąć serwis przypisany na podstawie danych z faktury?</p>
            <p>Ta operacja usunie serwis przypisany na podstawie danych z faktury, oraz przywróci oryginalnie przypisany serwis.</p>
	    	</div>


  		</div>
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
  </div>
