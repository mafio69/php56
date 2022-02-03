@extends('layouts.main')


@section('main')

    @include('companies.map.nav')

{{--    --}}
{{--    <div class="flash-message"></div>--}}
{{----}}
    <div class="loader">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>

    <div class="map-container">
        <div class="map-content">
            <div class="row">
                <div class="col-md-12">
                    <div id="map-canvas"></div>
                </div>
            </div>
        </div>
        <div class="side-panel" id="info-bar">
            <ul class="list-group  pre-scrollable" id="info-ul">
            </ul>
        </div>
    </div>

    <style>
        .map-container {
            display: table;
            width: 100%;
        }

        .map-content, .side-panel {
            display: table-cell;
        }

        .side-panel {
            /*-ms-flex: 0 0 230px;*/
            /*flex: 0 0 230px;*/
            width: 400px;
        }

        .pre-scrollable {
            max-height: none;
            /*overflow-y: scroll;*/
            height: 700px;
        }

        .details-container {
            border: 1px solid #ddd;
            border-radius: 2px;
            /*margin: 0px 5px;*/
            padding: 10px;
        }

        .list-group-item {
            cursor: pointer;
        }

        .loader{
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            display: none;
            font-size: 42px;
        }
        .loader:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .2);
        }
    </style>



@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        var geocoder;
        var map;
        var infowindow = new google.maps.InfoWindow();
        var branches = [];
        var markers = [];
        var startPoint = null;
        var endPoint = null;
        var infowindow;
        var myLatlng;
        var imageIdea = '/images/blue_MarkerI.png';
        var imageOther = '/images/orange_MarkerO.png';
        var radius = 20000;
        var branchCircles = [];
        var marker_special = [];
        var infowindow_distance = null;
        var markers_search = [];

        var origin2 = 'Poznań, Polska';
        var destinationA = 'Stockholm, Sweden';
        var distance_matrix = new google.maps.DistanceMatrixService;
        var request = false;
        var lastlat = 0;
        var lastlng = 0;
        var lastid = 0;
        var counter = 0;
        var markerCluster;
        var init = false;

        // było item.id
        var markersCounter = 0;
        var definedMarkers = [];
        var visibleMarkers = [];
        var dontUpdateVisible = 0;

        function drawArea(item) {
            if (branchCircles[item.id] != null) {   //było item.id
                branchCircles[item.id].setMap(null);    //było item.id
            }
            if ($('[name="range_use"]').is(':checked')) {
                myLatlng = new google.maps.LatLng(item.lat, item.lng);
                var areaOptions = {
                    strokeColor: '#337ab7',
                    strokeOpacity: 0.7,
                    strokeWeight: 2,
                    fillColor: '#337ab7',
                    fillOpacity: 0.30,
                    map: map,
                    center: myLatlng,
                    radius: radius
                };
                branchCircles[item.id] = new google.maps.Circle(areaOptions);   //było item.id
            }

            //
            // markersCounter++;
        }

        function drawMarker(item) {
            if (item.lat == lastlat && item.lng == lastlng) {
                counter++;
                myLatlng = new google.maps.LatLng(item.lat + counter / 8000, item.lng + counter / 8000);
            } else {
                counter = 0;
                myLatlng = new google.maps.LatLng(item.lat, item.lng);
            }
            markers[item.id] = new google.maps.Marker({     //było item.id
                position: myLatlng,
                map: map,
                title: item.short_name,
                icon: item.marker,
            });

            if (item) {
                google.maps.event.addListener(markers[item.id], 'click', function () {      //było item.id
                    if (!$('#distance_use').hasClass('active')) {
                        if (infowindow) infowindow.close();
                                @if(Auth::user()->can('mapa_serwisow#edycja'))
                        var btnEdit = '<span target="{{ URL::to("company/garages/edit-modal") }}/' + item.id + '" class="btn btn-primary btn-xs modal-open-lg-special" data-toggle="modal" data-target="#modal-lg"><i class="fa fa-edit fa-fw"></i> edytuj</span>';    //było item.id
                                @else
                        var btnEdit = '';
                        @endif
                            infowindow = new google.maps.InfoWindow({
                            // content: '<h4>' + item.short_name + '</h4><p>serwis: ' + item.company_name + '</p><p>adres warsztatu: ' + item.address + '</p><p>' + item.email + '</p><p>' + item.phone + '</p><h5>Obsługiwane marki</h5><p>Osobowe: ' + item.brands[1] + '</p><p>Ciężarowe: ' + item.brands[2] + '</p><h5>Ilość aut zastępczych</h5><p>' + item.typevehicles + '</p><h5>Uwagi</h5><p>' + item.remarks + '</p>' + btnEdit
                            content: '<h4>' + item.short_name + '</h4><p>serwis: ' + item.company_name + '</p><p>adres warsztatu: ' + item.address + '</p>'
                        });
                        infowindow.open(map, markers[item.id]);     //było item.id
                        showDetails(item.id) // wyświetlanie szczegółów po kliknięciu markera
                    }
                });
            }

            markers[item.id].addListener('click', function (e) {    //było item.id
                addToDistanceMarker(e, markers[item.id]);       //było item.id
            });

            lastlat = item.lat;
            lastlng = item.lng;

            drawArea(item);

        }

        function cleanMap() {
            if (infowindow) infowindow.close();

            branches.forEach(function (branch) {
                if (branchCircles[branch.id] != null) {
                    branchCircles[branch.id].setMap(null);
                    delete branchCircles[branch.id];
                }
                if (markers[branch.id] != null) {
                    markers[branch.id].setMap(null);
                    delete markers[branch.id];
                }
                if (branches[branch.id] != null) {
                    delete branches[branch.id];
                }
            });
        }

        function addToDistanceMarker(event, marker) {
            if ($('#distance_use').hasClass('active')) {
                var path = poly.getPath();

                if (startPoint == null) {
                    startPoint = event.latLng;
                } else if (endPoint == null) {
                    endPoint = event.latLng;
                } else {
                    clearDistance();
                    startPoint = event.latLng;
                    endPoint = null;
                }

                path.push(event.latLng);

                marker_special[path.getLength()] = [];
                marker_special[path.getLength()]['type'] = 'marker';
                marker_special[path.getLength()]['obj'] = marker;

                getDistance();
            }
        }

        function addToDistancePoint(event) {
            if ($('#distance_use').hasClass('active')) {
                var path = poly.getPath();

                if (startPoint == null) {
                    startPoint = event.latLng;
                } else if (endPoint == null) {
                    endPoint = event.latLng;
                } else {
                    clearDistance();
                    startPoint = event.latLng;
                    endPoint = null;
                }

                path.push(event.latLng);

                getDistance();

                marker_special[path.getLength()] = [];
                marker_special[path.getLength()]['type'] = 'marker_special';
                marker_special[path.getLength()]['obj'] = new google.maps.Marker({
                    position: event.latLng,
                    title: '#' + path.getLength(),
                    map: map
                });
            }

        }

        function clearDistance() {
            var path = poly.getPath();
            path.pop();
            path.pop();
            for (var i in marker_special) {
                if (marker_special[i]['type'] != 'marker')
                    marker_special[i]['obj'].setMap(null);
            }
            marker_special = [];
        }

        function getDistance() {
            if (startPoint != null && endPoint != null) {
                var path = poly.getPath();
                distance_matrix.getDistanceMatrix({
                    origins: [startPoint],
                    destinations: [endPoint],
                    travelMode: 'DRIVING',
                    unitSystem: google.maps.UnitSystem.METRIC,
                    avoidHighways: false,
                    avoidTolls: false
                }, function (response, status) {
                    infowindow_distance = new google.maps.InfoWindow({
                        content: '<h4>Odległość</h4><p>' + response.rows[0].elements[0].distance.text + '</p>'
                    });

                    infowindow_distance.open(map, marker_special[path.getLength()]['obj']);
                    //console.log(response.rows[0].elements[0].distance.text);
                });
            }
        }

        function initAutocomplete() {

            // Create the search box and link it to the UI element.
            var input = document.getElementById('search');
            var searchBox = new google.maps.places.SearchBox(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function () {
                searchBox.setBounds(map.getBounds());
            });

            var markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function () {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers_search.forEach(function (marker) {
                    marker.setMap(null);
                });
                markers_search = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function (place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    var icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    markers_search.push(new google.maps.Marker({
                        map: map,
                        icon: icon,
                        title: place.name,
                        position: place.geometry.location
                    }));

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });

                map.fitBounds(bounds);
                map.setZoom(9);
            });
        }

        function initialize() {
            if (init != true) {
                init = true;
                geocoder = new google.maps.Geocoder();
                var mapOptions = {
                    center: {lat: 52.406374, lng: 17.566540},
                    zoom: 7,
                    gestureHandling: 'greedy'
                };
                map = new google.maps.Map(document.getElementById('map-canvas'),
                    mapOptions);

                poly = new google.maps.Polyline({
                    strokeColor: '#000000',
                    strokeOpacity: 1.0,
                    strokeWeight: 3,
                });

                poly.setMap(map);

                // Zliczanie widocznych markerów po ustabilizowaniu mapy
                map.addListener('idle', function () {

                    // Tylko, jeśli okno szczegółów zamknięte
                    if (dontUpdateVisible == 0) {
                        getVisibleMarkers();
                    }
                });

                map.addListener('click', addToDistancePoint);
                /*  map.addListener("idle", function() {
                      if (map.getZoom() > 9) {
                        markerCluster = new MarkerClusterer(map, markers, options);
                      }
                      else {
                         markerCluster.clearMarkers();
                      }

                   });*/
                //if(request==false)
                $('.check_group').change();
                var options = {
                    imagePath: '/images/markers/m',
                    minimumClusterSize: 5
                };
            }
        }


        function getVisibleMarkers() {
            dontUpdateVisible = 0;//

            definedMarkers = [];
            visibleMarkers = [];

            for (var i = 0; i < markers.length; i++) {
                if (markers[i] !== undefined) {
                    definedMarkers.push(i);
                }
            }

            for (var i = 0; i < definedMarkers.length; i++) {
                if (map.getBounds().contains(markers[definedMarkers[i]].getPosition())) {
                    visibleMarkers.push(definedMarkers[i]);
                }
            }

            var infoBar = document.getElementById("info-bar");
            infoBar.innerHTML = "<ul class=\"list-group  pre-scrollable\" id=\"info-ul\"></ul>";
            var infoUl = document.getElementById(id = "info-ul");
            if (visibleMarkers.length == 0) {
                infoUl.innerHTML += "<li>Brak serwisów na wskazanym obszarze</li>";
            } else {
                for (var i = 0; i < visibleMarkers.length; i++) {
                    infoUl.innerHTML += "<li class=\"list-group-item\" onclick='showDetails(" + branches[visibleMarkers[i]].id + ")'>" + branches[visibleMarkers[i]].company_name + "</li>";
                }
            }
        }

        // TODO: Aktualizuj listę równiez po zmianie kategorii

        function showDetails(i) {
            dontUpdateVisible = 1;

            if (infowindow) infowindow.close(); // Usuń otwarte okno aby wyświetlane było tylko 1
            infowindow = new google.maps.InfoWindow({
                content: '<h4>' + branches[i].short_name + '</h4><p>serwis: ' + branches[i].company_name + '</p><p>adres warsztatu: ' + branches[i].address + '</p>'
            });
            infowindow.open(map, markers[branches[i].id]);

            var infoBar = document.getElementById('info-bar');
            infoBar.innerHTML = "<div class='details-container pre-scrollable'>" +
                "<form id='branch-form'>" +
                "<input type='hidden' name='_token' value='{{ csrf_token() }}'/>" +
                "<h4 class='text-center'>" + branches[i].company_name + "</h4>" +
                "<p>" + branches[i].address + "</p>" +
                "<p>" + branches[i].email + "</p>" +
                "<p>" + branches[i].phone + "</p>" +

                "<div>"+
                    "<label>Osoby kontaktowe</label>" +
                    "<p class='form-control-static editable' data-field='contact_people'>" + branches[i].contact_people + "</p>" +
                "</div>"+
                "<div><label>Obsługiwane marki:</label>" +
                "<p >Osobowe: <span class='form-control-static'>" + branches[i].brands[1] + "</span></p>" +
                "<p >Ciężarowe: <span class='form-control-static'>" + branches[i].brands[2] + "</span></p>" +
                "<p >Dostawcze: <span class='form-control-static editable' data-field='delivery_cars'>" + branches[i].delivery_cars + "</span></p></div>" +
                "<div><label>Posiadana autoryzacja: </label><p class='form-control-static'>" + branches[i].authorizations + "</p></div>" +

                "<div><label>Ilość aut zastępczych: </label><p class='form-control-static'>" + branches[i].typevehicles + "</p></div>" +
                "<br>" +
                "<div><label>Holownik: </label><p class='form-control-static editable' data-field='tug_remarks'>" + branches[i].tug_remarks + "</p></div>" +
                "<div><label>Godziny pracy:</label>" +
                // "<p >Od:<span class='form-control-static editable' data-field='open_time' data-type='time'> " + branches[i].open_time.substring(0, 5) + "</span>" +
                // "Do:<span class='form-control-static editable'  data-field='close_time' data-type='time'> " + branches[i].close_time.substring(0, 5) + "</span></p></div>" +
                "<p >Od:<span class='form-control-static editable' data-field='open_time' data-type='time' style='margin-right: 20px'> " + branches[i].open_time.substring(0, 5) + "</span>" +
                "Do:<span class='form-control-static editable'  data-field='close_time' data-type='time'> " + branches[i].close_time.substring(0, 5) + "</span></p></div>" +

                "<div><label>Uwagi:</label><p class='form-control-static editable' data-field='remarks'>" + branches[i].remarks + "</p></div>" +

                "<div><label>Kierowalność/priorytety:</label><p class='form-control-static editable' data-field='priorities'>" + branches[i].priorities + "</p></div>" +
                "<hr>" +
                "<span class='btn btn-default pull-left' onclick='getVisibleMarkers()'><i class='fa fa-fw fa-arrow-left'></i>Powrót</span>"+
                "<span class='btn btn-primary pull-right btn-edit' onclick='editBranch()'><i class='fa fa-fw fa-pencil'></i>Edytuj</span>" +
                "<span class='btn btn-primary pull-right btn-update' onclick='updateBranch("+i+")' style='display:none;'><i class='fa fa-fw fa-pencil'></i>Zapisz</span>" +
                "</form>"+
                "</div>"
            ;
        }

        function editBranch() {
            $('#info-bar .form-control-static.editable').each(function(){
                var field = $(this).data('field');
                var value = $(this).text();
                var type = $(this).data('type');
                if(type === undefined) type = 'text';
                $(this).hide().after('<input type="'+type+'" class="form-control input-sm" name="'+field+'" value="' + value + '">');
            });

            $('.btn-edit').hide();
            $('.btn-update').show();
        }

        function updateBranch(i)
        {
            $.ajax({
                url: "{{ URL::action('CompaniesController@postUpdateBranchFields') }}/" + i,
                data: $('#branch-form').serialize(),
                dataType: "json",
                type: "POST",
                success: function (data) {
                    $.each(data, function(k, el){
                        if(branches[i][k]){
                            branches[i][k] = el;
                        }
                    });

                    $("#response-alert-info").html("Zapisano zmiany w danych serwisu.").fadeIn(300).delay(3000).fadeOut(300, function(){
                        $(this).html('');
                    });
                }
            });
        }

        function reload() {
            if (typeof map === 'object' && typeof map.getBounds() === 'object') {
                if (request != false) {
                    request.abort();
                }

                let bounds = map.getBounds()
                let ne = bounds.getNorthEast(); // LatLng of the north-east corner
                let sw = bounds.getSouthWest(); // LatLng of the south-west corder

                request = $.ajax({
                    url: "{{ URL::action('CompaniesController@postListMapGarages') }}",
                    data: $('#nav-form').serialize() + '&brands_c=' + $('#brands_c').val() + '&brands_t=' + $('#brands_t').val() + '&authorizations=' + $('#authorizations').val(),
                    dataType: "json",
                    type: "POST",
                    beforeSend: function() {
                        $('.loader').show();
                    },
                    success: function (data) {
                        cleanMap();

                        $.each(data, function (i, item) {
                            branches[item.id] = item;
                            drawMarker(item);
                        });

                        getVisibleMarkers();

                        $('.loader').hide();
                    }
                });
            }else{
                setTimeout(function (){
                    reload();
                }, 1000);
            }
        }

        google.maps.event.addDomListener(window, 'load', function () {
            if (init != true) {
                initialize(),
                initAutocomplete()
            }
        });

        $(document).ready(function () {
            $('.page-header').css('margin', 0).css('border', 'none');
            $('#search_btn').click(function () {
                searchPoint();
            });
            $('#distance_use').click(function () {
                $(this).toggleClass('btn-defalut').toggleClass('btn-success');
                $(this).toggleClass('active');
                if (!$(this).hasClass('active'))
                    clearDistance();
                return false;
            });
            $('#search').keyup(function (e) {
                if (e.keyCode == 13) {
                    var temp_this = $('#distance_use');
                    $(temp_this).toggleClass('btn-defalut').toggleClass('btn-success');
                    $(temp_this).toggleClass('active');
                    if (!$(temp_this).hasClass('active'))
                        clearDistance();
                    return false;
                }
            });
            $('#range').bootstrapSlider({
                formatter: function (value) {
                    return 'Zasięg km: ' + value;
                }
            }).on('slide', function (e) {
                radius = e.value * 1000;
                branches.forEach(function (branch) {
                    drawArea(branch);
                });
            }).on('change', function (e) {
                radius = e.value.newValue * 1000;
                branches.forEach(function (branch) {
                    drawArea(branch);
                });
            });
            $('.markers').on('change', function (e) {
                $('.markers').parents('button').removeClass('btn-info')
                $('.markers').parents('button').addClass('btn-default');
                $('input.markers:checked').parent().addClass('btn-info').removeClass('btn-defalut');

            });
            $('.check_group, #typeGarages, #brands_c, #brands_t, #authorizations, .markers').on('change', function (e) {
                reload();
            });

            $('#typeGarages').multiselect({
                buttonText: function (options) {
                    if (options.length === 0) {
                        return 'Filtruj typy serwisów | domyślnie wszystkie serwisy <b class="caret"></b>';
                    } else {
                        var count = 0;
                        var selected = '';
                        options.each(function () {
                            selected += $(this).text() + ', ';
                            count++;
                        });
                        if (count > 2)
                            return 'wybrano ' + count + ' typy <b class="caret"></b>';
                        else
                            return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
                    }
                }
            });

            $('#brands_c').select2({
                placeholder: "Wybierz obsługiwane marki samochodów | domyślnie wyświetlane wszystkie serwisy",
                minimumInputLength: 2,
                multiple: true,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "{{ URL::action('CompaniesController@getBrandsList', [1]) }}",
                    dataType: 'json',
                    type: "GET",
                    data: function (term, page) {
                        return {
                            q: term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                    },
                    results: function (data) {

                        return {results: data};
                    }

                }
            });

            $('#brands_t').select2({
                placeholder: "Wybierz obsługiwane marki samochodów | domyślnie wyświetlane wszystkie serwisy",
                minimumInputLength: 2,
                multiple: true,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "{{ URL::action('CompaniesController@getBrandsList', [2]) }}",
                    dataType: 'json',
                    type: "GET",
                    data: function (term, page) {
                        return {
                            q: term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                    },
                    results: function (data) {
                        return {results: data};
                    }
                }
            });

            //
            $('#authorizations').select2({
                placeholder: "Wybierz autoryzowane marki samochodów | domyślnie wyświetlane wszystkie serwisy",
                minimumInputLength: 2,
                multiple: true,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: "{{ URL::action('CompaniesController@getBrandsList', [1]) }}",
                    dataType: 'json',
                    type: "GET",
                    data: function (term, page) {
                        return {
                            q: term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                    },
                    results: function (data) {

                        return {results: data};
                    }

                }
            });

        });

        $('#page-wrapper').on('click', '.modal-open-lg-special', function () {
            var hrf = $(this).attr('target');
            $.get(hrf, function (data) {
                $('#modal-lg .modal-content').html(data);
            });
        });
        $('#modal-lg').on('click', '#set-spec', function () {
            var $btn = $(this).button('loading');
            if ($('#dialog-form').valid()) {
                $.ajax({
                    type: "POST",
                    url: $('#dialog-form').prop('action'),
                    data: $('#dialog-form').serialize(),
                    assync: false,
                    cache: false,
                    success: function (data) {
                        if (data.code == '0') {
                            $('#modal-lg').modal("hide");
                            reload();
                        } else if (data.code == '1') self.location = data.url;
                        else {
                            $('#modal-lg .modal-body').html(data.error);
                            if (isset(data.url) && data.url != '') {
                                $btn.button('reset');
                                $('#modal-lg').on('hidden.bs.modal', function (e) {
                                    self.location = data.url;
                                });
                            }
                        }
                    },
                    dataType: 'json'
                });
            } else {
                $btn.button('reset');
            }
            return false;
        });
    </script>
@stop
