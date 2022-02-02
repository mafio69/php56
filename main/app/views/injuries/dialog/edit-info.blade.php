 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja inf. wewnętrznej</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-postEditInjuryInfo', array($id)) }}" method="post"  id="dialog-injury-form"> 

        {{Form::token()}}
       <div class="form-group">
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Informacja wewnętrzna:</label>
            @if($injury->info != 0)
            {{ Form::textarea('info', $info->content, array('class' => 'form-control  ', 'placeholder' => 'Informacja wewnętrzna'))  }}
            @else
            {{ Form::textarea('info', '', array('class' => 'form-control  ', 'placeholder' => 'Informacja wewnętrzna'))  }}
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

