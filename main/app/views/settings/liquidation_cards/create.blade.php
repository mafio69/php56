@extends('layouts.main')

@section('header')

Dodawanie karty likwidacji szkód

@stop

@section('main')

@include('modules.flash_notification')

<form action="{{ URL::route('settings.liquidation_cards', array('store') ) }}" method="post" role="form">
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="{{{ URL::route('settings.liquidation_cards', array('index') ) }}}" class="btn btn-default">Anuluj</a>
		</div>
	</div>
	@if($errors->any())
	<div class="row">
	    <div class="alert alert-danger">
	    	<a href="#" class="close" data-dismiss="alert">&times;</a>
	    	{{ implode('', $errors->all('<li class="error">:message</li>'))}}
	    </div>
	</div>
	@endif

	<div class="row">
	    <div class="form-group">
            <h4 class="inline-header"><span>Dane karty likwidacji pojazdu:</span></h4>
            <div class="row injury_bg ">
                <div class="col-md-4 col-lg-3 marg-btm tips" title= 'Numer karty'>
                {{ Form::text('number', $number, array('class' => 'form-control number  required ', 'id' => 'number', 'placeholder' => 'Numer karty'))  }}
                </div>
                <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Data wydania karty'>
                {{ Form::text('release_date', date('Y-m-d'), array('class' => 'form-control  required ', 'id' => 'release_date', 'placeholder' => 'Data wydania'))  }}
                </div>
                <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Data ważności karty'>
                {{ Form::text('expiration_date', '', array('class' => 'form-control  required ', 'id' => 'expiration_date', 'placeholder' => 'Data ważności'))  }}
                </div>
            </div>
        </div>
		<div class="form-group">
			<h4 class="inline-header"><span>Zgłoszenie karty likwidacji pojazdu:</span></h4>
			<div class="row injury_bg ">
			    <div class="col-md-4 col-lg-3 marg-btm tips" title= 'Rejestracja'>
                {{ Form::text('registration', '', array('class' => 'form-control  required upper', 'id' => 'registration', 'placeholder' => 'Rejestracja', 'tabindex' => '1'))  }}
                </div>
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy leasingowej'>
			    {{ Form::text('nr_contract', '', array('class' => 'form-control  required upper', 'id' => 'nr_contract', 'placeholder' => 'Nr umowy leasingowej'))  }}
			    </div>
			</div>
		</div>
		<div class="form-group">
            <h4 class="inline-header"><span>Dane identyfikacyjne pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group ">
                        {{ Form::text('vin', '', array('id'=>'vin', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'VIN', 'title' => 'VIN'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('brand', '', array('id'=>'brand', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'marka', 'title' => 'marka'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('model', '', array('id'=>'model', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'model', 'title' => 'model'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('year_production', '', array('id'=>'year_production', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'rok produkcji', 'title' => 'rok produkcji'))  }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Dane właściciela i klienta:</span></h4>
            <div class="row">
                <div class="col-md-6 col-lg-6 editable marg-btm">
                    <div class="form-group">
                        <label class="input-group  marg-btm" id="owner_idea">
                            {{ Form::text('owner', '', array('id'=>'owner', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'Właściel', 'title' => 'Właściel'))  }}
                            <span class="input-group-btn">
                                <button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-owner"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 editable marg-btm">
                    <div class="form-group">
                        <label class="input-group  marg-btm" id="client_idea">
                            {{ Form::text('client', '', array('id'=>'client', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'Klient', 'title' => 'Klient'))  }}
                            <span class="input-group-btn">
                                <button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-client"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Status umowy:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('end_leasing', '', array('id'=>'end_leasing', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'data końca leasingu', 'title' => 'data końca leasingu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 editable marg-btm">
                    <div class="form-group">
                        {{ Form::text('contract_status', '', array('id'=>'contract_status', 'class' => 'form-control upper tips', 'readonly' => 'readonly', 'placeholder' => 'status umowy', 'title' => 'status umowy'))  }}
                    </div>
                </div>
            </div>
        </div>

        {{Form::token()}}
        {{Form::hidden('vehicle_id', '', array('id' => 'vehicle_id'))}}
        {{Form::hidden('client_id', '', array('id' => 'client_id'))}}
        {{Form::hidden('owner_id', '', array('id' => 'owner_id'))}}
        {{Form::hidden('user_id', Auth::user()->id)}}

	</div>

<div class="row marg-btm">
	<h4 class="inline-header "></h4>
	<div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">

		{{ Form::submit('Zapisz',  array('id'=>'addInjurySubmit', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }}
	</div>
</div>

</form>

<div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>



@stop

@section('headerJs')
	@parent
	<script type="text/javascript" >

		var delay = ( function() {
			var timer = 0;
			return function(callback, ms) {
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();


		jQuery.validator.setDefaults({
		    errorPlacement: function(error, element) {
		    	if(element.parent()[0]['nodeName'] == 'LABEL')
		    		error.insertAfter(element.parent());
		    	else
		    		error.insertAfter(element);
		    }
		});


      $(document).ready(function(){
			$("form").submit(function(e) {
			    var self = this;
			    e.preventDefault();
                btn = $('#addInjurySubmit');
                btn.attr('disabled', 'disabled');

			    if($("form").valid()){
			     	if($('#object_id').val() != '')
			     		self.submit();
			     	else{
			     		$('#modal .modal-title').html('Komunikat');
						$('#modal .modal-body').html('Proszę wprowadzić dane przedmiotu istniejącego w bazie.');
						$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
						$('#modal').modal('show');
						btn.removeAttr('disabled');
			     	}
			    }else{
                    btn.removeAttr('disabled');
			    }

			     return false; //is superfluous, but I put it here as a fallback
			});


	        <?php //wyszukiawnie samochodu po rejestracji?>
    	    	var focus = 0;
    	    	$('#registration').focusout(function(){
    	    		if($(this).val().length >= 7 && $(this).val().length <= 8 && focus == 0){
    	    			focus = 1;
    		    		$.ajax({
    				        url: "<?php echo URL::route('settings.liquidation_cards', array('getIsdlList'));?>",
    				        data: {
    				        	registration: $('#registration').val(),
    				        	_token: $('input[name="_token"]').val()
    				        },
    				        dataType: "json",
    				        type: "POST",
    				        beforeSend: function() {
    				        	$('#modal .modal-title').html('Komunikat');
    				        	$('#modal .modal-body').html('Trwa wczytywanie danych z ISDL');
    				        	$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
    				        	$('#modal').modal('show');
    				        },
    				        statusCode: {
    					        500: function( data ) {
    					            $('#modal .modal-title').html('Informacja z systemu ISDL');
    					        	$('#modal .modal-body').html('Błąd połączenia z serwerem ISDL. Skontaktuj się z administratorem.');
    					        	$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
    					        }
    					    },
    				        success: function( data ) {
                                data = tryParseJSON(data);
                                var count_all = Object.keys(data).length;
                                var count_not_found = 0;
                                var count_found = 0;
                                var found_vehicle = [];

                                $.each(data,
                                    function(i, item) {
                                        if(item.status != 0)
                                            count_not_found++;
                                        if(item.status == 0)
                                        {
                                            count_found++;
                                            found_vehicle.push(item);
                                        }
                                    }
                                );

                                var vehicle;
                                if(count_found == 1) {
                                    vehicle = found_vehicle[0];
                                }else if(count_found > 1){
                                    $('#modal .modal-title').html('Informacja z systemu');
                                    $('#modal .modal-body').html('Istnieją '+count_found+' samochody w systemie dla wskazanych parametrów');
                                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }else if(count_not_found == count_all){
                                    $('#modal .modal-title').html('Informacja z systemu');
                                    $('#modal .modal-body').html(data[1].des);
                                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }else{
                                    $('#modal').modal('hide');
                                    $('#modal .modal-body').html('');
                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }


                                if(isset(vehicle) && vehicle.id != $('#vehicle_id').val() ){
                                    $('#modal').modal('hide');
                                    $('#modal .modal-body').html('');
                                    //samochód istnieje w bazie systemu
                                    $('#vehicle_id').val(vehicle.id);
                                    $('#nr_contract').val(vehicle.nr_contract);
                                    $('#client_id').val(vehicle.client_id);
                                    $('#owner_id').val(vehicle.owner_id);

                                    $('.show-owner').removeAttr('disabled');
                                    $('.show-client').removeAttr('disabled');

                                    $.each(vehicle, function(i, item) {
                                        if( i.substr(i.length - 5) == '_show' ){
                                            if(vehicle[i] != '0000-00-00' && vehicle[i] != 0 && vehicle[i] != '' && isset(vehicle[i])){
                                                $name = i.substr(0, (i.length - 5));
                                                $('input[name='+$name+']').val(vehicle[i]);
                                            }
                                        }
                                    });

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }
    						}
    				    });
    				}else{
    					if( $(this).val() != '' && ( $(this).val().length < 7 || $(this).val().length > 8) ){
    						$('#modal .modal-title').html('Komunikat');
    						$('#modal .modal-body').html('Wprowadzono błędny numer rejestracyjny');
    						$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
    						$('#modal').modal('show');

                            setTimeout(function(){
                                focus = 0;
                            }, 1000);
    					}
    				}
    	    	}).autocomplete({
    		      	source: function( request, response ) {
    				    $.ajax({
    				        url: "<?php echo  URL::route('vehicle-registration-getList');?>",
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
    		        	setTimeout(function(){
    			  			$('#registration').focusout();
    			  		},500);
    		        }
    		    }).bind("keypress", function(e) {
    		    	if(e.which == 13){
    				  	setTimeout(function(){
    				  		$('#registration').focusout();
    				  	},500);
    				 }
    			});


    			$( "#nr_contract" ).focusout(function(){
    				if($(this).val().length > 0 && $(this).val().length < 11 && focus == 0){
    					focus = 1;
    		    		$.ajax({
    				        url: "<?php echo  URL::route('settings.liquidation_cards', array('getIsdlList'));?>",
    				        data: {
    				        	nr_contract: $('#nr_contract').val(),
    				        	_token: $('input[name="_token"]').val()
    				        },
    				        dataType: "json",
    				        type: "POST",
    				        beforeSend: function() {
    				        	$('#modal .modal-title').html('Komunikat');
    				        	$('#modal .modal-body').html('Trwa wczytywanie danych z ISDL');
    				        	$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
    				        	$('#modal').modal('show');
    				        },
    				        statusCode: {
    					        500: function( data ) {
    					            $('#modal .modal-title').html('Informacja z systemu ISDL');
    					        	$('#modal .modal-body').html('Błąd połączenia z serwerem ISDL. Skontaktuj się z administratorem.');
    					        	$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
    					        }
    					    },
    				        success: function( data ) {

                                data = tryParseJSON(data);
                                var count_all = Object.keys(data).length;
                                var count_not_found = 0;
                                var count_found = 0;
                                var found_vehicle = [];

                                $.each(data,
                                    function(i, item) {
                                        if(item.status != 0)
                                            count_not_found++;
                                        if(item.status == 0)
                                        {
                                            count_found++;
                                            found_vehicle.push(item);
                                        }
                                    }
                                );

                                var vehicle;
                                if(count_found == 1) {
                                    vehicle = found_vehicle[0];
                                }else if(count_found > 1){
                                    $('#modal .modal-title').html('Informacja z systemu ISDL');
                                    $('#modal .modal-body').html('Istnieją '+count_found+' samochody w systemie dla wskazanych parametrów');
                                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }else if(count_not_found == count_all){
                                    $('#modal .modal-title').html('Informacja z systemu ISDL');
                                    $('#modal .modal-body').html(data[1].des);
                                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }else{
                                    $('#modal').modal('hide');
                                    $('#modal .modal-body').html('');
                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }


                                if(isset(vehicle) && vehicle.id != $('#vehicle_id').val() ){
                                    $('#modal').modal('hide');
                                    $('#modal .modal-body').html('');
                                    //samochód istnieje w bazie systemu
                                    $('#vehicle_id').val(vehicle.id);
                                    $('#client_id').val(vehicle.client_id);
                                    $('#owner_id').val(vehicle.owner_id);

                                    $('.show-owner').removeAttr('disabled');
                                    $('.show-client').removeAttr('disabled');

                                    $.each(vehicle, function(i, item) {
                                        if( i.substr(i.length - 5) == '_show' ){
                                            if(vehicle[i] != '0000-00-00' && vehicle[i] != 0 && vehicle[i] != '' && isset(vehicle[i])){
                                                $name = i.substr(0, (i.length - 5));
                                                $('input[name='+$name+']').val(vehicle[i]);
                                            }
                                        }
                                    });

                                    setTimeout(function(){
                                        focus = 0;
                                    }, 1000);
                                }
    						}
    				    });
    				}else{
                        if($(this).val() != '' && $(this).val().length > 10){
                            $('#modal .modal-title').html('Komunikat');
                            $('#modal .modal-body').html('Wprowadzono błędny numer umowy');
                            $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                            $('#modal').modal('show');

                            setTimeout(function(){
                                focus = 0;
                            }, 1000);
                        }
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
    		        	setTimeout(function(){
    			  			$('#nr_contract').focusout();
    			  		},500);
    		        }
    		    }).bind("keypress", function(e) {
    			  if( e.which == 13 ){
    			  	setTimeout(function(){
    		  			$('#nr_contract').focusout();
    		  		},500);
    			  }
    			});

            <?php //podgląd danych właściciela?>
                $('.show-owner').click(function(){
                    hrf='/injuries/owner/show/'+$('#owner_id').val()+'/';

                    $.get( hrf, function( data ) {
                        $('#modal .modal-title').html('Dane właściciela');
                        $('#modal .modal-body').html(data);
                        $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                    });
                });
            <?php //podgląd danych klienta?>
                $('.show-client').click(function(){
                    hrf='/injuries/client/show/'+$('#client_id').val()+'/';

                    $.get( hrf, function( data ) {
                        $('#modal .modal-title').html('Dane klienta');
                        $('#modal .modal-body').html(data);
                        $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                    });
                });

            $( "#release_date" ).datepicker({ showOtherMonths: true, selectOtherMonths: true,
              dateFormat: "yy-mm-dd",
              changeMonth: true,
              numberOfMonths: 3,
              onClose: function( selectedDate ) {
                $( "#expiration_date" ).datepicker( "option", "minDate", selectedDate );
              }
            });
            $( "#expiration_date" ).datepicker({ showOtherMonths: true, selectOtherMonths: true,
              defaultDate: "+1w",
              dateFormat: "yy-mm-dd",
              changeMonth: true,
              numberOfMonths: 3,
              onClose: function( selectedDate ) {
                $( "#release_date" ).datepicker( "option", "maxDate", selectedDate );
              }
            });

      });



    </script>

@stop

