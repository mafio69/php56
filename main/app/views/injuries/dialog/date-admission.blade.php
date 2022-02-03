 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Ustalanie terminu przyjęcia</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-setDateAdmission', array($id)) }}" method="post"  id="dialog-injury-form"> 
  		
        {{Form::token()}}
       <div class="form-group">

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Termin przyjęcia:</label>
            {{ Form::text('date_admission', $injury->date_admission, array('class' => 'form-control required ', 'id'=>'date_admission',  'placeholder' => 'termin przyjęcia')) }} 
          </div>
        </div>

              
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){

        $('#date_admission').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,dateFormat: "yy-mm-dd" });
    });

  </script>