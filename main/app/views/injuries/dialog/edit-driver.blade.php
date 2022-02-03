 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych kierowcy</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('injuries-setEditInjuryDriver', array($id)) }}" method="post"  id="dialog-injury-form"> 
  		
        {{Form::token()}}
        {{Form::hidden('driver_id', $injury->driver_id)}}
        {{Form::hidden('client_id', $injury->client_id)}}
       <div class="form-group">
        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Imię:</label>
            {{ Form::text('name', isset($driver->name)?$driver->name:'', array('class' => 'form-control upper required', 'id'=>'name',  'placeholder' => 'imię')) }}
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Nazwisko:</label>
            {{ Form::text('surname', isset($driver->surname)?$driver->surname:'', array('class' => 'form-control upper required', 'id'=>'surname',  'placeholder' => 'nazwisko')) }}
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Telefon:</label>
            {{ Form::text('phone', isset($driver->phone)?$driver->phone:'', array('class' => 'form-control upper ', 'id'=>'phone',  'placeholder' => 'telefon')) }}
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Email:</label>
            {{ Form::text('email', isset($driver->email)?$driver->email:'', array('class' => 'form-control email ', 'id'=>'email',  'placeholder' => 'email')) }}
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12 marg-btm">
            <label >Miasto:</label>
            {{ Form::text('city', isset($driver->city)?$driver->city:'', array('class' => 'form-control upper ', 'id'=>'city',  'placeholder' => 'miasto')) }}
          </div>
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
        $('input[name=surname], input[name=name], input[name=phone], input[name=email], input[name=city]').keypress(function() {
          $('input[name=driver_id]').val('');  
        }).keyup(function(){
          $('input[name=driver_id]').val('');
        });

        $( "input[name=surname], input[name=name]" ).autocomplete({
            source: function( request, response ) {
            $.ajax({
                url: "<?php echo  URL::route('drivers-getList');?>",
                data: {
                  id_client: $('input[name="client_id"]').val(),
                  term: request.term,
                  ele: $(this).attr('name'),
                  _token: $('input[name="_token"]').val()
                },
                dataType: "json",
                type: "POST",
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return item;
                    }));
                }
            });
        },
            minLength: 1,
            select: function(event, ui) {
                $('input[name=surname]').val(ui.item.surname);
                $('input[name=name]').val(ui.item.name);
                $('input[name=phone]').val(ui.item.phone);
                $('input[name=email]').val(ui.item.email);
                $('input[name=city]').val(ui.item.city);  
                $('input[name="driver_id"]').val(ui.item.id);      
            },
            open: function(event, ui) {
                $(".ui-autocomplete").css("z-index", 1000);
            }
        });
    });

  </script>