@extends('layouts.main')

@section('styles')
@parent


     


@stop

@section('header')
	
Dodawanie nowej szkody


	
@stop

@section('main')
<form action="{{ URL::route('injuries-post-create' ) }}" method="post" role="form">
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="/" class="btn btn-default">Anuluj</a>
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
			<h4 class="inline-header"><span>Zgłoszenie szkody:</span></h4>
			<div class="row injury_bg">
				<div class="col-md-4 col-lg-3 marg-btm tips" title= 'Rejestracja'>
			    {{ Form::text('registration', '', array('class' => 'form-control  required upper', 'id' => 'registration', 'placeholder' => 'Rejestracja', 'tabindex' => '1'))  }} 
				</div>
				
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy leasingowej'>
			    {{ Form::text('nr_contract', '', array('class' => 'form-control  required upper', 'id' => 'nr_contract', 'placeholder' => 'Nr umowy leasingowej'))  }} 
			    </div>
                <div class="col-md-4 col-lg-3 " >
                    <button type="button" class="btn btn-warning btn-sm btn-block hidden marg-btm " id="show_exists">W systemie istnieją już zgłoszenia dla tego pojazdu <span class="badge" id="num_exists"></span></button>
                </div>
                <div class="col-md-4 col-lg-3">
                    <span class="btn btn-info btn-sm hidden btn-block  marg-btm" disabled="disabled" id="liqudation_card"></span>
                </div>
                <div class="col-md-4 col-lg-3 " >
                    <button type="button" class="btn btn-info btn-sm btn-block hidden marg-btm " id="show_matched_letters">W systemie istnieją pisma dopasowane do pojazdu <span class="badge" id="num_matched"></span></button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-8 col-md-offset-2 marg-btm " style="display:none;" id="exists_injuries" >
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-8 col-md-offset-2 marg-btm " style="display:none;" id="matched_letters" >
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
							    <input class="form-control tips required" title="VIN z serwera IL" disabled="true" name="vin" placeholder="VIN" tabindex="2"/>					    
								{{Form::hidden('vin', '', array('id' => 'vin'))}}
					      	
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
								{{Form::hidden('brand', '', array('id' => 'brand'))}}
					      	
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
								{{Form::hidden('model', '', array('id' => 'model'))}}
					      	
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
						{{ Form::text('engine', '', array( 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'silnik', 'title' => 'Dane silnika'))  }}				      	
						{{Form::hidden('engine', '', array('id' => 'engine'))}}
				    </div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						<label class="input-group  " id="year_production_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="year_production_grp" value="0" checked>
							    </span>	
							    <input class="form-control tips required upper" title="Rok produkcji z serwera IL" disabled="true" name="year_production" placeholder="rok produkcji"/>					    
								{{Form::hidden('year_production', '', array('id' => 'year_production'))}}
					      	
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
						{{ Form::text('first_registration', '', array('class' => 'form-control tips upper ', 'disabled'=>'disabled', 'placeholder' => 'data pierwszej rejestracji', 'title' => 'Data pierwszej rejestracji'))  }}
						{{Form::hidden('first_registration', '', array('id' => 'first_registration'))}}
				    </div>
				</div>
				<div class="col-md-4 col-lg-3 editable marg-btm">
					<div class="form-group">
						{{ Form::text('mileage', '', array('id'=>'mileage', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'przebieg', 'title' => 'Przebieg'))  }}
				    </div>
				</div>
                <div class="col-md-4 col-lg-3 editable marg-btm">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="cfm" id="cfm" value="1" disabled> CFM
                        </label>
                    </div>
                </div>

				
			</div>			
		</div>
		{{Form::hidden('insurance_company_name', '', array('id' => 'insurance_company_name'))}}
		{{Form::hidden('expire', '', array('id' => 'expire'))}}
		{{Form::hidden('nr_policy', '', array('id' => 'nr_policy'))}}
		{{Form::hidden('insurance', '', array('id' => 'insurance'))}}
		{{Form::hidden('contribution', '', array('id' => 'contribution'))}}
		{{Form::hidden('assistance', '', array('id' => 'assistance'))}}
		{{Form::hidden('assistance_name', '', array('id' => 'assistance_name'))}}
		{{Form::hidden('contract_status', '', array('id' => 'contract_status'))}}
		{{Form::hidden('end_leasing', '', array('id'=>'end_leasing'))}}
		<div class="form-group">
			<h4 class="inline-header"><span>Dane właściciela i klienta:</span></h4>
			<div class="row">
				<div class="col-md-6 col-lg-6 editable marg-btm">
					<div class="form-group">
						<label class="input-group  marg-btm" id="owner_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="owner_grp" value="0" checked>
							    </span>	
							    <p class="form-control tips required" title="Właściel z serwera IL" disabled="true" name="owner">Właściel</p>	
							    <span class="input-group-btn">
						        	<button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-owner"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
						      	</span>	
						      	<!--			    
								<span class="input-group-btn">
						        	<button class="btn btn-default edit-owner" desc="owner" type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
						      	-->
					      	
				      	</label>
				      	<label class="input-group tips" title="Właściciel z systemu" id="owner_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="owner_grp" value="1">
							    </span>	
							    <p class="form-control"></p>
							    <span class="input-group-btn">
						        	<button class="btn btn-default show-owner"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
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
						      	<!--
								<span class="input-group-btn">
						        	<button class="btn btn-default edit-client" desc="client" type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
						      	-->
					      	
				      	</label>
				      	<label class="input-group tips" title="Klient z systemu" id="client_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="client_grp" value="1">
							    </span>	
							    <p class="form-control"></p>
							    <span class="input-group-btn">
						        	<button class="btn btn-default show-client"  type="button" ><span class="fa fa-search" data-toggle="modal" data-target="#modal"></span></button>
						      	</span>	
				      	</label>
			      	</div>
				</div>			
			</div>
		</div>
		
		<div class="form-group">
		    <h4 class="inline-header"><span>Dane kierowcy:</span></h4>
		    <div class="row injury_bg">
			    
				<div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('driver_name', '', array('class' => 'form-control  tips upper bold' , 'placeholder' => 'imię', 'disabled' => 'disabled', 'title' => 'Imię kierowcy'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('driver_surname', '', array('class' => 'form-control  tips upper bold', 'placeholder' => 'nazwisko', 'disabled' => 'disabled', 'title' => 'Nazwisko kierowcy'))  }} 
				</div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('driver_phone', '', array('class' => 'form-control tips upper bold', 'placeholder' => 'telefon', 'disabled' => 'disabled', 'title' => 'Telefon kierowcy'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('driver_email', '', array('class' => 'form-control tips email bold', 'placeholder' => 'email', 'disabled' => 'disabled', 'title' => 'Email kierowcy'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('driver_city', '', array('class' => 'form-control tips upper bold', 'placeholder' => 'miasto', 'disabled' => 'disabled', 'title' => 'Miasto kierowcy'))  }} 
			    </div>
			    
			</div>	
	  	</div>
	  	<div class="form-group">
	  		<h4 class="inline-header"><span>Dane zgłaszającego: <button type="button" class="btn btn-primary btn-xs" id="cp_driver"> kopiuj dane kierowcy</button></span></h4>
		    <div class="row injury_bg">
			    
				<div class="col-md-4 col-lg-3 marg-btm tips" title ='Imię zgłaszającego'>
			    {{ Form::text('notifier_name', '', array('class' => 'form-control upper  bold', 'placeholder' => 'imię'))  }}
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nazwisko zgłaszającego'>
			    {{ Form::text('notifier_surname', '', array('class' => 'form-control upper  bold', 'placeholder' => 'nazwisko'))  }}
				</div>
			    <div class="col-md-4 col-lg-3 marg-btm tips"  title ='Telefon zgłaszającego'>
			    {{ Form::text('notifier_phone', '', array('class' => 'form-control upper  bold', 'placeholder' => 'telefon'))  }}
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Email zgłaszającego'>
			    {{ Form::text('notifier_email', '', array('class' => 'form-control  email bold', 'placeholder' => 'email'))  }} 
			    </div>
			   
			    
			</div>
            <div class="row injury_bg">
                <div class="col-md-12 marg-btm " >
                    <div class="radio">
                        <label class="tips" title ='Zgłaszający jest osobą kontaktową'>
                            {{ Form::checkbox('contact_person', '2'); }}
                            osoba do kontaktu
                        </label>
                    </div>
                </div>
            </div>
	  	</div>	
		<div class="form-group">
			<h4 class="inline-header"><span>Dane szkody:</span></h4>
		    <div class="row injury_bg">
			    <div class="col-md-4 col-lg-4 marg-btm">
			   		<label >Data zdarzenia:</label>
			    	{{ Form::text('date_event', '', array('class' => 'form-control required bold', 'id'=>'date_event', 'placeholder' => 'data zdarzenia'))  }} 
			    </div>
			    
			</div>
		
		    
		    <div class="row injury_bg">
		    	<div class="col-md-12 marg-btm">
		    		<label >Miejsce zdarzenia:</label>
		    	</div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('event_city', '', array('class' => 'form-control  upper bold', 'id' => 'city', 'placeholder' => 'miasto'))  }}
				</div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('event_street', '', array('class' => 'form-control  upper bold', 'id' => 'street', 'placeholder' => 'ulica'))  }} 
			    </div>
			
				<div class="col-md-12 marg-btm"> 	
					<div class="checkbox">
						<label>
						  <input type="checkbox" id="mapShow" name="if_map"> Zlokalizuj na mapie
						</label>
						
					</div>
				</div>
				<div class="col-md-12 marg-btm">
					<div class="checkbox"  style="display:none;">
						<label >
						  <input type="checkbox" id="correctMap" name="if_map_correct"> Skoryguj niezależnie pozycję
						</label>
					</div>	
				</div>
				<div class="col-md-12 marg-btm">
					<div id="map-canvas" style="width:100%; height:400px; display:none; "></div>
				</div>
			</div>
		</div>
	  	<div class="form-group">
	  		<h4 class="inline-header"><span>Rodzaj zdarzenia:</span></h4>
	  		<div class="row injury_bg">
	  			<div class="col-md-12 marg-btm">
		  			<?php 
		  			$lp = 0;

		  			foreach ($type_incident as $k => $v) {	  				
		  				if($lp == 0) echo '<div class="row">';	  				
		  			?>

			  			<div class="col-md-3 col-lg-2 ">
			  				<div class="radio">
							  <label>
							    <input type="radio" name="zdarzenie" id="zdarzenie{{ $v->id }}" value="{{ $v->id }}" >
							    {{ $v->name }}
							  </label>
							</div>
			  			</div>	
		  			<?php 
		  				$lp++;
		  				if($lp == 6){
		  					echo '</div>';
		  					$lp = 0;	
		  				} 
		  			}?>
					</div>
	  			</div>
	  		
	  			<div class="col-md-12 marg-btm">
	  				<label >Opis szkody:</label>
	  				{{ Form::textarea('remarks', '', array('class' => 'form-control  bold', 'placeholder' => 'Opis szkody'))  }}
	  			</div>
	  		</div>

	  	</div>
	  	
		<div class="form-group">
			<h4 class="inline-header"><span>Informacje dodatkowe:</span></h4>
			<div class="row injury_bg">
				<div class="col-md-4 col-lg-3 marg-btm">
					<label >Typ szkody: <button type="button" class="btn btn-primary btn-xs" id="offender_info" data-toggle="modal" data-target="#modal-offender" style="display:none;"> dane sprawcy</button></label>
			    	<select name="injuries_type" id="injuries_type" class="form-control required" >
			    		<option value="">---wybierz---</option>
			    		<?php foreach($injuries_type as $k => $v){
			    			echo '<option value="'.$v->id.'">'.$v->name.'</option>';
			    		}?>
			    	</select>
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    	<label >Nr szkody:</label>
			    	{{ Form::text('injury_nr', '', array('class' => 'form-control upper bold', 'id'=>'injury_nr', 'placeholder' => 'nr szkody'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    	<label >Zakład ubezpieczeń:</label>
			    	<span class="input-group  " >
					    
					    <select name="insurance_company_id" id="insurance_company_id" class="form-control required" >
					    	<option value="">---wybierz---</option>
				    		<?php foreach($insurance_companies as $k => $v){
				    			echo '<option value="'.$v->id.'">'.$v->name.'</option>';
				    		}?>
				    	</select>				    
						<span class="input-group-btn">
				        	<button class="btn btn-default show-insurance_company" type="button" ><span class="fa fa-search"></span></button>
				      	</span>
			      	</span>			    	
			    </div>
			</div>
			{{Form::hidden('receives', '0', array('id' => 'receives'))}}
			{{Form::hidden('invoicereceives', '0', array('id' => 'invoicereceives'))}}			
			<div class="row injury_bg">
				<div class="col-md-4 col-lg-3 marg-btm">
			   		<label >Zawiadomiono policję:</label>
			    	<select name="police" id="police" class="form-control required" >
                        <option value="-1" selected>nie ustalono</option>
			    		<option value="0">nie</option>
			    		<option value="1">tak</option>
			    	</select>
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    	{{ Form::text('police_nr', '', array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_nr', 'disabled' => 'disabled', 'placeholder' => 'nr zgłoszenia policji', 'title' => 'nr zgłoszenia policji'))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    	{{ Form::text('police_unit', '', array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_unit', 'disabled' => 'disabled', 'placeholder' => 'jednostka policji', 'title' => 'jednostka policji' ))  }} 
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    	{{ Form::text('police_contact', '', array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_contact', 'disabled' => 'disabled', 'placeholder' => 'kontakt z policją', 'title' => 'kontakt z policją'))  }} 
			    </div>
			</div>
			<div class="row injury_bg">
				<div class="col-md-4 col-lg-3 marg-btm">
					<div class="checkbox">
					  <label>
					    <input type="checkbox" name="if_statement" value="1">
					    Spisano oświadczenia
					  </label>
					</div>
				</div>
				<div class="col-md-4 col-lg-3 marg-btm">
					<div class="checkbox">
					  <label>
					    <input type="checkbox" name="if_registration_book " value="1">
					    Zabrano dowód rejestracyjny
					  </label>
					</div>
				</div>
			</div>
			<div class="row injury_bg">
				<div class="col-md-4 col-lg-3 marg-btm">
					<div class="checkbox">
					  <label>
					    <input type="checkbox" name="if_towing" value="1">
					    Wymaga holowania
					  </label>
					</div>
				</div>
				{{Form::hidden('if_courtesy_car', '1', array('id' => 'if_courtesy_car'))}}
				{{Form::hidden('if_door2door', '0', array('id' => 'if_door2door'))}}
				
			</div>
			<div class="row injury_bg marg-btm">
				<div class="col-md-12 marg-btm">
				    <label >Informacja wewnętrzna:</label>
				    {{ Form::textarea('info', '', array('class' => 'form-control  bold', 'placeholder' => 'Informacja wewnętrzna'))  }}
				</div>
			</div>
		</div>
		
		
			{{Form::token()}}	
			{{Form::hidden('lat', '', array('id' => 'lat'))}}
			{{Form::hidden('lng', '', array('id' => 'lng'))}}
			{{Form::hidden('vehicle_id', '', array('id' => 'vehicle_id'))}}
			{{Form::hidden('driver_id', '', array('id' => 'driver_id'))}}
			{{Form::hidden('client_id', '', array('id' => 'client_id'))}}
			{{Form::hidden('owner_id', '', array('id' => 'owner_id'))}}
			<?php //adm-administator, inf-infolinia?>
			{{Form::hidden('insert_role', 'inf')}}

	</div>

<div class="modal fade " id="modal-offender" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Dane sprawcy</h4>
      </div>
      <div class="modal-body">
      		<div class="form-group">
      			<div class="row">
		  			<div class="col-md-6 marg-btm">
		  				{{ Form::text('offender_surname', '', array('class' => 'form-control upper', 'placeholder' => 'nazwisko', 'disabled' => 'disabled'))  }}
		  			</div>
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_name', '', array('class' => 'form-control upper',  'placeholder' => 'imię', 'disabled' => 'disabled'))  }}
      				</div>
  				</div>

  				<h4 class="inline-header"><span>Adres zameldowania:</span></h4>
  				<div class="row">
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_post', '', array('class' => 'form-control upper',  'placeholder' => 'kod pocztowy', 'disabled' => 'disabled'))  }}
      				</div>
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_city', '', array('class' => 'form-control upper',  'placeholder' => 'miasto', 'disabled' => 'disabled'))  }}
      				</div>
  				</div>
  				<div class="row">
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_street', '', array('class' => 'form-control upper',  'placeholder' => 'ulica', 'disabled' => 'disabled'))  }}
      				</div>
  				</div>

  				<h4 class="inline-header"><span>Dane pojazdu:</span></h4>
  				<div class="row">
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_registration', '', array('class' => 'form-control upper',  'placeholder' => 'nr rejestracyjny', 'disabled' => 'disabled'))  }}
      				</div>
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_car', '', array('class' => 'form-control upper',  'placeholder' => 'marka i model pojazdu', 'disabled' => 'disabled'))  }}
      				</div>
  				</div>
  				<div class="row">
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_oc_nr', '', array('class' => 'form-control upper',  'placeholder' => 'nr polisy OC', 'disabled' => 'disabled'))  }}
      				</div>
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_zu', '', array('class' => 'form-control upper',  'placeholder' => 'nazwa ZU', 'disabled' => 'disabled'))  }}
      				</div>
  				</div>
  				<div class="row">
      				<div class="col-md-6  marg-btm">
      					{{ Form::text('offender_expire', '', array('class' => 'form-control upper',  'placeholder' => 'data ważności polisy', 'disabled' => 'disabled'))  }}
      				</div>
      				<div class="col-md-6  marg-btm">      					
				    	<select name="offender_owner" class="form-control" disabled="disabled" >
				    		<option value="1">Sprawca jest właścicielem pojazdu:</option>
				    		<option value="1">tak</option>
				    		<option value="0">nie</option>
				    	</select>
      				</div>
  				</div>
  				<div class="row">
      				<div class="col-md-12  marg-btm">
      					{{ Form::textarea('offender_remarks', '', array('class' => 'form-control ',  'placeholder' => 'uwagi', 'disabled' => 'disabled', 'style' => 'height:50px;'))  }}
      				</div>
  				</div>
      		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
      </div>
    </div>
  </div>
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
		 
		var mapa;
		var geocoder = new google.maps.Geocoder();
		var marker ;
		var infowindow = new google.maps.InfoWindow();
		var correlation = 0;

		function initialize() {
		  
		    var myOptions = {
		      zoom: 7,
			  scrollwheel: true,
			  navigationControl: false,
			  mapTypeControl: false,
		      center: new google.maps.LatLng(52.528846,17.071874),
				};

			mapa = new google.maps.Map(document.getElementById('map-canvas'), myOptions);

			google.maps.event.addListener(mapa, 'click', function(event) {
  				placeMarker(event.latLng);
  			});

			var slat = $('#lat').val();
			var slng = $('#lng').val();
			
			if(slat != ''  && slng != ''){
				latlng = new google.maps.LatLng(slat,slng);
				placeMarker(latlng);
			}

		};

		function initListenerDrag(){
		  google.maps.event.addListener(marker, "dragend", function(event) { 															  
				var lat_d = event.latLng.lat(); 
				var lng_d = event.latLng.lng(); 
				$('#lat').val(lat_d);
				$('#lng').val(lng_d);
				var latlng = new google.maps.LatLng(lat_d, lng_d);
				if(correlation == 0){
				  geocoder.geocode( {'latLng': latlng}, function(results, status) {
					  if (status == google.maps.GeocoderStatus.OK) {

						  	infowindow.setContent(results[0].formatted_address);
						  	infowindow.open(mapa, marker);
						  
						  var adressA = results[0].formatted_address.split(', ');
						  var kod = adressA[1];
						  var rx = /\d\d-\d\d\d/;
						  var wynik = rx.exec(kod);
						  if (wynik)
						  {
							  kod = wynik[0]; 
							  adressA[1] = adressA[1].replace(rx, '');
							  adressA[1] = $.trim(adressA[1]);
						  }
						  else
						  {
							  kod = '';
						  }		
							
						  //$('#id_kod_pocztowy').val(kod);
						  
					  	$("#city").val(adressA[1]);
					  	$('#street').val(adressA[0]);
						  
					  }
				  });
				}
		  }); 	
		}

		function placeMarker(location) {
		  if ( marker ) {
			marker.setPosition(location);
		  } else {
			marker = new google.maps.Marker({
			  position: location,
			  draggable:true,
			  map: mapa
			});
		  }
		  $('#lat').val(location.lat());
		  $('#lng').val(location.lng());
		  if(correlation == 0){
			  geocoder.geocode( {'latLng': location}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					  
					  infowindow.setContent('<div style="height:30px;">'+results[0].formatted_address+'</div>');
					  infowindow.open(mapa, marker);
					  
					  var adressA = results[0].formatted_address.split(', ');
					  
					  var kod = adressA[1];
					  var rx = /\d\d-\d\d\d/;
					  var wynik = rx.exec(kod);
					  if (wynik)
					  {
						  kod = wynik[0]; 
						  adressA[1] = adressA[1].replace(rx, '');
						  adressA[1] = $.trim(adressA[1]);
					  }
					  else
					  {
						  kod = '';
					  }		
						
		  			  //$('#code').val(kod);
					  
					  $("#city").val(adressA[1]);
					  $('#street').val(adressA[0]);
					  
					  
				  }
			  });
		  }
		  initListenerDrag() ;
		  
		}

		function lokalizacja_wyszukiwanie(){
			var slat = 0;
			var slng = 0;
			var address = $("#city").val()+' '+$('#street').val();
			geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					mapa.panTo(results[0].geometry.location);
					slat = results[0].geometry.location.lat();
					slng = results[0].geometry.location.lng();
					
					$('#lat').val(slat);
					$('#lng').val(slng);
					
					var latlng = new google.maps.LatLng(slat,slng);
					if(marker == null){
						marker = new google.maps.Marker({
						  position: latlng,
						  draggable:true,
						  map: mapa
					  	});
					}else{								
						marker.setPosition(latlng);
					}

					infowindow.setContent(results[0].formatted_address);
			      	infowindow.open(mapa, marker);
					
					initListenerDrag() ;

					if( $('#street').val() == '')
						mapa.setZoom(12);
					else
						mapa.setZoom(16);
				} else {
					//alert("Nie można zlokalizować podanego adresu.");
				}
			});
		}

		function lokalizacja_wyszukiwanie_simple(){
			var slat = 0;
			var slng = 0;
			var address = $("#city").val()+' '+$('#street').val();
			geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					slat = results[0].geometry.location.lat();
					slng = results[0].geometry.location.lng();
					
					$('#lat').val(slat);
					$('#lng').val(slng);
				}
			});
		}

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
			     	if($('#vehicle_id').val() != '')
			     		self.submit();
			     	else{
			     		$('#modal .modal-title').html('Komunikat');
						$('#modal .modal-body').html('Proszę wprowadzić dane pojazdu istniejącego w bazie.');
						$('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
						$('#modal').modal('show');
						btn.removeAttr('disabled');
			     	}
                }else{
                     btn.removeAttr('disabled');
                }
                return false; //is superfluous, but I put it here as a fallback
			});			

			$('#city, #street').keyup(function() {
				delay( function() {
					lokalizacja_wyszukiwanie_simple();
				}, 100);
			}).focusout(function(){
				lokalizacja_wyszukiwanie_simple();
			});
	      	
	  		
	    	$('#mapShow').on('change', function(){

	      		if($('#mapShow').is(':checked')){
		      		$('#map-canvas').show();
		      		$('#correctMap').parent().parent().show();
		  			initialize();
		      		lokalizacja_wyszukiwanie();
		      		$('#city, #street').keyup(function() {
						delay( function() {
							lokalizacja_wyszukiwanie();
						}, 500);
					}).focusout(function(){
						lokalizacja_wyszukiwanie();
					});

		      		$('#correctMap').on('change', function(){
		      			if($('#correctMap').is(':checked')){	
		      				correlation = 1;
		      			}else{
		      				correlation = 0;
		      			}

		      		});

	  			}else{
	  				$('#map-canvas').hide();
	  				$('#correctMap').parent().parent().hide();
	  			}
	      	});

	    	$(document).on('click', '#show_exists', function(){
	    		$('#exists_injuries').toggle(500);
	    	});

		  	$(document).on('click', '#show_matched_letters', function(){
			  $('#matched_letters').toggle(500);
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
									$('#vin_idea input[name=vin]').val(vehicle.vin);

					            if(vehicle.brand != '' && isset(vehicle.brand))
					            	$('#brand_idea input[name=brand]').val(vehicle.brand);

					            if(vehicle.model != '' && isset(vehicle.model))
					            	$('#model_idea input[name=model]').val(vehicle.model);

					            $('#engine').val(vehicle.engine);

					            if(vehicle.year_production != '' && isset(vehicle.year_production))
					            	$('#year_production_idea input[name=year_production]').val(vehicle.year_production);

					            $('input[name=first_registration]').val(vehicle.first_registration);

					            $('#mileage').val(vehicle.mileage).removeAttr('disabled');

                                if(vehicle.cfm == '1')
                                    $('#cfm').prop('checked', true);

                                $('#cfm').removeAttr('disabled');

					            if(vehicle.client != '' && isset(vehicle.client))
					            	$('#client_idea p').html(vehicle.client).removeAttr('disabled');
					            $('#client_idea button').removeAttr('disabled');

					            $('#client_id').val(vehicle.client_id);

					            if(vehicle.owner != '' && isset(vehicle.owner))
					           		$('#owner_idea p').html(vehicle.owner).removeAttr('disabled');
					            $('#owner_idea button').removeAttr('disabled');

					            $('#owner_id').val(vehicle.owner_id);

					            if(vehicle.contract_status != '' && isset(vehicle.contract_status)){
					            	$('input[name=contract_status]').val(vehicle.contract_status);
					            }
					            if(vehicle.end_leasing != '' && isset(vehicle.end_leasing)){
					            	$('input[name=end_leasing]').val(vehicle.end_leasing);
					            }

					            if(vehicle.insurance_company_name != '' && isset(vehicle.insurance_company_name))
					            	$('#insurance_company_name').val(vehicle.insurance_company_name);


					            if(vehicle.expire != '0000-00-00' && vehicle.expire != '' && isset(vehicle.expire))
					            	$('input[name=expire]').val(vehicle.expire);

					            $('#contribution').val(vehicle.contribution);

					            $('#assistance').val(vehicle.assistance);


					            $("select[name='netto_brutto'] option[value='" + data.netto_brutto + "']").attr("selected","selected");
					            $("select[name='netto_brutto']").removeAttr('disabled');

					            if(vehicle.assistance_name != '' && isset(vehicle.assistance_name))
					            	$('input[name=assistance_name]').val(vehicle.assistance_name);

					            $('#insurance').val(vehicle.insurance);

					            if(vehicle.nr_policy != '')
					            	$('input[name=nr_policy]').val(vehicle.nr_policy);

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

								$.ajax({
									url: "{{ URL::route('routes.post', ['injuries', 'getMatchedLetters']) }}",
									data: {
										vehicle_id: vehicle.id,
                                        vehicle_type: vehicle.vehicle_type,
										_token: $('input[name="_token"]').val()
									},
									async: false,
									cache: false,
									type: "POST",
									dataType: "json",
									success: function( data ) {
										if(data.matched == 1){
											$('#num_matched').html(data.count);
											$('#show_matched_letters').removeClass('hidden').addClass('show');
											$('#matched_letters').html(data.dataHtml);
										}else{
											$('#num_matched').html('0');
											$('#show_matched_letters').addClass('hidden').removeClass('show');
											$('#matched_letters').html('');
										}
									}
								});

								{{-- sprawdzenie czy istnieje karta likwidacji szkód w systemie --}}
								$.ajax({
									url: "<?php echo  URL::route('vehicle-check-liquidationCard');?>",
									data: {
										vehicle_id: vehicle.id,
										_token: $('input[name="_token"]').val()
									},
									async: false,
									cache: false,
									type: "POST",
									success: function( data ) {
										data = tryParseJSON( data );
										if(data.exists == 1){
											$('#liqudation_card').removeClass('hidden').addClass('show').html(data.dataHtml);
										}else{
											$('#liqudation_card').removeClass('show').addClass('hidden').html('');
										}
									}
								});

							    setTimeout(function(){
							    	focus = 0;
							    }, 1000);

					        }else if(count_found > 0){
                                $('#modal').modal('hide');

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
		        	setTimeout(function(){
			  			$('#registration').focusout();
			  		},500);
		        }
		    }).bind("keypress", function(e) {
			  if( e.which == 13 ){
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
									$('#vin_idea input[name=vin]').val(vehicle.vin);

					            if(vehicle.brand != '' && isset(vehicle.brand))
					            	$('#brand_idea input[name=brand]').val(vehicle.brand);

					            if(vehicle.model != '' && isset(vehicle.model))
					            	$('#model_idea input[name=model]').val(vehicle.model);

					            $('#engine').val(vehicle.engine);

					            if(vehicle.year_production != '' && isset(vehicle.year_production))
					            	$('#year_production_idea input[name=year_production]').val(vehicle.year_production);

					            $('input[name=first_registration]').val(vehicle.first_registration);

					            $('#mileage').val(vehicle.mileage).removeAttr('disabled');

                                if(vehicle.cfm == '1')
                                    $('#cfm').prop('checked', true);

                                $('#cfm').removeAttr('disabled');

					            if(vehicle.client != '' && isset(vehicle.client))
					            	$('#client_idea p').html(vehicle.client).removeAttr('disabled');
					            $('#client_idea button').removeAttr('disabled');

					            $('#client_id').val(vehicle.client_id);

					            if(vehicle.owner != '' && isset(vehicle.owner))
					           		$('#owner_idea p').html(vehicle.owner).removeAttr('disabled');
					            $('#owner_idea button').removeAttr('disabled');

					            $('#owner_id').val(vehicle.owner_id);

					            if(vehicle.contract_status != '' && isset(vehicle.contract_status)){
					            	$('input[name=contract_status]').val(vehicle.contract_status);
					            }
					            if(vehicle.end_leasing != '' && isset(vehicle.end_leasing)){
					            	$('input[name=end_leasing]').val(vehicle.end_leasing);
					            }

					            if(vehicle.insurance_company_name != '' && isset(vehicle.insurance_company_name))
					            	$('#insurance_company_name').val(vehicle.insurance_company_name);


					            if(vehicle.expire != '0000-00-00' && vehicle.expire != '' && isset(vehicle.expire))
					            	$('input[name=expire]').val(vehicle.expire);

					            $('#contribution').val(vehicle.contribution);

					            $('#assistance').val(vehicle.assistance);


					            $("select[name='netto_brutto'] option[value='" + vehicle.netto_brutto + "']").attr("selected","selected");
					            $("select[name='netto_brutto']").removeAttr('disabled');

					            if(vehicle.assistance_name != '' && isset(vehicle.assistance_name))
					            	$('input[name=assistance_name]').val(vehicle.assistance_name);

					            $('#insurance').val(vehicle.insurance);

					            if(vehicle.nr_policy != '')
					            	$('input[name=nr_policy]').val(vehicle.nr_policy);


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

								$.ajax({
									url: "{{ URL::route('routes.post', ['injuries', 'getMatchedLetters']) }}",
									data: {
										vehicle_id: vehicle.id,
                                        vehicle_type: vehicle.vehicle_type,
										_token: $('input[name="_token"]').val()
									},
									async: false,
									cache: false,
									type: "POST",
									dataType: "json",
									success: function( data ) {
										if(data.matched == 1){
											$('#num_matched').html(data.count);
											$('#show_matched_letters').removeClass('hidden').addClass('show');
											$('#matched_letters').html(data.dataHtml);
										}else{
											$('#num_matched').html('0');
											$('#show_matched_letters').addClass('hidden').removeClass('show');
											$('#matched_letters').html('');
										}
									}
								});

								{{-- sprawdzenie czy istnieje karta likwidacji szkód w systemie --}}
								$.ajax({
									url: "<?php echo  URL::route('vehicle-check-liquidationCard');?>",
									data: {
										vehicle_id: vehicle.id,
										_token: $('input[name="_token"]').val()
									},
									async: false,
									cache: false,
									type: "POST",
									success: function( data ) {
										data = tryParseJSON( data );
										if(data.exists == 1){
											$('#liqudation_card').removeClass('hidden').addClass('show').html(data.dataHtml);
										}else{
											$('#liqudation_card').removeClass('show').addClass('hidden').html('');
										}
									}
								});

							    setTimeout(function(){
							    	focus = 0;
							    }, 1000);

					        }else if(count_found > 0){
                                $('#modal').modal('hide');

                                setTimeout(function(){
                                    focus = 0;
                                }, 1000);
                            }
						}
				    });
				}else if($(this).val() != '' && $(this).val().length > 10){
                        $('#modal .modal-title').html('Komunikat');
                        $('#modal .modal-body').html('Wprowadzono błędny numer umowy');
                        $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                        $('#modal').modal('show');

                        setTimeout(function(){
                            focus = 0;
                        }, 1000);

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
			
		<?php //wyszukiwanie kierowcy?>
			$( "input[name=driver_surname], input[name=driver_name]" ).autocomplete({
		      	source: function( request, response ) {
				    $.ajax({
				        url: "<?php echo  URL::route('drivers-getList');?>",
				        data: {
				        	id_client: $('#client_id').val(),
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
		        	$('input[name=driver_surname]').val(ui.item.surname);
		            $('input[name=driver_name]').val(ui.item.name);
		            $('input[name=driver_phone]').val(ui.item.phone);
		            $('input[name=driver_email]').val(ui.item.email);
		            $('input[name=driver_city]').val(ui.item.city);  
		            $('#driver_id').val(ui.item.id);	    
		        },
		        open: function(event, ui) {
		            $(".ui-autocomplete").css("z-index", 1000);
		        }
		    });
		
		<?php //wyszukiwanie zgłaszającego?>
			$( "input[name=notifier_surname], input[name=notifier_name]" ).autocomplete({
		      	source: function( request, response ) {
				    $.ajax({
				        url: "<?php echo  URL::route('drivers-getList');?>",
				        data: {
				        	id_client: $('#client_id').val(),
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
		        	$('input[name=notifier_surname]').val(ui.item.surname);
		            $('input[name=notifier_name]').val(ui.item.name);
		            $('input[name=notifier_phone]').val(ui.item.phone);
		            $('input[name=notifier_email]').val(ui.item.email);
		            $('input[name=notifier_city]').val(ui.item.city);  
		        },
		        open: function(event, ui) {
		            $(".ui-autocomplete").css("z-index", 1000);
		        }
		    });

		<?php //obsługa przycisku edycji ?>
			$('.edit-radio').click(function(){
				desc = $(this).attr('desc');
				
				$('input[name='+desc+']').removeAttr('disabled');
				
				if(desc == 'end_leasing'){
					$('input[name='+desc+']').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,minDate: "+0D",dateFormat: "yy-mm-dd" }).focus();
				}else if(desc == 'expire'){
					$('input[name='+desc+']').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,minDate: "+0D",dateFormat: "yy-mm-dd" }).focus();
				}else{
					$('input[name='+desc+']').focus();
				}		
				
				//$(this).attr('disabled', 'disabled');		

			});

		<?php //kopiowanie danych kierowcy?>
			$('#cp_driver').click(function(){
				$('input[name=notifier_surname]').val($('input[name=driver_surname]').val());
				$('input[name=notifier_name]').val($('input[name=driver_name]').val());
	            $('input[name=notifier_phone]').val($('input[name=driver_phone]').val());
	            $('input[name=notifier_email]').val($('input[name=driver_email]').val());
	            $('input[name=notifier_city]').val($('input[name=driver_city]').val());
			});
		<?php //wykrycie zmian w danych kierowcy?>
			$('input[name=driver_surname], input[name=driver_name], input[name=driver_phone], input[name=driver_email], input[name=driver_city]').keypress(function() {
				$('#driver_id').val('');	
			});

		<?php //dane sprawcy?>
			$('#injuries_type').change(function(){
				if($(this).val() == 2 || $(this).val() == 4 || $(this).val() == 5){
					$('#offender_info').show();
					$('#modal-offender input, #modal-offender select, #modal-offender textarea').removeAttr('disabled');
					$('input[name=offender_expire]').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
					$('#modal-offender').modal('show');
				}else{
					$('#offender_info').hide();
					$('#modal-offender input, #modal-offender select, #modal-offender textarea').attr('disabled','disabled');
				}
			});

		<?php //edycja danych sprawcy?>
			$('#offender_info').click(function(){
				$('#modal-offender').modal('show');
			});

		<?php //zmiana statusu zawiadomienia policji?>
			$('#police').change(function(){
				if($(this).val() == 1 ){
					$('#police_nr').removeAttr('disabled').focus();
					$('#police_unit').removeAttr('disabled');
					$('#police_contact').removeAttr('disabled');
				}else{
					$('#police_nr').attr('disabled','disabled');
					$('#police_unit').attr('disabled','disabled');
					$('#police_contact').attr('disabled','disabled');
				}
			});

	    	$('#date_event').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });

	    <?php //podgląd danych właściciela?>
	    	$('.show-owner').click(function(){
		       	hrf='/injuries/owner/show/'+$('#owner_id').val()+'/';       	

				$.get( hrf, function( data ) {
					$('#modal .modal-title').html('Dane właściciela');
				  	$('#modal .modal-body').html(data);
				});
	    	});
	    <?php //podgląd danych klienta?>
	    	$('.show-client').click(function(){
		       	hrf='/injuries/client/show/'+$('#client_id').val()+'/';       	

				$.get( hrf, function( data ) {
					$('#modal .modal-title').html('Dane klienta');
				  	$('#modal .modal-body').html(data);
				});
	    	});	    
	    <?php //edycja właściciela?>
	    	$('.edit-owner').click(function(){
	    		id = $('#owner_id').val();
	    		if(id == '') id = 0;

		       	hrf='/injuries/owner/edit/'+id+'/';       	
				$.get( hrf, function( data ) {
					$('#modal .modal-title').html('Zmiana właściciela pojazdu');
				  	$('#modal .modal-body').html(data);
				});
	    	});
	    <?php //edycja klienta?>
	    	$('.edit-client').click(function(){
	    		id = $('#client_id').val();
	    		if(id == '') id = 0;

		       	hrf='/injuries/client/edit/'+id+'/';       	
				$.get( hrf, function( data ) {
					$('#modal .modal-title').html('Zmiana klienta');
				  	$('#modal .modal-body').html(data);
				});
	    	});
	    

	    <?php //podgląd danych ubezpieczalni?>
	    	$('.show-insurance_company').click(function(){
		       	hrf='/injuries/insurance_company/show/'+$('#insurance_company_id').val()+'/';       	

				$.get( hrf, function( data ) {
					$('#modal .modal-title').html('Dane ubezpieczalni');
				  	$('#modal .modal-body').html(data);
				  	$('#modal').modal('show');
				});
	    	});

      });

		
      
    </script>
  
@stop

