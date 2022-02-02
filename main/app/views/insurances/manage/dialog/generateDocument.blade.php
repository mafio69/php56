
 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Generowanie dokumentu <i>{{$documentType->name}}</i></h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/documents/generate-doc', [$id, $documentType->id]) }}" method="post"  id="dialog-generate-doc-form" class="allow-confirm">

        {{Form::token()}}

        <div class="form-group">

          @include('insurances.manage.dialog.generate_documents_partials.'.$documentType->short_name)

        </div>

    </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="generate-document">Wygeneruj</button>
  </div>
<script type="text/javascript">
  $(document).ready(function(){
    $('.datepicker').datepicker({ showOtherMonths: true, selectOtherMonths: true,  dateFormat: "yy-mm-dd" });
    $('textarea').keydown(function(){
      var key = window.event.keyCode;

      if (key === 13) {
          $(this).val($(this).val() + "\n");
          return false;
      }
      else {
          return true;
      }
    })
    $('[name="refer"], [name="date_form"], [name="date_to"]').change(function(){
      $.get("{{ URL::to('insurances/documents/annex-calculate',[$id]) }}/"+$('[name="refer"] option:selected').val()+'?start_date='+$('[name="date_form"]').val()+'&end_date='+$('[name="date_to"]').val()).success(function(response){
       var data = JSON.parse(response);
       if(data.value!=null&&data.value!=0){
          $('[name="annex_value"]').val(data.value);
       }
      })
      if($('[name="refer"] option:selected').val() == '12'){
          $('#end_date').show().val('');
      }
      else{
          $('#end_date').hide().val('{{ date('Y-m-d') }}');
      }
    })
  });
</script>
