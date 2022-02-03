 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Anulowanie szkody</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-setCancel', array($id)) }}" method="post"  id="dialog-injury-form"> 
  		
        {{Form::token()}}
        <label >Przyczyna anulowania:</label>
        {{ Form::textarea('content', '', array('class' => 'form-control required', 'id'=>'content',  'placeholder' => 'wprowadź przyczynę anulowania szkody')) }}
    </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
  </div>