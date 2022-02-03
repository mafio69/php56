 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Ustalanie terminu</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.chat.postDeadline', array($id)) }}" method="post"  id="dialog-injury-form">
  		
      {{Form::token()}}
      <div class="form-group">
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Podaj termin:</label>
            {{ Form::text('deadline', '', array('class' => 'form-control required', 'id'=>'deadline')) }} 
          </div>
        </div>
      </div> 

  	</form>

  </div>
  <div class="modal-footer" style="margin-top:0px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Dodaj</button>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){
      $('#deadline').datepicker();
        
    });

  </script>