<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych miejsca zdarzenia</h4>
  </div>
  <div class="modal-body" style="overflow:hidden;">
  	<form action="{{ URL::route('dos.other.injuries.setEditInjuryMap', array($id)) }}" method="post"  id="dialog-injury-form">
  		{{Form::token()}}
  		{{Form::hidden('lat', $injury->lat, array('id' => 'lat'))}}
		{{Form::hidden('lng', $injury->lng, array('id' => 'lng'))}}
  		<div class="form-group">
		    <label >Miejsce zdarzenia:</label>
		    <div class="row">
			    <div class="col-md-6 col-lg-6 marg-btm">
			    {{ Form::text('event_city', $injury->event_city, array('class' => 'form-control required', 'id' => 'city', 'placeholder' => 'miasto'))  }} 
				</div>
			    <div class="col-md-6 col-lg-6 marg-btm">
			    {{ Form::text('event_street', $injury->event_street, array('class' => 'form-control  ', 'id' => 'street', 'placeholder' => 'ulica'))  }} 
			    </div>
			</div>
			
	  	</div>
		<div class="form-group">
			<div class="checkbox">
				<label>
				  <input type="checkbox" id="mapShow" name="if_map"
				  @if($injury->if_map == 1)
				   checked
				  @endif
				  > Zlokalizuj na mapie
				</label>
				
			</div>
			<div class="checkbox"  style="display:none;">
				<label >
				  <input type="checkbox" id="correctMap" name="if_map_correct"
				  @if($injury->if_map_correct == 1)
				   checked
				  @endif
				  > Skoryguj niezależnie pozycję
				</label>
			</div>	
			
			<div id="map-canvas-edit" style="width:100%; height:400px; display:none; "></div>
		
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
	<button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
</div>

<script type="text/javascript" >
		 
		var mapa_edit;
		var geocoder_edit = new google.maps.Geocoder();
		var marker_edit ;
		var infowindow_edit = new google.maps.InfoWindow();
		var correlation = 0;

		function initialize_edit() {
		  
		    var myOptions = {
		      zoom: 7,
			  scrollwheel: true,
			  navigationControl: false,
			  mapTypeControl: false,
		      center: new google.maps.LatLng(52.528846,17.071874),				
				};

			mapa_edit = new google.maps.Map(document.getElementById('map-canvas-edit'), myOptions);

			google.maps.event.addListener(mapa_edit, 'click', function(event) {
  				placeMarker_edit(event.latLng);
  			});

			var slat = $('#lat').val();
			var slng = $('#lng').val();
			
			if(slat != ''  && slng != ''){
				latlng = new google.maps.LatLng(slat,slng);
				placeMarker_edit(latlng);
			}

		};

		function initListenerDrag(){
		  google.maps.event.addListener(marker_edit, "dragend", function(event) { 															  
				var lat_d = event.latLng.lat(); 
				var lng_d = event.latLng.lng(); 
				$('#lat').val(lat_d);
				$('#lng').val(lng_d);
				var latlng = new google.maps.LatLng(lat_d, lng_d);
				if(correlation == 0){
				  geocoder_edit.geocode( {'latLng': latlng}, function(results, status) {
					  if (status == google.maps.GeocoderStatus.OK) {

						  	infowindow_edit.setContent(results[0].formatted_address);
						  	infowindow_edit.open(mapa_edit, marker_edit);
						  
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

		function placeMarker_edit(location) {
		  if ( marker_edit ) {
			marker_edit.setPosition(location);
		  } else {
			marker_edit = new google.maps.Marker({
			  position: location,
			  draggable:true,
			  map: mapa_edit
			});
		  }
		  $('#lat').val(location.lat());
		  $('#lng').val(location.lng());
		  if(correlation == 0){
			  geocoder_edit.geocode( {'latLng': location}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					  
					  infowindow_edit.setContent('<div style="height:50px;">'+results[0].formatted_address+'</div>');
					  infowindow_edit.open(mapa_edit, marker_edit);
					  
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
			geocoder_edit.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					mapa_edit.panTo(results[0].geometry.location);
					slat = results[0].geometry.location.lat();
					slng = results[0].geometry.location.lng();
					
					$('#lat').val(slat);
					$('#lng').val(slng);
					
					var latlng = new google.maps.LatLng(slat,slng);
					if(marker_edit == null){
						marker_edit = new google.maps.Marker({
						  position: latlng,
						  draggable:true,
						  map: mapa_edit,
					  	});
					}else{								
						marker_edit.setPosition(latlng);
					}

					infowindow_edit.setContent(results[0].formatted_address);
			      	infowindow_edit.open(mapa_edit, marker_edit);
					
					initListenerDrag() ;

					if( $('#street').val() == '')
						mapa_edit.setZoom(12);
					else
						mapa_edit.setZoom(16);
				} else {
					//alert("Nie można zlokalizować podanego adresu.");
				}
			});
		}

		function lokalizacja_wyszukiwanie_simple(){
			var slat = 0;
			var slng = 0;
			var address = $("#city").val()+' '+$('#street').val();
			geocoder_edit.geocode( { 'address': address}, function(results, status) {
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

		$(document).ready(function(){
			$('#city, #street').keyup(function() {
				delay( function() {
					lokalizacja_wyszukiwanie_simple();
				}, 100);
			});

			$('#mapShow').on('change', function(){

	      		if($('#mapShow').is(':checked')){
		      		$('#map-canvas-edit').show();
		      		$('#correctMap').parent().parent().show();
		  			initialize_edit();
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
	  				$('#map-canvas-edit').hide();
	  				$('#correctMap').parent().parent().hide();
	  			}
	      	}).change();
		});


</script>