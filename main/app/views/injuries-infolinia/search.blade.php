@extends('layouts.main')

@section('left-nav')
	
@stop

@section('header')
	
Wyszukiwanie szkody
	

@stop

@section('main')
{{ Form::token() }}
<div class="jumbotron pad-small">
  <div class="container">
     <div class="row">
    	<div class="cols-sm-6 col-md-4">
    		<div class="form-group">
                {{ Form::label('registration', 'Nr rejestracji:') }}
                {{ Form::text('registration', null, ['class' => 'form-control']) }}
            </div>
    	</div>
    	<div class="cols-sm-6 col-md-4">
    		<div class="form-group">
                {{ Form::label('nr_contract', 'Nr umowy leasingowej:') }}
                {{ Form::text('nr_contract', null, ['class' => 'form-control']) }}
            </div>
    	</div>
    	<div class="cols-sm-6 col-md-4">
    		<div class="form-group">
                {{ Form::label('injury_nr', 'Nr szkody ubezpieczyciela:') }}
                {{ Form::text('injury_nr', null, ['class' => 'form-control upper']) }}
            </div>
    	</div>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-body" id="search-containter">

  </div>
</div>
 
	
<!-- small modal -->
<div class="modal fade bs-example-modal-sm" id="modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      
    </div>
  </div>
</div>




@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {	       
	       $('#registration').focusout(function(){
	       		setTimeout(function(){
		            if( $('#registration').val().length == 7){
		              $.ajax({
		                url: "<?php echo  URL::route('injuries-search-registration');?>",
		                data: {
		                  registration: $('#registration').val(),
		                  _token: $('input[name="_token"]').val()
		                },
		                type: "POST",
		                async: false,
		                cache: false,
		                complete: function( data ) {
		                  if(data.responseText == '-1'){
		                    $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Nie znaleziono szkód dla podanego numeru rejestracji.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
		                    $('#modal-sm').modal('show');
		                    $('#search-containter').html('');
		                  }else{
		                    $('#search-containter').html(data.responseText);
		                  }
		                }
		              });
		            }else{
		               $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Niepoprawny format rejestracji (wymagane 7 znaków).</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
		               $('#modal-sm').modal('show');
		               $('#search-containter').html('');
		            }
	        	}, 100);
	        }).autocomplete({
	            source: function( request, response ) {
	              $.ajax({
	                  url: "<?php echo  URL::route('routes.post', ['injuries', 'getVehicleRegistrationList']);?>",
	                  data: {
	                    term: request.term,
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
	            minLength: 2,
	            open: function(event, ui) {
	              $(".ui-autocomplete").css("z-index", 1000);
	            },
	            select: function(event, ui) {
	              $(this).focusout();
	            }
	        }).bind("keypress", function(e) {
	          if( e.which == 13 ) $(this).focusout();
	        });

	        $('#nr_contract').focusout(function(){
	        	if( $('#nr_contract').val().length > 0){
		       		setTimeout(function(){
			              $.ajax({
			                url: "<?php echo  URL::route('injuries-search-contract');?>",
			                data: {
			                  contract: $('#nr_contract').val(),
			                  _token: $('input[name="_token"]').val()
			                },
			                type: "POST",
			                async: false,
			                cache: false,
			                complete: function( data ) {
			                  if(data.responseText == '-1'){
			                    $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Nie znaleziono szkód dla podanego numeru umowy leasingowej.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
			                    $('#modal-sm').modal('show');
			                    $('#search-containter').html('');
			                  }else{
			                    $('#search-containter').html(data.responseText);
			                  }
			                }
			              });
			            
		        	}, 100);
				}
	        }).autocomplete({
	            source: function( request, response ) {
	              $.ajax({
	                  url: "<?php echo  URL::route('vehicle-contract-getList');?>",
	                  data: {
	                    term: request.term,
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
	            minLength: 2,
	            open: function(event, ui) {
	              $(".ui-autocomplete").css("z-index", 1000);
	            },
	            select: function(event, ui) {
	              $(this).focusout();
	            }
	        }).bind("keypress", function(e) {
	          if( e.which == 13 ) $(this).focusout();
	        });

	        $('#injury_nr').focusout(function(){
	        	if( $('#injury_nr').val().length > 0){
		       		setTimeout(function(){
			              $.ajax({
			                url: "<?php echo  URL::route('injuries-search-injury_nr');?>",
			                data: {
			                  injury_nr: $('#injury_nr').val(),
			                  _token: $('input[name="_token"]').val()
			                },
			                type: "POST",
			                async: false,
			                cache: false,
			                complete: function( data ) {
			                  if(data.responseText == '-1'){
			                    $('#modal-sm .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel">Komunikat</h4></div><div class="modal-body">Nie znaleziono szkód dla podanego numeru rejestracji.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>')
			                    $('#modal-sm').modal('show');
			                    $('#search-containter').html('');
			                  }else{
			                    $('#search-containter').html(data.responseText);
			                  }
			                }
			              });
			            
		        	}, 100);
				}
	        }).autocomplete({
	            source: function( request, response ) {
	              $.ajax({
	                  url: "<?php echo  URL::route('vehicle-injury_nr-getList');?>",
	                  data: {
	                    term: request.term,
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
	            minLength: 2,
	            open: function(event, ui) {
	              $(".ui-autocomplete").css("z-index", 1000);
	            },
	            select: function(event, ui) {
	              $(this).focusout();
	            }
	        }).bind("keypress", function(e) {
	          if( e.which == 13 ) $(this).focusout();
	        });

	    });
	    
    </script>
  
@stop

