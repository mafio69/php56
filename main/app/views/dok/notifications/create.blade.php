@extends('layouts.main')

@section('styles')
@parent


     


@stop

@section('header')
	
Dodawanie nowego zgłoszenia

<div class="pull-right">
    <a href="{{{ URL::previous() }}}" class="btn btn-default">Anuluj</a>
</div>
	
@stop

@section('main')
<form action="{{ URL::route('dok.notifications.store') }}" method="post" role="form">
	
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
			<h4 class="inline-header"><span>Wprowadzanie zgłoszenia:</span></h4>
			<div class="row injury_bg ">
				<div class="col-md-4 col-lg-3 marg-btm tips" title= 'Rejestracja'>
			    {{ Form::text('registration', '', array('class' => 'form-control  required upper', 'id' => 'registration', 'placeholder' => 'Rejestracja', 'tabindex' => '1'))  }} 
				</div>
				
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy leasingowej'>
			    {{ Form::text('nr_contract', '', array('class' => 'form-control  required upper', 'id' => 'nr_contract', 'placeholder' => 'Nr umowy leasingowej'))  }} 
			    </div>

			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr NIP klienta'>
			    {{ Form::text('nip_number', '', array('class' => 'form-control  upper', 'id' => 'nip_number', 'placeholder' => 'Nr NIP klienta'))  }} 
			    </div>
			    
			</div>
		</div>
		<div class="form-group ">
			<div class="row">
				<div class="col-md-10 col-md-offset-1">
					<div class="btn-group btn-group-justified block " data-toggle="buttons">
						@foreach($dok_wayof as $wayof)
						  <label class="btn btn-primary ">
						    <input type="radio" name="wayof_id" value="{{ $wayof->id }}" > {{ $wayof->name }}
						  </label>
					  	@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<h4 class="inline-header"><span>Dane identyfikacyjne pojazdu:</span></h4>
			<div class="row">
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group ">
						<label class="input-group  " id="vin_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="vin_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="VIN z serwera IL" disabled="true" name="vin" placeholder="VIN" tabindex="2"/>					    
								<span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="vin" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
					      	
				      	</label>
				      	<label class="input-group tips" title="VIN z systemu" id="vin_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="vin_grp" value="1">
							    </span>	
							    <p class="form-control"></p>	
				      	</label>
			      	</div>
			    </div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						<label class="input-group  " id="brand_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="brand_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="Marka z serwera IL" disabled="true" name="brand" placeholder="marka"/>					    
								<span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="brand" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
					      	
				      	</label>
				      	<label class="input-group tips" title="Marka z systemu" id="brand_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="brand_grp" value="1">
							    </span>	
							    <p class="form-control"></p>	
				      	</label>
			      	</div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						<label class="input-group  " id="model_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="model_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="Model z serwera IL" disabled="true" name="model" placeholder="model"/>					    
								<span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="model" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
					      	
				      	</label>
				      	<label class="input-group tips" title="Model z systemu" id="model_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="model_grp" value="1">
							    </span>	
							    <p class="form-control"></p>	
				      	</label>
			      	</div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						{{ Form::text('engine', '', array('id'=>'engine', 'class' => 'form-control upper tips', 'disabled'=>'disabled', 'placeholder' => 'silnik', 'title' => 'Dane silnika'))  }}				      	
				    </div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						<label class="input-group  " id="year_production_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="year_production_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="Rok produkcji z serwera IL" disabled="true" name="year_production" placeholder="rok produkcji"/>					    
								<span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="year_production" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
					      	
				      	</label>
				      	<label class="input-group tips" title="Rok produkcji z systemu" id="year_production_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="year_production_grp" value="1">
							    </span>	
							    <p class="form-control"></p>	
				      	</label>
			      	</div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						{{ Form::text('first_registration', '', array('id'=>'first_registration', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'data pierwszej rejestracji', 'title' => 'Data pierwszej rejestracji'))  }}
				    </div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						{{ Form::text('mileage', '', array('id'=>'mileage', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'przebieg', 'title' => 'Przebieg'))  }}
							
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
								<span class="input-group-addon">
							    	<input type="radio" name="owner_grp" value="0" checked>
							    </span>	
							    <p class="form-control tips required" title="Właściel z serwera IL" disabled="true" name="owner">Właściciel</p>	
							    <span class="input-group-btn">
						        	<button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-owner"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
						      	</span>	
				      	</label>
			      	</div>
				</div>
				<div class="col-md-6 col-lg-6 editable marg-btm">
					<div class="form-group">
						<label class="input-group  marg-btm" id="client_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="client_grp" value="0" checked>
							    </span>	
							    <p class="form-control tips required" title="Klient z serwera IL" disabled="true" name="client">Klient</p>	
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
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						<label class="input-group  marg-btm" id="end_leasing_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="end_leasing_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="Data końca leasingu z serwera IL" disabled="true" name="end_leasing_desc" placeholder="Data końca leasingu"/>					    
							    <input type="hidden" name="end_leasing"/>
				      	</label>
				      	<label class="input-group tips" title="Data końca leasingu z systemu" id="end_leasing_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="end_leasing_grp" value="1">
							    </span>	
							    <p class="form-control"></p>	
				      	</label>
			      	</div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						<label class="input-group  marg-btm" id="contract_status_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="contract_status_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="Status umowy z serwera IL" disabled="true" name="contract_status_desc" placeholder="Status umowy" />					    
							    <input type="hidden" name="contract_status"/>
				      	</label>
				      	<label class="input-group tips" title="Status umowy z systemu" id="contract_status_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="contract_status_grp" value="1">
							    </span>	
							    <p class="form-control"></p>	
				      	</label>
			      	</div>
				</div>
			</div>
		</div>
		
		
	  	<div class="form-group">
	  		<h4 class="inline-header"><span>Dane zgłaszającego:</span></h4>
		    <div class="row injury_bg">
		    	<div class="col-md-4 col-lg-3 marg-btm tips" title ='Imię zgłaszającego'>
			    {{ Form::text('notifier_name', '', array('class' => 'form-control bold  required upper', 'placeholder' => 'imię'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nazwisko zgłaszającego'>
			    {{ Form::text('notifier_surname', '', array('class' => 'form-control bold required upper', 'placeholder' => 'nazwisko'))  }} 
				</div>
			    <div class="col-md-4 col-lg-3 marg-btm tips"  title ='Telefon zgłaszającego'>
			    {{ Form::text('notifier_phone', '', array('class' => 'form-control bold required upper', 'placeholder' => 'telefon'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Email zgłaszającego'>
			    {{ Form::text('notifier_email', '', array('class' => 'form-control bold email', 'placeholder' => 'email'))  }} 
			    </div>
			    
			    
			</div>	
	  	</div>	

	  	<div class="form-group">
	  		<h4 class="inline-header"><span>Zgłoszenia:</span></h4>

	  		<div class="row">
	  			<div class="col-md-12 col-lg-10 col-lg-offset-1 marg-btm">
	  				<div class="row">
	  					<div class="panel panel-primary">
	  						<div class="panel-heading">Zgłoszenie - 1 </div>
  							<div class="panel-body">
  								<div class="col-md-12">
  								<label >Proces:</label>
  								</div>
			  					<div class="col-md-3">
						  			<div class="btn-group-vertical notifi-list" data-toggle="buttons">
						  				@foreach($processes as $k => $process)
						  					<label class="btn btn-default">
										    	<input type="radio" class="notifi-process sr-only required" count="1" value="{{ $process->id }}" required> {{ $process->name }}
                                                @if($process->description != '')
                                                <i class="fa fa-info-circle blue tips pull-right" title="{{ $process->description }}"></i>
                                                @endif
										    </label>
									  	@endforeach
									</div>
								</div>
								<div class="col-md-12 marg-btm" style="margin-top:20px;">
									<div class="btn-group" data-toggle="buttons">
									  <label class="btn btn-danger priority">
									    <input type="checkbox" name="priority[1]" value="1"> <i class="fa fa-bolt"></i> zgłoszenie priorytetowe
									  </label>
									</div>
								</div>
								<div class="col-md-12">
									<label >Informacja wewnętrzna:</label>
									{{ Form::textarea('info[1]', '', array('class' => 'form-control  bold', 'placeholder' => 'Informacja wewnętrzna'))  }}
								</div>
								{{Form::hidden('process[1]', '', array('id' => 'process_1', 'class' => 'required process_hidden'))  }}
							</div>
						</div>
					</div>
					<button type="button" class="btn  btn-primary pull-right " id="add-notifi" count="1"><span class="glyphicon glyphicon-plus-sign"></span> kolejne zgłoszenie</button>
				</div>
	  		</div>

	  	</div>

		
		{{Form::token()}}	
		{{Form::hidden('vehicle_id', '', array('id' => 'vehicle_id'))}}
		{{Form::hidden('client_id', '', array('id' => 'client_id'))}}
		{{Form::hidden('owner_id', '', array('id' => 'owner_id'))}}

		<?php //adm-administator, inf-infolinia?>
		{{Form::hidden('insert_role', 'adm')}}


	</div>

	<div class="row marg-btm">
		<h4 class="inline-header "></h4>
		<div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">

			{{ Form::submit('Zapisz',  array('class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }} 
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
			$(document).on('click', '.del-notifi', function(){
				$(this).closest('.row').remove();
			});

			$("form").submit(function(e) {
			     var self = this;
			     e.preventDefault();
			     
			     if($("form").valid()){
			     	if($('#vehicle_id').val() != ''){
			     		pass = 1;
			     		$('.process_hidden').each(function(){
			     			if( !isset($(this).val()) || $(this).val() == '' || $(this).val() == null )
			     				pass = 0;
			     		});
			     		if(pass == 0){
			     			$('#modal .modal-title').html('Komunikat');
							$('#modal .modal-body').html('Proszę wybrać proces dla każdego ze zgłoszeń.');
							$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
							$('#modal').modal('show');
			     		}else
			     			self.submit();
			     	}else{
			     		$('#modal .modal-title').html('Komunikat');
						$('#modal .modal-body').html('Proszę wprowadzić dane pojazdu istniejącego w bazie.');
						$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
						$('#modal').modal('show');
			     	}
			     }			     
			     return false; //is superfluous, but I put it here as a fallback

			});			

	    	
	    <?php //wyszukiawnie samochodu?>
	    	var focus = 0;
	    	$('#registration').focusout(function(){
	    		if($(this).val().length == 7 && focus == 0){
	    			focus = 1;
		    		$.ajax({
				        url: "<?php echo  URL::route('vehicle-registration-isdl-getList');?>",
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
                                    if(item.status == 1)
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

                                setTimeout(function(){
                                    focus = 0;
                                }, 1000);
                            }


                            if(isset(vehicle) && vehicle.id != $('#vehicle_id').val()){
				        		$('#modal').modal('hide');
			        			//samochód istnieje w bazie systemu
					        	$('#vehicle_id').val(vehicle.id);
					            $('#nr_contract').val(vehicle.nr_contract);

					            if(vehicle.vin != '' && isset(vehicle.vin))
									$('#vin_idea input[name=vin]').val(vehicle.vin).removeAttr('disabled');
					            $('#vin_idea button').removeAttr('disabled');
                                
					            if(vehicle.brand != '' && isset(vehicle.brand))
					            	$('#brand_idea input[name=brand]').val(vehicle.brand).removeAttr('disabled');
					            $('#brand_idea button').removeAttr('disabled');

					            if(vehicle.model != '' && isset(vehicle.model))
					            	$('#model_idea input[name=model]').val(vehicle.model).removeAttr('disabled');
					            $('#model_idea button').removeAttr('disabled');

					            $('#engine').val(vehicle.engine).removeAttr('disabled');

					            if(vehicle.year_production != '' && isset(vehicle.year_production))
					            	$('#year_production_idea input[name=year_production]').val(vehicle.year_production).removeAttr('disabled');
					            $('#year_production_idea button').removeAttr('disabled');

					            $('#first_registration').val(vehicle.first_registration).removeAttr('disabled').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });

					            $('#mileage').val(vehicle.mileage).removeAttr('disabled');

					            if(vehicle.client != '' && isset(vehicle.client))
					            	$('#client_idea p').html(vehicle.client).removeAttr('disabled');
					            $('#client_idea button').removeAttr('disabled');

					            $('#client_id').val(vehicle.client_id);

					            if(vehicle.owner != '' && isset(vehicle.owner))
					           		$('#owner_idea p').html(vehicle.owner).removeAttr('disabled');
					            $('#owner_idea button').removeAttr('disabled');

					            $('#owner_id').val(vehicle.owner_id);

					            if(vehicle.end_leasing != '' && isset(vehicle.end_leasing)){
					            	$('#end_leasing_idea input[name=end_leasing_desc]').val(vehicle.end_leasing);
					            	$('#end_leasing_idea input[name=end_leasing]').val(vehicle.end_leasing);
					            }
					            $('#end_leasing_idea button').removeAttr('disabled');

					            if(vehicle.contract_status != '' && isset(vehicle.contract_status)){
					            	$('#contract_status_idea input[name=contract_status_desc]').val(vehicle.contract_status);
					            	$('#contract_status_idea input[name=contract_status]').val(vehicle.contract_status);
					            }
					            $('#contract_status_idea button').removeAttr('disabled');

					            
					            $('#assistance_idea select[name=assistance]').removeAttr('disabled').find('option[value="'+vehicle.assistance+'"]').attr("selected",true);
					            $('#assistance_idea button').removeAttr('disabled');


					            if(vehicle.nr_policy != '')
					            	$('#nr_policy_idea input[name=nr_policy]').val(vehicle.nr_policy).removeAttr('disabled');
					            $('#nr_policy_idea button').removeAttr('disabled');

					            $('input[name=driver_surname]').removeAttr('disabled');
					            $('input[name=driver_name]').removeAttr('disabled');
					            $('input[name=driver_phone]').removeAttr('disabled');
					            $('input[name=driver_email]').removeAttr('disabled');
					            $('input[name=driver_city]').removeAttr('disabled');

					            $.each(vehicle, function(i, item) {
								    if( i.substr(i.length - 7) == '_system' ){
								    	if(vehicle[i] != '0000-00-00' && vehicle[i] != 0 && vehicle[i] != ''){
								    		$('#'+i).parent().addClass('focus-change');
					            			$('#'+i).show(500); 
					            			if(i == 'assistance_system'){
					            				if(vehicle[i] == 1)
					            					$('#'+i+' p.form-control').html('tak');
					            				else
					            					$('#'+i+' p.form-control').html('nie');
					            			}else
					            				$('#'+i+' p.form-control').html(vehicle[i]);
					            			parent = $('#'+i).closest('.row').find('div.editable .form-group').css('height', '88px');
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
					if($(this).val() != '' && $(this).val().length > 7){
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
				        url: "<?php echo  URL::route('vehicle-registration-isdl-getList');?>",
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
                                    if(item.status == 1)
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

                                setTimeout(function(){
                                    focus = 0;
                                }, 1000);
                            }


                            if(isset(vehicle) && vehicle.id != $('#vehicle_id').val()){
				        		$('#modal').modal('hide');
			        			//samochód istnieje w bazie systemu
					        	$('#vehicle_id').val(vehicle.id);
					            $('#registration').val(vehicle.registration);

					            if(vehicle.vin != '' && isset(vehicle.vin))
									$('#vin_idea input[name=vin]').val(vehicle.vin).removeAttr('disabled');
					            $('#vin_idea button').removeAttr('disabled');

					            if(vehicle.brand != '' && isset(vehicle.brand))
					            	$('#brand_idea input[name=brand]').val(vehicle.brand).removeAttr('disabled');
					            $('#brand_idea button').removeAttr('disabled');

					            if(vehicle.model != '' && isset(vehicle.model))
					            	$('#model_idea input[name=model]').val(vehicle.model).removeAttr('disabled');
					            $('#model_idea button').removeAttr('disabled');

					            $('#engine').val(vehicle.engine).removeAttr('disabled');

					            if(vehicle.year_production != '' && isset(vehicle.year_production))
					            	$('#year_production_idea input[name=year_production]').val(vehicle.year_production).removeAttr('disabled');
					            $('#year_production_idea button').removeAttr('disabled');

					            $('#first_registration').val(vehicle.first_registration).removeAttr('disabled').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });

					            $('#mileage').val(vehicle.mileage).removeAttr('disabled');

					            if(vehicle.client != '' && isset(vehicle.client))
					            	$('#client_idea p').html(vehicle.client).removeAttr('disabled');
					            $('#client_idea button').removeAttr('disabled');

					            $('#client_id').val(vehicle.client_id);

					            if(vehicle.owner != '' && isset(vehicle.owner))
					           		$('#owner_idea p').html(vehicle.owner).removeAttr('disabled');
					            $('#owner_idea button').removeAttr('disabled');

					            $('#owner_id').val(vehicle.owner_id);

					            if(vehicle.end_leasing != '' && isset(vehicle.end_leasing))
					            	$('#end_leasing_idea input[name=end_leasing]').val(vehicle.end_leasing).removeAttr('disabled').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
					            $('#end_leasing_idea button').removeAttr('disabled');

					            if(vehicle.contract_status != '' && isset(vehicle.contract_status))
					            	$('#contract_status_idea input[name=contract_status]').val(vehicle.contract_status).removeAttr('disabled');
					            $('#contract_status_idea button').removeAttr('disabled');


					            $('#assistance_idea select[name=assistance]').removeAttr('disabled').find('option[value="'+vehicle.assistance+'"]').attr("selected",true);
					            $('#assistance_idea button').removeAttr('disabled');

					            if(vehicle.nr_policy != '')
					            	$('#nr_policy_idea input[name=nr_policy]').val(vehicle.nr_policy).removeAttr('disabled');
					            $('#nr_policy_idea button').removeAttr('disabled');

					            $('input[name=driver_surname]').removeAttr('disabled');
					            $('input[name=driver_name]').removeAttr('disabled');
					            $('input[name=driver_phone]').removeAttr('disabled');
					            $('input[name=driver_email]').removeAttr('disabled');
					            $('input[name=driver_city]').removeAttr('disabled');

					            $.each(vehicle, function(i, item) {
								    if( i.substr(i.length - 7) == '_system' ){
								    	if(vehicle[i] != '0000-00-00' && vehicle[i] != 0 && vehicle[i] != ''){
								    		$('#'+i).parent().addClass('focus-change');
					            			$('#'+i).show(500); 
					            			if(i == 'assistance_system'){
					            				if(vehicle[i] == 1)
					            					$('#'+i+' p.form-control').html('tak');
					            				else
					            					$('#'+i+' p.form-control').html('nie');
					            			}else
					            				$('#'+i+' p.form-control').html(vehicle[i]);
					            			parent = $('#'+i).closest('.row').find('div.editable .form-group').css('height', '88px');
					            		}
					            	}
								});

								//sprawdzenie czy istnieje szkody na ten pojazd w systemie
								$.ajax({
							        url: "<?php echo  URL::route('vehicle-check-injuries');?>",
							        data: {
							        	vehicle_id: vehicle.id,
										vehicle_type: vehicle.vehicle_type,
							        	_token: $('input[name="_token"]').val()
							        },
							        async: false,
							        cache: false,
							        type: "POST",
							        success: function( data ) {
							        	data = tryParseJSON( data );
							        	if(data.exists == 1){
							        		$('#num_exists').html(data.count);
							        		$('#show_exists').removeClass('hidden').addClass('show');
							        		$('#exists_injuries').html(data.dataHtml);							        		
							        	}else{
							        		$('#num_exists').html('0');
							        		$('#show_exists').addClass('hidden').removeClass('show');
							        		$('#exists_injuries').html('');
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
	    
	 
	    
	    $(document).on('change', '.notifi-process', function(){
	    	boxNotifi = $(this);
	    	process_id = $(this).val();
	    	count = $(this).attr('count');

	    	$.ajax({
		        url: "<?php echo  URL::route('dok.notifications.create.processes');?>",
		        data: {
		        	process_id: process_id,
		        	count: count,
		        	_token: $('input[name="_token"]').val()
		        },
		        type: "POST",
		        success: function( data ) {
		        	if(data == 0){
		        		boxNotifi.parent().parent().parent().nextAll('.child_process').remove();
		        		$('#process_'+count).val(boxNotifi.val());
		        	}else{
		        		boxNotifi.parent().parent().parent().nextAll('.child_process').remove();
		            	$( data ).insertAfter( boxNotifi.parent().parent().parent() );
		            	$('#process_'+count).val('');
		            }
		        }
		    });
	    });

	    $('#add-notifi').on('click', function(){
	    	count = $(this).attr('count');

	    	$.ajax({
		        url: "<?php echo  URL::route('dok.notifications.create.getNewGroup');?>",
		        data: {
		        	count: count,
		        	_token: $('input[name="_token"]').val()
		        },
		        type: "POST",
		        success: function( data ) {
		        	$( data ).insertBefore( $('#add-notifi') );
		        }
		    });

		    $('#add-notifi').attr('count', parseInt(count)+1 );
	    });

	  

	   

      });

		
      
    </script>
  
@stop

