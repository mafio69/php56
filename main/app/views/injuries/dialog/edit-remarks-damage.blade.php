 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja uwag do uszkodzeń</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-postEditInjuryRemarks-damage', array($id)) }}" method="post"  id="dialog-injury-form"> 
  		
        {{Form::token()}}
       <div class="form-group">
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Uwagi do uszkodzeń:</label>
            @if($injury->remarks_damage != 0)
            {{ Form::textarea('info', $info->content, array('class' => 'form-control  ', 'placeholder' => 'Uwagi do uszkodzeń'))  }}
            @else
            {{ Form::textarea('info', '', array('class' => 'form-control  ', 'placeholder' => 'Uwagi do uszkodzeń'))  }}
            @endif
          </div>
        </div>
        
 
        
      </div> 
              
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
  </div>
