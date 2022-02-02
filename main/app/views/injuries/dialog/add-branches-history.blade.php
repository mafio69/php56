  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Wyszukiwanie serwisu</h4>
  </div>
  <div class="modal-body" style="overflow:hidden; padding-bottom:0px; ">
  	<form action="{{ URL::to('injuries/store-branches-history', array($injury->id)) }}" method="post"  id="dialog-form">
        <input type="hidden" id="id_warsztat" name="branch_id" >
        {{Form::token()}}


        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Data dodania:</label>
                    <input type="text" class="form-control input-sm date" name="created_at" required>
                </div>
            </div>
			<div class="col-sm-12">
				<input class="form-control input-sm" id="branchName" type="text" placeholder="nazwa warsztatu"/>
			</div>
			<div class="col-sm-12 marg-btm "><h6>Serwisy dostępne w podanym zasięgu wyszukiwania:</h6></div>
			<div class="col-sm-12 marg-btm searched_com" id="searched_com"></div>
	  	</div>
  	</form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Dodaj wpis</button>
  </div>


<script type="text/javascript">
var mapa;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var geocoder = new google.maps.Geocoder();
var infowindow = new google.maps.InfoWindow();
var markers = new Array();
var companies = new Array();
var infoWindows = new Array();
var marker;
var marker_place;
var triangleCoords;
var bermudaTriangle;

function initialize() {
	directionsDisplay = new google.maps.DirectionsRenderer();
	var myOptions = {
	  zoom: 9,
	  center: new google.maps.LatLng(51.919438,19.145136),
	  mapTypeId: google.maps.MapTypeId.ROADMAP,
	  disableDoubleClickZoom: true,
	};
	mapa = new google.maps.Map(document.getElementById('map-canvas'),
		myOptions);

	google.maps.event.addListener(mapa, 'dblclick', function(event) {
		placeMarker(event.latLng);
	});
	google.maps.event.trigger(mapa, 'resize');
//	google.maps.event.addListener(mapa, 'click', function(event) {
//	  placeMarker(event.latLng);
//	});

}

function placeMarker(location) {
	$('#lat_point').val(location.lat());
	$('#lng_point').val(location.lng());
	lokalizacja_wyszukiwanie();
}

function clearMarkers() {
	for( i in markers ) {
		if(markers[i] != null){
			markers[i].setMap( null );
			markers[i] = null;
		}
	}
	markers = null;

	if(marker != null){
		marker.setMap(null	);
		marker = null;
	}
	if(marker_place != null){
		marker_place.setMap(null	);
		marker_place = null;
	}

	if(bermudaTriangle != null){
		bermudaTriangle.setMap(null	);
		bermudaTriangle = null;
	}

}
function clearCompanies() {
	for( i in companies ) {
		companies[i] = null;
	}
	companies = null;
}
function clearInfoWindows() {
	for( i in infoWindows ) {
		infoWindows[i].setMap( null );
		infoWindows[i] = null;
	}
	infoWindows = null;
}
function addMarkerWindow (m, iw, j) {
	google.maps.event.addListener(m, 'click', function() {
		for( i in infoWindows ) {
			infoWindows[i].close();
		}
		iw.open(mapa, m);

		for( i in markers ) {
			iconFile = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
			markers[i].setIcon(iconFile) ;
			infoWindows[i].close();
		}
		iconFile = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
		m.setIcon(iconFile) ;

		$('.bt_com_search').removeClass('active');
		$('.bt_com_search').each(function(){

			if( $(this).attr('id_marker') == j){
				$(this).addClass('active');
				$(this).click();
				$('#id_warsztat').val( $(this).attr('id') );
			}

		});



	});
	google.maps.event.addListener(m, 'mouseover', function() {
		iconFile = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
		m.setIcon(iconFile) ;
		iw.open(mapa, m);
	});
	google.maps.event.addListener(m, 'mouseout', function() {
		id_marker = $('.bt_com_search.active').attr('id_marker');
		if(id_marker != j){

			iconFile = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
			m.setIcon(iconFile) ;
		}
			iw.close();

	});
}
function lokalizacja_wyszukiwanie(){
	var slat = 0;
	var slng = 0;
	clearMarkers();

	slat = parseFloat($('#lat').val());
	slng = parseFloat($('#lng').val());

	slat_point = parseFloat($('#lat_point').val());
	slng_point = parseFloat($('#lng_point').val());

	mapa.panTo(new google.maps.LatLng(slat_point,slng_point));
	//mapa.setZoom(10);

	var tmp = new google.maps.LatLng(slat, slng);
	marker_place = new google.maps.Marker({
		draggable: false,
		position: tmp,
		map: mapa,
		title: 'miejsce zdarzenia',
		animation: google.maps.Animation.DROP
	});
	iconFile = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
	marker_place.setIcon(iconFile) ;

	promien = $('#amount').val();
	if($('#onBrand').is(':checked'))
		onBrand = 1;
	else
		onBrand = 0;

	if (ajaxReq != null) ajaxReq.abort();
	ajaxReq = $.ajax({
		url:"<?php echo URL::route('injuries-assignBranchesList', array($injury->id) ); ?>",
		data:{
			"slat": slat_point,
			"slng": slng_point,
			"promien": promien,
			"onBrand": onBrand,
			"type" : $('#typeCompany').val(),
			"_token": $('input[name="_token"]').val()
		},
		dataType: "json",
		type: "POST",
		success: function( data ) {

			markers = new Array(data.length);
			companies = new Array(data.length);

			if(data.length !=0 ){
				id_warsztat = $('#id_warsztat').val();
				if( $("#searched_com").hasClass('done')){
					$("#searched_com").accordion('destroy');
				}
				$('#searched_com').html('');
				for( i in data ) {
					//dodawanie markerów
					var tmp = new google.maps.LatLng(data[i].lat, data[i].lng);
					marker = new google.maps.Marker({
						draggable: false,
						position: tmp,
						map: mapa,
						title: data[i].nazwa,
						animation: google.maps.Animation.DROP
					});
					markers[i] = marker;
					//wypełnianie danymi tablicy z firmami
					companies[i] = new Array(5);
					companies[i][0] = data[i].nazwa;
					companies[i][1] = data[i].kod + ", " + data[i].miasto + ", " + data[i].ulica;
					companies[i][2] = data[i].id;
					//dodawanie okienek
					var tmpstr = "<h5>" + companies[i][0] + "</h5>" + companies[i][1] +'</span>';
					infoWindow = new google.maps.InfoWindow({
						content: tmpstr,
						maxWidth: 260
					});
					infoWindows[i] = infoWindow;
					addMarkerWindow(marker , infoWindow, i);
					$('#searched_com').append(data[i].dataText);

				}
				$('#searched_com').accordion({
					collapsible: true,
					heightStyle: "content",
					active: false
				}).addClass('done');

				directionsDisplay.setMap(null);
			}else{
				$('#searched_com').html('nie ma serwisu w podanym promieniu szkody');
			}
		}
	});

	lat_r = promien / 111.0;
	lng_r = promien / (111.0 * Math.cos(slat * Math.PI / 180));
	cor_a = new Array();

	if( slat_point >= 0 ) {
		cor_a['lat_lewy'] = slat_point - lat_r;
		cor_a['lat_prawy'] = slat_point + lat_r;
	} else {
		cor_a['lat_lewy'] = slat_point + lat_r;
		cor_a['lat_prawy'] = slat_point - lat_r;
	}
	if( slng_point >= 0 ) {
		cor_a['lng_lewy'] = slng_point - lng_r;
		cor_a['lng_prawy'] = slng_point + lng_r;
	} else {
		cor_a['lng_lewy'] = slng_point + lng_r;
		cor_a['lng_prawy'] = slng_point - lng_r;
	}


	triangleCoords = [
		new google.maps.LatLng(cor_a['lat_lewy'], cor_a['lng_lewy']),
		new google.maps.LatLng(cor_a['lat_prawy'], cor_a['lng_lewy']),
		new google.maps.LatLng(cor_a['lat_prawy'], cor_a['lng_prawy']),
		new google.maps.LatLng(cor_a['lat_lewy'], cor_a['lng_prawy'])
	  ];

	bermudaTriangle = new google.maps.Polygon({
		paths: triangleCoords,
		strokeColor: "#FF0000",
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: "#FF0000",
		fillOpacity: 0.10
	  });

	bermudaTriangle.setMap(mapa);

}

var delay = ( function() {
	var timer = 0;

	return function(callback, ms) {
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();
var ajaxReq = null;

  $(document).ready(function(){
      $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
  	$('#locate_on_map').on('click', function(){
		if($(this).is(':checked')) {
			$('#map-canvas').show();
			initialize()
			lokalizacja_wyszukiwanie();
		}else{
			$('#map-canvas').hide();
		}
	});


	$(document).on('mouseenter', '.bt_com_search', function(){
		id_marker = $(this).attr('id_marker');

		if($('#locate_on_map').is(':checked')) {
			if (id_marker) {
				iconFile = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
				markers[id_marker].setIcon(iconFile);
				infoWindows[id_marker].open(mapa, markers[id_marker]);
			}
		}

	}).on('mouseleave', '.bt_com_search', function(){
		id_marker = $('.bt_com_search.active').attr('id_marker');
		if($('#locate_on_map').is(':checked')) {
			for (i in markers) {
				if (i != id_marker) {
					iconFile = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
					markers[i].setIcon(iconFile);

				}
				infoWindows[i].close();
			}
		}
	}).on('click', '.bt_com_search', function(){
		$('.bt_com_search').removeClass('active').removeClass('green');
		$(this).addClass('active').addClass('green');
		$('#id_warsztat').val($(this).attr('id'));
	});


	$( "#slider" ).slider({
		  value:30,
		  min: 0,
		  max: 200,
		  slide: function( event, ui ) {
			$( "#amount" ).val(ui.value);
			delay( function() {
				lokalizacja_wyszukiwanie();
				$('#branchName').val(null);
				}, 500);
		  }
		});
	$( "#amount" ).val( $( "#slider" ).slider( "value" ));

	$('#typeCompany').change(function(){
		lokalizacja_wyszukiwanie();
		$('#branchName').val(null);
	});

	$('#onBrand').change(function(){
		lokalizacja_wyszukiwanie();
		$('#branchName').val(null);
	})

	$('#branchName').on('keyup', function(){
		term = $(this).val();

		if($('#locate_on_map').is(':checked')) {
			clearMarkers();

			slat = parseFloat($('#lat').val());
			slng = parseFloat($('#lng').val());

			slat_point = parseFloat($('#lat_point').val());
			slng_point = parseFloat($('#lng_point').val());

			mapa.panTo(new google.maps.LatLng(slat_point, slng_point));
			//mapa.setZoom(10);

			var tmp = new google.maps.LatLng(slat, slng);
			marker_place = new google.maps.Marker({
				draggable: false,
				position: tmp,
				map: mapa,
				title: 'miejsce zdarzenia',
				animation: google.maps.Animation.DROP
			});
			iconFile = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
			marker_place.setIcon(iconFile);
		}

		if (ajaxReq != null) ajaxReq.abort();
		ajaxReq = $.ajax({
			url:"<?php echo URL::route('injuries-assignBranchesNameList', array($injury->id) ); ?>",
			data:{
				"term": term,
				"type" : $('#typeCompany').val(),
				"_token": $('input[name="_token"]').val()
			},
			dataType: "json",
			type: "POST",
			success: function( data ) {

				markers = new Array(data.length);
				companies = new Array(data.length);

				if(data.length !=0 ){
					id_warsztat = $('#id_warsztat').val();
					if( $("#searched_com").hasClass('done')){
						$("#searched_com").accordion('destroy');
					}
					$('#searched_com').html('');
					for( i in data ) {

						if($('#locate_on_map').is(':checked')) {
							//dodawanie markerów
							var tmp = new google.maps.LatLng(data[i].lat, data[i].lng);
							marker = new google.maps.Marker({
								draggable: false,
								position: tmp,
								map: mapa,
								title: data[i].nazwa,
								animation: google.maps.Animation.DROP
							});
							markers[i] = marker;
							//wypełnianie danymi tablicy z firmami
							companies[i] = new Array(5);
							companies[i][0] = data[i].nazwa;
							companies[i][1] = data[i].kod + ", " + data[i].miasto + ", " + data[i].ulica;
							companies[i][2] = data[i].id;
							//dodawanie okienek
							var tmpstr = "<h5>" + companies[i][0] + "</h5>" + companies[i][1] + '</span>';
							infoWindow = new google.maps.InfoWindow({
								content: tmpstr,
								maxWidth: 260
							});
							infoWindows[i] = infoWindow;
							addMarkerWindow(marker, infoWindow, i);
						}
						$('#searched_com').append(data[i].dataText);

					}
					$('#searched_com').accordion({
						collapsible: true,
						heightStyle: "content",
						active: false
					}).addClass('done');

					if($('#locate_on_map').is(':checked')) {
						directionsDisplay.setMap(null);
					}
				}else{
					$('#searched_com').html('nie ma serwisu o podanej nazwie');
				}
			}
		});
	});



  });
  //google.maps.event.addDomListener(window, 'load', initialize);
</script>
