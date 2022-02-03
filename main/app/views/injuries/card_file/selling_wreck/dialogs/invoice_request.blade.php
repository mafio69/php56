 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Wysyłanie prośby o fakturę</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries.info.postInvoiceRequest', array($wreck->id)) }}" method="post"  id="dialog-injury-form">
       {{Form::token()}}
       <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                  <label >Alert czasowy:</label>
                  {{ Form::text('alert', Date('Y-m-d', strtotime("+3 days")) , array('class' => 'form-control required', 'id'=>'date_submit',  'placeholder' => 'Alert czasowy', 'required')) }}
                </div>
            </div>
       </div>
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Wyślij</button>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){
        $('#date_submit').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
    });

  </script>