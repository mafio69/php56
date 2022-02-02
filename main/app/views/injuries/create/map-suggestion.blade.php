<nav class="navbar navbar-default">
    <div class="container-fluid">
        <form class="navbar-form text-center" role="search" id="nav-form" >
            <div class="row">
                    {{ Form::token() }}
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" id="distance_use"><i class="fa fa-arrows-h"></i> Zmierz odległość</button>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" class="check_group" name="range_use">
                            Zasięg warsztatów:
                        </label>
                        <input id="range" name="range" data-slider-id='rangeSlider' type="text" data-slider-min="1" data-slider-max="50" data-slider-step="1" data-slider-value="20"/>
                    </div>
                    <div class="form-group separator">
                        |
                    </div>
                    @foreach($groups as $group_id => $group_name)
                        <div class="checkbox marg-right">
                            <label>
                                <input type="radio" @if($group_id==1) checked @endif class="check_group" name="groups" value="{{$group_id}}"> {{ $group_name }}
                            </label>
                        </div>
                    @endforeach
                    <div class="checkbox marg-right">
                        <label>
                            <input type="radio" class="check_group" name="groups" value="0"> Pozostałe
                        </label>
                    </div>
                    <div class="form-group separator">
                        |
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="marg-right">Typy warsztatów: </label>
                        {{ Form::select('typeGarages[]', $typegarages, null, ['class' => 'form-control', 'id' => 'typeGarages', 'multiple'])}}
                    </div>
            </div>
            <div class="row marg-top-min">
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Wyszukaj miejsce na mapie:</label>
                    <input name="search" class="form-control input-sm" id="search" placeholder="Wyszukaj miejsce" autocomplete="off"/>
                </div>
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Obsługiwane marki osobowe:</label>
                    <input name="brands_c" class="form-control input-sm" id="brands_c" multiple ="multiple"  />
                </div>
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Autoryzacje:</label>
                    <input name="authorizations" class="form-control input-sm" id="authorizations" multiple ="multiple"  />
                </div>
                <div class="form-group col-sm-3">
                    <label  class="marg-right">Obsługiwane marki ciężarowe:</label>
                    <input name="brands_t" class="form-control input-sm" id="brands_t" multiple ="multiple "  />
                </div>
            </div>

            <div class="row marg-top-min">
                <div class="col-sm-12 text-center">
                    <div class="btn-group" data-toggle="buttons">
                        <button class="btn btn-info btn-xs active">
                            <input type="checkbox" autocomplete="off" checked name="markers[1]" class="markers"> <img src="/images/markers/blue.png" style="height:20px;padding:2px"> Obsługa samochodów osobowych
                        </button>
                        <button class="btn btn-info btn-xs active">
                            <input type="checkbox" autocomplete="off" checked name="markers[2]" class="markers"> <img src="/images/markers/red.png" style="height:20px;padding:2px"> Obsługa pojazdów ciężarowych
                        </button>
                        <button class="btn btn-info btn-xs active">
                            <input type="checkbox" autocomplete="off" checked name="markers[3]" class="markers"> <img src="/images/markers/purple.png" style="height:20px;padding:2px"> Obsługa samochodów osobowych i  pojazdów ciężarowych
                        </button>
                        <button class="btn btn-info btn-xs active">
                            <input type="checkbox" autocomplete="off" checked name="markers[4]" class="markers"> <img src="/images/markers/yellow.png" style="height:20px;padding:2px"> Niezdefiniowane typy serwisu
                        </button>
                        <button class="btn btn-info btn-xs active">
                            <input type="checkbox" autocomplete="off" checked name="markers[5]" class="markers"> <img src="/images/markers/black.png" style="height:20px;padding:2px"> Serwis zawieszony
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</nav>

<input type="hidden" name="vehicle_type" value="{{ class_basename($vehicle) }}">
<input type="hidden" name="vehicle_nip_dost" value="{{ $vehicle->nip_dost }}">
<input type="hidden" name="vehicle_name_dost" value="{{ $vehicle->name_dost }}">

<div class="map-inside-container">
    <div class="loader">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="map-content">
        <div class="row">
            <div class="col-md-12">
                <div id="map-canvas"></div>
            </div>
        </div>
    </div>
    <div class="side-panel" id="info-bar">
        <p class="bg-primary lead text-center">
            {{ $vehicle->salesProgram ? $vehicle->salesProgram->name.' ('.$vehicle->salesProgram->name_key.')' : '' }}
        </p>
        <div id="info-bar-content">

        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg " id="modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<style>
    .map-inside-container {
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
    var request_list = false;
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

    var vehicle_nip_dost = $('input[name="vehicle_nip_dost"]').val();
    var vehicle_name_dost = $('input[name="vehicle_name_dost"]').val();

    var sortBy = (function () {
        var toString = Object.prototype.toString,
            // default parser function
            parse = function (x) { return x; },
            // gets the item to be sorted
            getItem = function (x) {
                var isObject = x != null && typeof x === "object";
                var isProp = isObject && this.prop in x;
                return this.parser(isProp ? x[this.prop] : x);
            };

        /**
         * Sorts an array of elements.
         *
         * @param  {Array} array: the collection to sort
         * @param  {Object} cfg: the configuration options
         * @property {String}   cfg.prop: property name (if it is an Array of objects)
         * @property {Boolean}  cfg.desc: determines whether the sort is descending
         * @property {Function} cfg.parser: function to parse the items to expected type
         * @return {Array}
         */
        return function sortby (array, cfg) {
            if (!(array instanceof Array && array.length)) return [];
            if (toString.call(cfg) !== "[object Object]") cfg = {};
            if (typeof cfg.parser !== "function") cfg.parser = parse;
            cfg.desc = !!cfg.desc ? -1 : 1;
            return array.sort(function (a, b) {
                a = getItem.call(cfg, a);
                b = getItem.call(cfg, b);
                return cfg.desc * (a < b ? -1 : +(a > b));
            });
        };

    }());

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
    }

    function drawMarker(item) {
        var available = false;
        $.each(item.branchPlanGroups, function(k, branchPlanGroup){
            var require_name = true;
            $.each(branchPlanGroup.branch_brands, function(l, branch_brand){
                if(! branch_brand.pivot.if_sold ){
                    require_name = false;
                }
            });

            if(
                branchPlanGroup.plan_group !== null &&
                (
                    branchPlanGroup.plan_group.company_groups === undefined
                    ||
                    branchPlanGroup.plan_group.company_groups.length === 0
                )
                &&
                (
                    (require_name && vehicle_nip_dost == item.nip)
                    ||
                    !require_name
                )
            ){
                available = true;
            }
        });

        if(available) {
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
                        boundable = false;
                        if (infowindow) infowindow.close();
                        infowindow = new google.maps.InfoWindow({
                            content: '<h4>' + item.short_name + '</h4><p>serwis: ' + item.company_name + '</p><p>adres warsztatu: ' + item.address + '</p>'
                        });
                        infowindow.open(map, markers[item.id]);     //było item.id
                        showDetails(item.id) // wyświetlanie szczegółów po kliknięciu markera
                        setTimeout(function (){
                            boundable = true;
                        }, 300);
                    }
                });
            }

            markers[item.id].addListener('click', function (e) {    //było item.id
                addToDistanceMarker(e, markers[item.id]);       //było item.id
            });

            lastlat = item.lat;
            lastlng = item.lng;
            drawArea(item);
            markers[item.id].setVisible(false);
        }
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
            });
        }
    }

    function initAutocomplete() {

        // Create the search box and link it to the UI element.
        var input = document.getElementById('search');
        var searchBox = new google.maps.places.SearchBox(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function () {
            if(boundable) {
                boundable = false;
                console.log('bounds changed');
                reload()
            }
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
                gestureHandling: 'greedy',
                styles: [
                    {
                        "featureType": "administrative",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    }
                ]
            };
            map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);

            poly = new google.maps.Polyline({
                strokeColor: '#000000',
                strokeOpacity: 1.0,
                strokeWeight: 3,
            });

            poly.setMap(map);

            // // Zliczanie widocznych markerów po ustabilizowaniu mapy
            // map.addListener('idle', function () {
            //     // Tylko, jeśli okno szczegółów zamknięte
            //     if (dontUpdateVisible == 0) {
            //         console.log('fier here');
            //         getVisibleMarkers();
            //     }
            // });

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
            reload();
        }
    }


    function getVisibleMarkers(plan_group_id = false) {
        dontUpdateVisible = 1;//

        definedMarkers = [];
        visibleMarkers = [];
        $.each(markers, function (i, item) {
            if (item !== undefined) {
                item.setVisible(true);
                definedMarkers.push(i);
            }
        })

        for (var i = 0; i < definedMarkers.length; i++) {
            console.log('marker', i, markers[definedMarkers[i]].getPosition().lat(), markers[definedMarkers[i]].getPosition().lng());
            if (map.getBounds().contains(markers[definedMarkers[i]].getPosition())) {
                visibleMarkers.push(definedMarkers[i]);
            }
        }
        console.log('getVisibleMarkers', plan_groups, definedMarkers, visibleMarkers,markers);

        if(plan_group_id){
            renderPlanGroups(plan_group_id)
        }
        dontUpdateVisible = 0;//
    }

    // TODO: Aktualizuj listę równiez po zmianie kategorii

    function showDetails(i) {
        boundable = false;
        if(isset(markers[i])) {
            markers[i].setVisible(true);
        }
        dontUpdateVisible = 1;

        if (infowindow) infowindow.close(); // Usuń otwarte okno aby wyświetlane było tylko 1
        $('#modal-lg').modal('hide');
        infowindow = new google.maps.InfoWindow({
            content: '<h4>' + branches[i].short_name + '</h4><p>serwis: ' + branches[i].company_name + '</p><p>adres warsztatu: ' + branches[i].address + '</p>'
        });
        infowindow.open(map, markers[branches[i].id]);

        var infoBar = document.getElementById('info-bar-content');
        var content = "<div class='details-container pre-scrollable'>" +
            "<form method='post' action='/injuries/make/create-new-entity'>";
        if($('input[name="vehicle_type"]').val() == 'stdClass'){
            content += "<input name=\"vehicle_id\" type=\"hidden\" value='{{ $vehicle->id }}'>";
            content += "<input name=\"contract_id\" type=\"hidden\" value='{{ $contract_id }}'>";
            content += "<input name=\"contract_internal_agreement_id\" type=\"hidden\" value='{{ $contract_internal_agreement_id }}'>";
            content += "<input name=\"policy_id\" type=\"hidden\" value='{{ $policy_id }}'>";
        }else {
            content += "<input name=\"vmanage_vehicle_id\" type=\"hidden\" value='{{ $vehicle->id }}'>";
        }
        content += "<input type='hidden' name='_token' value='{{ csrf_token() }}'/>" +
            "<input type='hidden' name='branch_id' value='"+branches[i].id+"'/>" +
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
            // "<p >Od:<span class='form-control-static editable' data-field='open_time' data-type='time'> " + branches[i].open_time.substring(0, 5) + "</span></p>" +
            // "<p >Do:<span class='form-control-static editable'  data-field='close_time' data-type='time'> " + branches[i].close_time.substring(0, 5) + "</span></p></div>" +
            "<p >Od:<span class='form-control-static editable' data-field='open_time' data-type='time' style='margin-right: 20px'> " + branches[i].open_time.substring(0, 5) + "</span>" +
            "Do:<span class='form-control-static editable'  data-field='close_time' data-type='time'> " + branches[i].close_time.substring(0, 5) + "</span></p></div>" +

            "<div><label>Uwagi:</label><p class='form-control-static editable' data-field='remarks'>" + branches[i].remarks + "</p></div>" +

            "<div><label>Kierowalność/priorytety:</label><p class='form-control-static editable' data-field='priorities'>" + branches[i].priorities + "</p></div>" +
            "<hr>" +
            "<span class='btn btn-default pull-left' onclick='reload()'><i class='fa fa-fw fa-arrow-left'></i>Powrót</span>"+
            "<button type='submit' class='btn btn-primary pull-right'>Przypisz serwis</span>"+
            "</form>"+
            "</div>";
            infoBar.innerHTML = content;
        setTimeout(function (){
            boundable = true;
        }, 300);
    }

    var source;
    var boundable = true;
    var plan_groups = [];

    function renderPlanGroups(plan_group_id = false)
    {
        console.log( 'render plan groups', plan_group_id, plan_groups, visibleMarkers);
        if(plan_groups.length == 0){
            $("#info-bar-content").html("<p class='text-center'>Brak serwisów na wskazanym obszarze</p>");
            $("#info-bar-content").append('<span class="btn btn-block btn-default text-center search-branch"><i class="fa fa-search fa-fw"></i> wyszukaj serwis</span>');

            $('.search-branch').on('click', function(){
                $.ajax({
                    type: "GET",
                    url: '/injuries/make/branch-search',
                    data: $('#nav-form').serialize() + '&brands_c=' + $('#brands_c').val() + '&brands_t=' + $('#brands_t').val() + '&authorizations=' + $('#authorizations').val() + '&vehicle_id={{ $vehicle->id }}'+'&vehicle_type={{ $vehicle_type }}',
                    assync: false,
                    cache: false,
                    success: function (data) {
                        $('#modal-lg .modal-content').html(data);
                        $('#modal-lg').modal('show');
                    },
                    dataType: 'html'
                });
            });
        }else {
            $("#info-bar-content").html('<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"></div>');

            $.each(plan_groups, function (i, plan_group) {
                $("#info-bar-content .panel-group").append(
                    '<div class="panel panel-default" data-sort="' + plan_group.ordering + '">\n' +
                    '      <div class="panel-heading" data-group="' + plan_group.id + '" >\n' +
                    '        <h4 class="panel-title pointer">\n' +
                    plan_group.name +
                    '        </h4>\n' +
                    '      </div>\n' +
                    '      <div class="list-group" id="listGroup' + plan_group.id + '">\n' +
                    '      </div>\n' +
                    '    </div>');
            })

            if (plan_group_id) {
                let panel_content = '';
                console.log('markers', visibleMarkers)
                $.each(visibleMarkers, function (i, item) {
                    $.each(branches[visibleMarkers[i]].branchPlanGroups, function (k, branchPlanGroup) {
                        var showing = false;
                        var if_sold = false;
                        var if_seller = false;

                        $.each(branchPlanGroup.branch_brands, function (l, branch_brand) {
                            if (!branch_brand.pivot.if_sold) {
                                showing = true;
                            } else {
                                showing = true;
                                if_sold = true;
                            }

                            if (vehicle_nip_dost == branches[visibleMarkers[i]].nip) {
                                if_seller = true;
                            }
                        });
                        console.log(showing, plan_group_id, parseInt(branchPlanGroup.plan_group_id) , parseInt(plan_group_id))
                        if (showing && plan_group_id && parseInt(branchPlanGroup.plan_group_id) == parseInt(plan_group_id)) {
                            panel_content +=
                                "<li class=\"list-group-item " + (if_seller ? "list-group-item-info" : "") + " \" onclick='showDetails(" +
                                branches[visibleMarkers[i]].id + ")' data-branch='" + branches[visibleMarkers[i]].id + "'>" +
                                (
                                    if_seller
                                        ?
                                        "<i class=\"fa fa-dollar fa-fw\"></i>" : ""
                                ) +
                                branches[visibleMarkers[i]].company_name +
                                "<span class=\"marg-left label label-info\">" + branches[visibleMarkers[i]].address + "</span>" +
                                "</li>";
                        }
                    });
                });

                $("#listGroup" + plan_group_id).append(panel_content);
            }
        }
    }

    function reload() {
        if (typeof map === 'object' && typeof map.getBounds() === 'object') {
            if (request) {
                request.abort();
            }

            let bounds = map.getBounds()
            let ne = bounds.getNorthEast(); // LatLng of the north-east corner
            let sw = bounds.getSouthWest(); // LatLng of the south-west corder

            request = $.ajax({
                url: "{{ url('companies/list-map-groups') }}",
                data: $('#nav-form').serialize() + '&sw_lat='+ sw.lat() + '&sw_lng='+ sw.lng() + '&ne_lat='+ ne.lat() + '&ne_lng='+ ne.lng() +'&brands_c=' + $('#brands_c').val() + '&brands_t=' + $('#brands_t').val() + '&authorizations=' + $('#authorizations').val() + '&vehicle_id={{ $vehicle->id }}' + '&vehicle_type={{ $vehicle_type }}' + '&contract_id={{ $contract_id }}',
                dataType: "json",
                type: "POST",
                beforeSend: function () {
                },
                success: function (data) {
                    cleanMap();
                    setTimeout(function (){
                        boundable = true;
                    }, 100);
                    plan_groups = data;
                    renderPlanGroups();
                }
            });
        }else{
            setTimeout(function (){
                reload();
            }, 1000);
        }

    }

    if (init != true) {
        initialize(),
        initAutocomplete()
    }

    $('#info-bar-content').on('click', '.panel-heading', function(){
        if (request_list) {
            request_list.abort();
        }


        let plan_group_id = $(this).data('group');

        let bounds = map.getBounds()
        let ne = bounds.getNorthEast(); // LatLng of the north-east corner
        let sw = bounds.getSouthWest(); // LatLng of the south-west corder

        console.log('heading', bounds, ne.lat(), ne.lng(), sw.lat(), sw.lng());

        request_list = $.ajax({
            url: "{{ URL::action('CompaniesController@postListMapGarages') }}",
            data: $('#nav-form').serialize() + '&plan_group_id=' + plan_group_id + '&brands_c=' + $('#brands_c').val() + '&brands_t=' + $('#brands_t').val() + '&authorizations=' + $('#authorizations').val() + '&vehicle_id={{ $vehicle->id }}'+'&vehicle_type={{ $vehicle_type }}' + '&contract_id={{ $contract_id }}&sw_lat='+ sw.lat() + '&sw_lng='+ sw.lng() + '&ne_lat='+ ne.lat() + '&ne_lng='+ ne.lng(),
            dataType: "json",
            type: "POST",
            beforeSend: function() {
                boundable = false;
                $('.loader').show();
            },
            success: function (data) {
                cleanMap();
                source  = data;
                $.each(data, function (i, item) {
                    branches[item.id] = item;
                    drawMarker(item);
                });
                getVisibleMarkers(plan_group_id);
                $('.loader').hide();
                setTimeout(function(){
                    boundable = true;
                }, 500)
            }
        });
    });

    $(document).ready(function () {
        $('.page-header').css('margin', 0).css('border', 'none');
        $('#search_btn').click(function () {
            searchPoint();
        });
        $('#distance_use').click(function () {
            $(this).toggleClass('btn-default').toggleClass('btn-success');
            $(this).toggleClass('active');
            if (!$(this).hasClass('active'))
                clearDistance();
            return false;
        });
        $('#search').keyup(function (e) {
            if (e.keyCode == 13) {
                var temp_this = $('#distance_use');
                $(temp_this).toggleClass('btn-default').toggleClass('btn-success');
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
            $('input.markers:checked').parent().addClass('btn-info').removeClass('btn-default');

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
