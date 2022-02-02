@extends('layouts.main')

@section('header')
    Dodawanie nowego warsztatu do firmy <i>{{$company->name}}</i><br/>
@stop

@section('main')
    <form action="{{ URL::to('company/garages/store', array($company->id) ) }}" method="post" role="form">
        <div class="row marg-btm">
            <div class="pull-right">
                <a href="{{ URL::to('company/garages/index', array($company->id) ) }}"
                   class="btn btn-default">Anuluj</a>
                {{ Form::submit('Dodaj warsztat',  array('class' => 'btn btn-primary'))  }}
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
                <label>Adres:
                    <button type="button" class="btn btn-primary btn-xs" id="cp_company"> kopiuj z danych firmy</button>
                </label>
                <div class="row">
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('city', '', array('class' => 'form-control required', 'id' => 'city', 'placeholder' => 'miasto'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('code', '', array('class' => 'form-control  required', 'id' => 'code', 'placeholder' => 'kod pocztowy'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('street', '', array('class' => 'form-control required ', 'id' => 'street', 'placeholder' => 'ulica'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::select('voivodeship_id', $voivodeships, null, ['class' => 'form-control', 'id' => 'voivodeship_id']) }}
                    </div>
                </div>
                <div class="checkbox pull-right" style="z-index:100">
                    <label>
                        <input type="checkbox" name="suspended"> Zawieszony
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="mapShow" name="if_map"> Zlokalizuj na mapie
                    </label>
                </div>
                <div class="checkbox" style="display:none;">
                    <label>
                        <input type="checkbox" id="correctMap" name="if_map_correct"> Skoryguj niezależnie pozycję
                    </label>
                </div>

                <div id="map-canvas" style="width:100%; height:400px; display:none; "></div>
            </div>
            <div class="form-group">
                <label>Dane warsztatu:</label>
                <div class="row">
                    <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="form-goup">
                            <label>Nazwa skrócona:</label>  
                            {{ Form::text('short_name', '', array('class' => 'form-control  tips', 'placeholder' => 'nazwa skrócona', 'title' => 'nazwa skrócona'))  }}
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 marg-btm tips" title="telefon">
                        <div class="form-goup">
                            <label>Telefon:</label>
                            @if($company->type == 1)
                                {{ Form::text('phone', '', array('class' => 'form-control   required', 'placeholder' => 'telefon' ))  }}
                            @else
                                {{ Form::text('phone', '', array('class' => 'form-control   ', 'placeholder' => 'telefon' ))  }}
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="form-goup">
                            <label>Email główny (oddzielone przecinkiem):</label>
                            {{ Form::text('email', '', array('class' => 'form-control  tips ', 'placeholder' => 'email (oddzielone przecinkiem)', 'title' => 'email (oddzielone przecinkiem)'))  }}
                        </div>
                    </div>
                   <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="form-group">
                            <label>Email dodatkowy (oddzielone przecinkiem):</label>
                            {{ Form::text('other_emails', '', array('class' => 'form-control   tips', 'placeholder' => 'email (oddzielone przecinkiem)', 'title' => 'email dodatkowy (oddzielone przecinkiem)'))  }}
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="form-group">
                            <label>Zakres obsługi:</label>
                            <select name="typeGarages_id[]" id="typeGarages" class="form-control" multiple="multiple">
                                <?php foreach ($typegarages as $k => $v) {
                                    echo '<option value="' . $v->id . '">' . $v->name . '</option>';
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="form-group">
                            <label>Priorytet:</label>
                            <select name="priority" class="form-control">
                                <?php for ($i = 0; $i <= 9; $i++) {
                                    echo '<option value="' . $i . '">priorytet ' . $i . '</option>';
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Osoby kontaktowe:</label>
                {{ Form::textarea('contact_people', '', array('class' => 'form-control  ', 'rows' => '3', 'placeholder' => 'Osoby kontaktowe'))  }}
            </div>

            <div class="form-group">
                <label>Ilość aut zastępczych:</label>
                <div class="row">
                    @foreach($typevehicles as $type)
                        <div class="col-md-3 col-lg-3 marg-btm">
                            <input id="car{{$type->id}}" type="text" value="0" name="car{{$type->id}}"
                                   desc="{{$type->name}}" class="carSpinner">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="tug" name="tug"> Posiada holownik
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 marg-btm">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="tug24h" name="tug24h" disabled="disabled"> Dostępność
                                holownika 24h
                            </label>
                        </div>
                    </div>
                </div>
                <label>Holownik (uwagi):</label>
                {{ Form::textarea('tug_remarks', '', array('class' => 'form-control  ', 'rows' => '3','placeholder' => 'Uwagi dotyczące holownika'))  }}
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 marg-btm">
                        <div class="btn btn-xs btn-info btn-block" data-toggle="collapse" data-target="#collapseBrandsO">
                            <i class="fa fa-exchange fa-fw"></i>
                            obsługiwane marki osobowe <span class="badge" id="brands-o-badge">0</span>
                        </div>
                    </div>
                    <div class="col-md-6 marg-btm">
                        <div class="btn btn-xs btn-info btn-block" data-toggle="collapse" data-target="#collapseBrandsC">
                            <i class="fa fa-exchange fa-fw"></i>
                            obsługiwane marki ciężarowe <span class="badge" id="brands-c-badge">0</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="collapse col-sm-6" id="collapseBrandsO">
                        <div class="panel panel-default panel-small small">
                            <div class="panel-heading text-center">
                                osobowe
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-md-offset-3">
                                        <span class="btn btn-primary btn-xs btn-block modal-open off-disable" data-toggle="modal" data-target="#modal" target="{{ URL::to('company/garages/add-brands/1') }}">
                                            <i class="fa fa-plus fa-fw"></i>
                                            dodaj
                                        </span>
                                    </div>
                                    <div class="col-sm-12 col-lg-6 col-lg-offset-3 brands-list">
                                        <table class="table table-condensed table-hover">
                                            <thead>
                                                <th>marka</th>
                                                <th>autoryzowany</th>
                                                <th></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse col-sm-6" id="collapseBrandsC">
                        <div class="panel panel-default panel-small small">
                            <div class="panel-heading text-center">
                                ciężarowe
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-md-offset-3">
                                        <span class="btn btn-primary btn-xs btn-block modal-open off-disable" data-toggle="modal" data-target="#modal" target="{{ URL::to('company/garages/add-brands/2') }}">
                                            <i class="fa fa-plus fa-fw"></i>
                                            dodaj
                                        </span>
                                    </div>
                                    <div class="col-sm-12 col-lg-6 col-lg-offset-3 brands-list">
                                        <table class="table table-condensed table-hover">
                                            <thead>
                                            <th>marka</th>
                                            <th>autoryzowany</th>
                                            <th></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Obsługiwane samochody dostawcze:</label>
                {{ Form::textarea('delivery_cars', '', array('class' => 'form-control  ', 'rows' => '1', 'placeholder' => 'Obsługiwane samochody dostawcze'))  }}
            </div>
            <div class="form-group">
                <label>Godziny pracy:</label>
                <div class="row">
                    <div class="col-md-3 col-lg-3 marg-btm">
                        Od: <input type="time" class="form-control" name="open_time">
                    </div>
                    <div class="col-md-3 col-lg-3 marg-btm">
                        Do: <input type="time" class="form-control" name="close_time">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Uwagi:</label>
                {{ Form::textarea('remarks', '', array('class' => 'form-control  ', 'placeholder' => 'uwagi'))  }}
            </div>
            <div class="form-group">
                <label>Kierowalność/priorytety:</label>
                {{ Form::textarea('priorities', '',array('class' => 'form-control  ', 'rows' => '3', 'placeholder' => 'kierowalność/priorytety'))  }}
            </div>
            {{Form::token()}}
            {{Form::hidden('lat', '', array('id' => 'lat'))}}
            {{Form::hidden('lng', '', array('id' => 'lng'))}}
        </div>
    </form>



@stop

@section('headerJs')
    @parent
    <script type="text/javascript">

        var mapa;
        var geocoder = new google.maps.Geocoder();
        var marker;
        var infowindow = new google.maps.InfoWindow();
        var correlation = 0;

        function initialize() {

            var myOptions = {
                zoom: 7,
                scrollwheel: true,
                navigationControl: false,
                mapTypeControl: false,
                center: new google.maps.LatLng(52.528846, 17.071874),
            };

            mapa = new google.maps.Map(document.getElementById('map-canvas'), myOptions);

            google.maps.event.addListener(mapa, 'click', function (event) {
                placeMarker(event.latLng);
            });

            var slat = $('#lat').val();
            var slng = $('#lng').val();

            if (slat != '' && slng != '') {
                latlng = new google.maps.LatLng(slat, slng);
                placeMarker(latlng);
            }

        };

        function initListenerDrag() {
            google.maps.event.addListener(marker, "dragend", function (event) {
                var lat_d = event.latLng.lat();
                var lng_d = event.latLng.lng();
                $('#lat').val(lat_d);
                $('#lng').val(lng_d);
                var latlng = new google.maps.LatLng(lat_d, lng_d);
                if (correlation == 0) {
                    geocoder.geocode({'latLng': latlng}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {

                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(mapa, marker);

                            var adressA = results[0].formatted_address.split(', ');
                            var kod = adressA[1];
                            var rx = /\d\d-\d\d\d/;
                            var wynik = rx.exec(kod);
                            if (wynik) {
                                adressA[1] = adressA[1].replace(rx, '');
                                adressA[1] = $.trim(adressA[1]);
                            }

                            $("#city").val(adressA[1]);
                            $('#street').val(adressA[0]);

                        }
                    });
                }
            });
        }

        function placeMarker(location) {
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    draggable: true,
                    map: mapa
                });
            }
            $('#lat').val(location.lat());
            $('#lng').val(location.lng());
            if (correlation == 0) {
                geocoder.geocode({'latLng': location}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {

                        infowindow.setContent('<div style="height:30px;">' + results[0].formatted_address + '</div>');
                        infowindow.open(mapa, marker);

                        var adressA = results[0].formatted_address.split(', ');

                        var kod = adressA[1];
                        var rx = /\d\d-\d\d\d/;
                        var wynik = rx.exec(kod);
                        if (wynik) {
                            adressA[1] = adressA[1].replace(rx, '');
                            adressA[1] = $.trim(adressA[1]);
                        }

                        $("#city").val(adressA[1]);
                        $('#street').val(adressA[0]);
                        $('#code').change();
                    }
                });
            }
            initListenerDrag();

        }

        function lokalizacja_wyszukiwanie() {
            var slat = 0;
            var slng = 0;
            var address = $("#city").val() + ' ' + $('#street').val();
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    mapa.panTo(results[0].geometry.location);
                    slat = results[0].geometry.location.lat();
                    slng = results[0].geometry.location.lng();

                    $('#lat').val(slat);
                    $('#lng').val(slng);

                    var latlng = new google.maps.LatLng(slat, slng);
                    if (marker == null) {
                        marker = new google.maps.Marker({
                            position: latlng,
                            draggable: true,
                            map: mapa,
                        });
                    } else {
                        marker.setPosition(latlng);
                    }

                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(mapa, marker);

                    initListenerDrag();

                    if ($('#street').val() == '')
                        mapa.setZoom(12);
                    else
                        mapa.setZoom(16);
                } else {
                    //alert("Nie można zlokalizować podanego adresu.");
                }
            });
        }

        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();


        function movieFormatResult(brand) {
            var markup = "<table class='movie-result'><tr><td>" + brand.name + "</td></tr></table>";
            return markup;
        }

        function movieFormatSelection(movie) {
            return movie.name;
        }

        $(document).ready(function () {
            $("form").submit(function (e) {
                var self = this;
                e.preventDefault();
                if ($("form").valid()) {
                    self.submit();
                }
                return false; //is superfluous, but I put it here as a fallback
            });

            $('#typeGarages').multiselect({
                buttonWidth: $('input[name="email"]').width() + 25,
                buttonText: function (options) {
                    if (options.length === 0) {
                        return 'Wybierz typy warsztatu <b class="caret"></b>';
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

            initialize();
            $('#mapShow').on('change', function () {
                if ($('#mapShow').is(':checked')) {
                    $('#map-canvas').show();
                    $('#correctMap').parent().parent().show();
                    initialize();
                    lokalizacja_wyszukiwanie();

                    $('#correctMap').on('change', function () {
                        if ($('#correctMap').is(':checked')) {
                            correlation = 1;
                        } else {
                            correlation = 0;
                        }

                    });

                } else {
                    $('#map-canvas').hide();
                    $('#correctMap').parent().parent().hide();
                }
            });

            $('#city, #street').keyup(function () {
                delay(function () {
                    lokalizacja_wyszukiwanie();
                }, 500);
            });

            $('.carSpinner').TouchSpin({
                min: 0,
                max: 1000000000,
                stepinterval: 1,
                prefix: ' '
            }).each(function () {
                var desc = $(this).attr('desc');
                $(this).prev().html(desc).css('border-right', '0px');
            });

            $('#tug').change(function () {
                if ($(this).is(':checked'))
                    $('#tug24h').removeAttr('disabled');
                else
                    $('#tug24h').attr('disabled', 'disabled');
            });

            $('#cp_company').click(function () {

                $.ajax({
                    type: 'GET',
                    url: '<?php echo URL::to('company/garages/data-to-branch', array($company->id)); ?>',
                    assync: false,
                    cache: false,
                    success: function (data) {
                        response = tryParseJSON(data);

                        $('input[name=short_name]').val(response.short_name);
                        $('input[name=street]').val(response.street);
                        $('input[name=code]').val(response.code);
                        $('input[name=city]').val(response.city);
                        $('input[name=email]').val(response.email);
                        $('input[name=phone]').val(response.phone);
                        $('#code').change();
                    }
                });
            });

            $('#code').on('change keyup paste click', function () {
                var $code = $(this).val();
                if ($code.length == '6') {
                    $.ajax({
                        type: 'GET',
                        url: '/company/garages/check-voivodeship',
                        data: {'code': $code},
                        assync: false,
                        cache: false,
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == 'ok') {
                                $('#voivodeship_id').val(data.voivodeship_id);
                            }
                        }
                    });
                }
            });

            $('.brands-list').on('click', '.remove-brand-row', function () {
                $(this).parents('tr').remove();
            });

        });


    </script>

@stop
