 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Generowanie dokumentu <i>{{$documentType->name}}</i></h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.get-doc-generate', array($id, $key)) }}" method="post"  id="dialog-generate-doc-form">
      
        {{Form::token()}}
        
        <div class="form-group">
          
          @include('dos.other_injuries.dialog.generate_documents_partials.'.$documentType->short_name)

          <div class="row" id="reason_content" style="display:none;">
            <div class="col-sm-12 ">
             <label>Podaj przyczynę nienaliczania opłaty:</label>
             {{ Form::textarea('reason', '', array('class' => 'form-control', 'id'=>'reason',  'placeholder' => 'przyczyna nienaliczania opłat')) }}
            </div>
          </div>
                    
        </div>
        
    </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="generate-document">Wygeneruj</button>  
  </div>

    <script type="text/javascript">
    $(document).ready(function(){
      $('#date_submit').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
      $('select[name="issue_fee"]').on('change', function(){
        if($(this).val() == 0 ) $('#reason_content').show();
        else $('#reason_content').hide();
      });
      
    });

  </script>