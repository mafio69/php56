 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie dokumentu</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setDelDocConf', array($id)) }}" method="post"  id="dialog-del-doc-form">
  		
        {{Form::token()}}
        
        <div class="form-group">
          <div class="row">
            <div class="col-sm-12 marg-btm">
              Potwierdź usunięcię dokumentu z kartoteki podając przyczynę usnięcia.
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 ">
              {{ Form::textarea('content', '', array('class' => 'form-control required', 'id'=>'content',  'placeholder' => 'przyczyna usunięcia')) }} 
            </div>
          </div>
          
   
          
        </div>
        
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="del-doc">Usuń</button>
  </div>