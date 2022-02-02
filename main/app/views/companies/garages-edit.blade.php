@extends('layouts.main')

@section('header')

Edycja oddziału do serwisu <i>{{$company->name}}</i>

@stop

@section('main')



<form action="{{ URL::to('company/garages/update', array($branch->id) ) }}" method="post" role="form">
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="{{ URL::to('company/garages/show', array($branch->id) ) }}" class="btn btn-default">Anuluj</a>
			{{ Form::submit('Zapisz zmiany',  array('class' => 'btn btn-primary'))  }}
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
		    <label >Adres:</label>
		    <div class="row">
			    <div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('city', $branch->city, array('class' => 'form-control required', 'id' => 'city', 'placeholder' => 'miasto'))  }}
				</div>
				<div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('code', $branch->code, array('class' => 'form-control required ', 'id' => 'code', 'placeholder' => 'kod pocztowy'))  }}
			    </div>
			    <div class="col-md-4 col-lg-3 marg-btm">
			    {{ Form::text('street', $branch->street, array('class' => 'form-control required ', 'id' => 'street', 'placeholder' => 'ulica'))  }}
			    </div>
				<div class="col-md-4 col-lg-3 marg-btm">
					{{ Form::select('voivodeship_id', $voivodeships, $branch->voivodeship_id, ['class' => 'form-control', 'id' => 'voivodeship_id']) }}
				</div>
			</div>
			<div class="checkbox pull-right" style="z-index:100">
				<label>
					<input type="checkbox" name="suspended"
					@if($branch->suspended == 1)
					 checked="checked"
					@endif> Zawieszona
				</label>
			</div>
	  	</div>
	  	<div class="form-group">
			<div class="checkbox">
				<label>
				  <input type="checkbox" id="mapShow" name="if_map"
				  @if($branch->if_map == 1)
				   checked="checked"
				  @endif
				  > Zlokalizuj na mapie
				</label>

			</div>
			<div class="checkbox"  style="display:none;">
				<label >
				  <input type="checkbox" id="correctMap" name="if_map_correct"
				  @if($branch->if_map_correct == 1)
				  	checked="checked"
				  @endif
				  > Skoryguj niezależnie pozycję
				</label>
			</div>

			<div id="map-canvas" style="width:100%; height:400px; display:none; "></div>

		</div>
	  	<div class="form-group">
		    <label >Dane warsztatu:</label>
		    <div class="row">
		    	<div class="col-md-3 col-lg-3 marg-btm">
					<div class="form-group">
						<label>Nazwa skrócona:</label>
						{{ Form::text('short_name', $branch->short_name, array('class' => 'form-control  tips', 'placeholder' => 'nazwa skrócona', 'title' => 'nazwa skrócona'))  }}
					</div>
				</div>
			    <div class="col-md-3 col-lg-3 marg-btm tips"  title = "telefon">
					<div class="form-group">
						<label>Telefon:</label>
						@if($company->type == 1)
							{{ Form::text('phone', $branch->phone, array('class' => 'form-control  required', 'placeholder' => 'telefon'))  }}
						@else
							{{ Form::text('phone', $branch->phone, array('class' => 'form-control  ', 'placeholder' => 'telefon'))  }}
						@endif
					</div>
				</div>
				<div class="col-md-3 col-lg-3 marg-btm">
					<div class="form-group">
						<label>Email główny (oddzielone przecinkiem):</label>
						{{ Form::text('email', $branch->email, array('class' => 'form-control   tips', 'placeholder' => 'email (oddzielone przecinkiem)', 'title' => 'email główny (oddzielone przecinkiem)'))  }}
					</div>
			    </div>
			    <div class="col-md-3 col-lg-3 marg-btm">
					<div class="form-group">
						<label>Email dodatkowy (oddzielone przecinkiem):</label>
						{{ Form::text('other_emails', $branch->other_emails, array('class' => 'form-control   tips', 'placeholder' => 'email (oddzielone przecinkiem)', 'title' => 'email dodatkowy (oddzielone przecinkiem)'))  }}
					</div>
			    </div>
			    <div class="col-md-3 col-lg-3 marg-btm">
					<div class="form-group">
						<label>Zakres obsługi:</label>
						<select name="typeGarages_id[]" id="typeGarages" class="form-control" multiple="multiple">
							<?php foreach($typegarages as $k => $v){
								echo '<option value="'.$v->id.'"';
								if( isset($typegaragesReSel[$v->id]) && $typegaragesReSel[$v->id] == 1){
									echo 'selected';
								}
								echo '>'.$v->name.'</option>';
							}?>
						</select>
					</div>
			    </div>
			   	<div class="col-md-3 col-lg-3 marg-btm">
					<div class="form-group">
						<label>Priorytet:</label>
						<select name="priority" class="form-control">
							<?php for($i=0; $i<=9; $i++){
								echo '<option value="'.$i.'"';
								 if($branch->priority == $i){
								 echo 'selected';
								}

								echo '>priorytet '.$i.'</option>';
							}?>
						</select>
					</div>
			    </div>
				<div class="col-md-3 col-lg-3 marg-btm">
					<div class="form-group">
						<label>NIP:</label>
						{{ Form::text('nip', $branch->nip, array('class' => 'form-control tips', 'placeholder' => 'NIP', 'title' => 'NIP'))  }}
					</div>
				</div>
			</div>
	  	</div>
		<div class="form-group">
			<label>Osoby kontaktowe:</label>
			{{ Form::textarea('contact_people', $branch->contact_people, array('class' => 'form-control  ', 'rows' => '3', 'placeholder' => 'Osoby kontaktowe'))  }}
		</div>
	  	<div class="form-group">
		    <label >Ilość aut zastępczych:</label>
		    <div class="row">
		    	@foreach($typevehicles as $type)
		    	<div class="col-md-3 col-lg-3 marg-btm" >
		    		<input id="car{{$type->id}}" type="text" name="car{{$type->id}}" desc="{{$type->name}}" class="carSpinner"
		    		@if(isset($typevehiclesReSel[$type->id]))
		    			value="{{$typevehiclesReSel[$type->id]}}"
		    		@else
		    			value="0"
		    		@endif
		    		>
				</div>
				@endforeach
			</div>
	  	</div>
	  	<div class="form-group">
	  		<div class="row">
	  			<div class="col-md-3 col-lg-3 marg-btm">
	  				<div class="checkbox">
						<label>
						  <input type="checkbox" id="tug" name="tug"
						  @if($branch->tug == 1)
						   checked
						  @endif
						  > Posiada holownik
						</label>
					</div>
	  			</div>
	  			<div class="col-md-3 col-lg-3 marg-btm">
	  				<div class="checkbox">
						<label>
						  <input type="checkbox" id="tug24h" name="tug24h"
						  @if($branch->tug == 1)
						  	@if($branch->tug24h == 1)
						   		checked
						   	@endif
						  @else
						  	disabled="disabled"
						  @endif
						  > Dostępność holownika 24h
						</label>
					</div>
	  			</div>
	  		</div>

			<label>Holownik (uwagi):</label>
			{{ Form::textarea('tug_remarks', $branch->tug_remarks, array('class' => 'form-control  ', 'rows' => '3','placeholder' => 'Uwagi dotyczące holownika'))  }}
	  	</div>


		<div class="form-group">
			<label >Obsługiwane samochody dostawcze:</label>
			{{ Form::textarea('delivery_cars', $branch->delivery_cars, array('class' => 'form-control  ', 'rows' => '1', 'placeholder' => 'Obsługiwane samochody dostawcze'))  }}
		</div>
		<div class="form-group">
			<label>Godziny pracy:</label>
			<div class="row">
				<div class="col-md-3 col-lg-3 marg-btm">
					Od: <input type="time" class="form-control" name="open_time" value="{{$branch->open_time}}">
				</div>
				<div class="col-md-3 col-lg-3 marg-btm">
					Do: <input type="time" class="form-control" name="close_time" value="{{$branch->close_time}}">
				</div>
			</div>
		</div>

		<div class="form-group">
		    <label >Uwagi:</label>
		    {{ Form::textarea('remarks', $branch->remarks, array('class' => 'form-control  ', 'placeholder' => 'uwagi'))  }}
		</div>

		<div class="form-group">
			<label >Kierowalność/priorytety:</label>
			{{ Form::textarea('priorities', $branch->priorities, array('class' => 'form-control  ', 'rows' => '3', 'placeholder' => 'kierowalność/priorytety'))  }}
		</div>

			{{Form::token()}}
			{{Form::hidden('lat', $branch->lat, array('id' => 'lat'))}}
			{{Form::hidden('lng', $branch->lng, array('id' => 'lng'))}}


	</div>
</form>



@stop

@section('headerJs')
	@parent
	<script type="text/javascript">

		var mapa;
		var geocoder = new google.maps.Geocoder();
		var marker ;
		var infowindow = new google.maps.InfoWindow();
		var correlation = <?php echo $branch->if_map_correct;?>;
		var begin = 1;

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
						  adressA[1] = adressA[1].replace(rx, '');
						  adressA[1] = $.trim(adressA[1]);
					  }
					  $("#city").val(adressA[1]);
					  $('#street').val(adressA[0]);
					  $('#code').change();
				  }
			  });
		  }
		  initListenerDrag() ;

		}

		function lokalizacja_wyszukiwanie(){
			var slat = 0;
			var slng = 0;
			if(begin == 0){
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
							  map: mapa,
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
					}
				});
			}else{

				slat = $('#lat').val();
				slng = $('#lng').val();
				latlng = new google.maps.LatLng(slat,slng, true);

				mapa.panTo(latlng);

				if(marker == null){
					marker = new google.maps.Marker({
					  position: latlng,
					  draggable:true,
					  map: mapa,
				  	});
				}else{
					marker.setPosition(latlng);
				}

				infowindow.setContent('<div claas="InfoWindow">'+$('#street').val()+', '+$('#code').val()+' '+$('#city').val()+'</div>');
		      	infowindow.open(mapa, marker);

				initListenerDrag() ;
				mapa.setZoom(16);

				begin = 0;
			}
		}

		var delay = ( function() {
			var timer = 0;
			return function(callback, ms) {
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();


		function movieFormatResult(brand) {
	        var markup = "<table class='movie-result'><tr><td>"+brand.name+"</td></tr></table>";
	        return markup;
	    }

	    function movieFormatSelection(movie) {
	        return movie.name;
	    }

      $(document).ready(function(){
			$("form").submit(function(e) {
			     var self = this;
			     e.preventDefault();
			     if($("form").valid()){
			     	self.submit();
			     }
			     return false; //is superfluous, but I put it here as a fallback
			});

	      	$('#typeGarages').multiselect({
	            buttonWidth: $('input[name="email"]').width()+25,
	            buttonText: function(options) {
	                if (options.length === 0) {
	                    return 'Wybierz typy warsztatu <b class="caret"></b>';
	                }
	                else {
	                	var count = 0;
	                    var selected = '';
	                    options.each(function() {
	                        selected += $(this).text() + ', ';
	                        count++;
	                    });
	                    if(count > 2)
	                    	return 'wybrano ' +count+ ' typy <b class="caret"></b>';
	                    else
	                    	return selected.substr(0, selected.length -2) + ' <b class="caret"></b>';
	                }
	            }
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
					});

		      		$('#correctMap').on('change', function(){
		      			if($('#correctMap').is(':checked')){
		      				correlation = 1;
		      			}else{
		      				correlation = 0;
		      			}

		      		}).change();

	  			}else{
	  				$('#map-canvas').hide();
	  				$('#correctMap').parent().parent().hide();
	  				begin = 0;
	  			}
	      	}).change();

	      	$('#city, #street').keyup(function() {
				delay( function() {
					lokalizacja_wyszukiwanie();
				}, 500);
			});

	      	$('#test').click(function(){
	      		console.log($('#typeGarages').val());
	      	});

	    	$('#brands_o').select2({
	    		placeholder: "Wybierz obsługiwane marki samochodów",
    			minimumInputLength: 2,
    			multiple:true,
    			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			        url: "<?php echo  URL::to('companies/brands-list', array(1) );?>",
			        dataType: 'json',
			        type: "GET",
			        data: function (term, page) {
			            return {
			                q: term,
			                _token: $('input[name="_token"]').val()
			            };
			        },
			        results: function (data) {

			            return {results: data};
			        }

			    },
			    initSelection: function(element, callback) {
			        var id=$(element).val();
			        if (id!=="") {
			            $.ajax("<?php echo  URL::to('companies/brands-list-connect');?>", {
			            	type: "GET",
			                data: {
			                    _token: $('input[name="_token"]').val(),
			                    q: id
			                },
			                dataType: "json"
			            }).done(function(data) { callback(data); });
			        }
			    },
			});

	    	$('#brands_c').select2({
	    		placeholder: "Wybierz obsługiwane marki samochodów",
    			minimumInputLength: 2,
    			multiple:true,
    			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			        url: "<?php echo  URL::to('companies/brands-list', array(2) );?>",
			        dataType: 'json',
			        type: "GET",
			        data: function (term, page) {
			            return {
			                q: term,
			                _token: $('input[name="_token"]').val()
			            };
			        },
			        results: function (data) {

			            return {results: data};
			        }

			    },
			    initSelection: function(element, callback) {
			        var id=$(element).val();
			        if (id!=="") {
			            $.ajax("<?php echo  URL::to('companies/brands-list-connect');?>", {
			            	type: "GET",
			                data: {
			                    _token: $('input[name="_token"]').val(),
			                    q: id
			                },
			                dataType: "json"
			            }).done(function(data) { callback(data); });
			        }
			    },
			});

	    	//
		  $('#authorizations').select2({
			  placeholder: "Wybierz autoryzacje",
			  minimumInputLength: 2,
			  multiple:true,
			  ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				  url: "<?php echo  URL::to('companies/brands-list', array(1) );?>",
				  dataType: 'json',
				  type: "GET",
				  data: function (term, page) {
					  return {
						  q: term,
						  _token: $('input[name="_token"]').val()
					  };
				  },
				  results: function (data) {

					  return {results: data};
				  }

			  },
			  initSelection: function(element, callback) {
				  var id=$(element).val();
				  if (id!=="") {
					  $.ajax("<?php echo  URL::to('companies/brands-list-connect');?>", {
						  type: "GET",
						  data: {
							  _token: $('input[name="_token"]').val(),
							  q: id
						  },
						  dataType: "json"
					  }).done(function(data) { callback(data); });
				  }
			  },
		  });
		  //

			$('.carSpinner').TouchSpin({
                min: 0,
                max: 1000000000,
                stepinterval: 1,
                prefix: ' '
            }).each(function(){
            	desc = $(this).attr('desc');
            	$(this).prev().html(desc).css('border-right','0px');
            });

            $('#tug').change(function(){
            	if($(this).is(':checked'))
            		$('#tug24h').removeAttr('disabled');
            	else
            		$('#tug24h').attr('disabled', 'disabled');
            });

		  $('#code').on('change keyup paste click', function(){
			  var $code = $(this).val();
			  if($code.length == '6'){
				  $.ajax({
					  type: 'GET',
					  url: '/company/garages/check-voivodeship',
					  data: {'code': $code},
					  assync:false,
					  cache: false,
					  dataType: 'json',
					  success: function(data) {
						  if(data.status == 'ok')
						  {
							  $('#voivodeship_id').val( data.voivodeship_id );
						  }
					  }
				  });
			  }
		  });

      });



    </script>

@stop
