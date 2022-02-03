 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych kontaktowych klienta</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-setEditInjuryClientContact', array($id)) }}" method="post"  id="dialog-injury-form"> 
  		
        {{Form::token()}}
       <div class="form-group">

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Kod pocztowy:</label>
            {{ Form::text('correspond_post', $injury->client->correspond_post, array('class' => 'form-control  ', 'id'=>'correspond_post',  'placeholder' => 'kod pocztowy')) }} 
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Miasto:</label>
            {{ Form::text('correspond_city', $injury->client->correspond_city, array('class' => 'form-control  ', 'id'=>'correspond_city',  'placeholder' => 'Miasto')) }} 
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Ulica:</label>
            {{ Form::text('correspond_street', $injury->client->correspond_street, array('class' => 'form-control  ', 'id'=>'correspond_street',  'placeholder' => 'ulica')) }} 
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Tefelon:</label>
            {{ Form::text('phone', $injury->client->phone, array('class' => 'form-control  ', 'id'=>'phone',  'placeholder' => 'telefon')) }} 
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Email:</label>
            {{ Form::text('email', $injury->client->email, array('class' => 'form-control email ', 'id'=>'email',  'placeholder' => 'email')) }} 
          </div>
        </div>

      </div> 
              
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
  </div>
