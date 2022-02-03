 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie notki</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.create-history', array($id)) }}" method="post"  id="dialog-injury-form">
  		
        {{Form::token()}}
       <div class="form-group">
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Notka:</label>
            {{ Form::text('content', '', array('class' => 'form-control required', 'id'=>'content',  'placeholder' => 'notka', 'required')) }}

          </div>
        </div>
        
 
        
      </div> 
              
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Dodaj</button>
  </div>

