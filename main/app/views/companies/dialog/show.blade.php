<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Lokalizacja serwisu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    {{ Form::hidden('lat', $branch->lat) }}
    {{ Form::hidden('lng', $branch->lng) }}
    <div id="map-canvas" style="width:100%; height:300px;  "></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>

<script type="text/javascript">
    var mapa;
    var geocoder = new google.maps.Geocoder();
    var marker ;
    var infowindow = new google.maps.InfoWindow();

    function initialize(slat,slng) {

        var myOptions = {
            zoom: 7,
            scrollwheel: true,
            navigationControl: false,
            mapTypeControl: false,
            center: new google.maps.LatLng(52.528846,17.071874),
        };

        mapa = new google.maps.Map(document.getElementById('map-canvas'), myOptions);

        if(slat != ''  && slng != ''){

            latlng = new google.maps.LatLng(slat,slng);
            mapa.panTo(latlng);
            mapa.setZoom(16);
            placeMarker(latlng);

        }

    };

    function placeMarker(location) {


        marker = new google.maps.Marker({
            position: location,
            draggable:false,
            map: mapa
        });



    }


    $(document).ready(function() {
        var lat = $('input[name="lat"]').val();
        var lng = $('input[name="lng"]').val();
        setTimeout(function(){
            initialize(lat, lng);
        }, 500);
    });

</script>