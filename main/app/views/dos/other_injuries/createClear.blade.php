@extends('layouts.main')

@section('styles')
    @parent
@stop

@section('header')

    Dodawanie nowego zlecenia

@stop

@section('main')
    <form action="{{ URL::route('dos.other.injuries.post' ) }}" method="post" role="form">
        <div class="row marg-btm">
            <div class="pull-right">
                <a href="{{{ URL::previous() }}}" class="btn btn-default">Anuluj</a>
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
                <div class="row injury_bg ">
                    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy leasingowej'>
                        {{ Form::text('nr_contract', '', array('class' => 'form-control  required upper', 'id' => 'nr_contract', 'placeholder' => 'Nr umowy leasingowej'))  }}
                    </div>
                    <div class="col-md-2 marg-btm " >
                        <a class="btn btn-warning btn-sm" href="{{ URL::route('dos.other.injuries.create') }}">Wprowadź w ISDL</a>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm " >
                        <button type="button" class="btn btn-warning btn-sm hidden" id="show_exists">W systemie istnieją już zgłoszenia dla tego obiektu <span class="badge" id="num_exists"></span></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-md-offset-2 marg-btm " style="display:none;" id="exists_injuries" >
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Dane identyfikacyjne przedmiotu:</span></h4>
                <div id="object_container">
                    <div class="row">
                        <div class="col-md-4 col-lg-3 marg-btm">
                            <div class="form-group">
                                {{ Form::text('description', '', array('id'=>'description', 'class' => 'form-control upper tips', 'placeholder' => 'Opis przedmiotu', 'title' => 'Opis przedmiotu'))  }}
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            <div class="form-group">
                                {{ Form::text('factoryNbr', '', array('id'=>'factoryNbr', 'class' => 'form-control upper tips',  'placeholder' => 'Numer fabryczny', 'title' => 'Numer fabryczny'))  }}
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            <span class="input-group tips" title="Kategoria">
                                <select name="assetType_id" id="assetType_id" class="form-control required" >
                                    <option value="">---wybierz kategorię---</option>
                                    <?php foreach($assetTypes as $k => $v){
                                        echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                                    }?>
                                </select>
                                <span class="input-group-btn">
                                    <button target="{{ URL::route('dos.other.injuries.dialog.create.category') }}" class="btn btn-default add-category tips" data-toggle="modal" data-target="#modal"  type="button" title="Dodaj nową kategorię"><span class="fa fa-plus"></span></button>
                                </span>
                            </span>
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            <div class="form-group">
                                {{ Form::text('year_production', '', array('id'=>'year_production', 'class' => 'form-control upper tips',  'placeholder' => 'Rok produkcji', 'title' => 'Rok produkcji'))  }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Dane właściciela i leasingobiorcy:</span></h4>
                <div class="row">
                    <div class="col-md-6 col-lg-6 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="owner_idea">
                                <select name="owner_id" id="owner_id" class="form-control required tips" title="właściciel" >
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }}
                                            @if($owner->old_name)
                                                ({{ $owner->old_name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                    <button  class="btn btn-default show-owner"  type="button" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="client_idea">
                                <input id="search_client"  type="text" class="form-control required" placeholder="Wpisz nazwę leasingobiorcy | NIP | REGON aby wyszukać">
                                {{ Form::hidden('client_id', null, ['id' => 'client_id']) }}
                                <span class="input-group-btn">
                                    <button style="border-radius:0px;margin-left: -1px;" target="{{{ URL::route('dos.other.injuries.newClient') }}}" class="btn btn-default modal-open tips" data-toggle="modal" data-target="#modal"  type="button" title="Dodaj nowego klienta"><span class="fa fa-plus"></span></button>
                                </span>
                                <span class="input-group-btn">
                                    <button  class="btn btn-default show-client"  type="button" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
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
                            {{ Form::text('end_leasing', '', array('id'=>'end_leasing', 'class' => 'form-control upper tips',  'placeholder' => 'Data końca leasingu', 'title' => 'Data końca leasingu', 'autocomplete' => 'off'))  }}
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3  marg-btm">
                        <div class="form-group">
                            {{ Form::text('contract_status', '', array('id'=>'contract_status', 'class' => 'form-control upper tips', 'placeholder' => 'Status umowy', 'title' => 'Status umowy'))  }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Polisa ubezpieczeniowa:</span></h4>
                <div class="row">
                    <div class="col-md-4 col-lg-3  marg-btm">
                        <div class="form-group">
                            {{ Form::text('expire', '', array('id'=>'expire', 'class' => 'form-control upper tips', 'placeholder' => 'Data ważności polisy', 'title' => 'Data ważności polisy z serwera IL', 'autocomplete' => 'off'))  }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Dane zgłaszającego: </span></h4>
                <div class="row injury_bg">
                    <div class="col-md-4 col-lg-3 marg-btm tips" title ='Imię zgłaszającego'>
                        {{ Form::text('notifier_name', '', array('class' => 'form-control bold  required upper', 'placeholder' => 'imię'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nazwisko zgłaszającego'>
                        {{ Form::text('notifier_surname', '', array('class' => 'form-control bold required upper', 'placeholder' => 'nazwisko'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm tips"  title ='Telefon zgłaszającego'>
                        {{ Form::text('notifier_phone', '', array('class' => 'form-control bold upper', 'placeholder' => 'telefon'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Email zgłaszającego'>
                        {{ Form::text('notifier_email', '', array('class' => 'form-control bold email', 'placeholder' => 'email'))  }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Dane szkody:</span></h4>
                <div class="row injury_bg">
                    <div class="col-md-4 col-lg-4 marg-btm">
                        <label >Data zdarzenia:</label>
                        {{ Form::text('date_event', '', array('class' => 'form-control bold required', 'id'=>'date_event', 'placeholder' => 'data zdarzenia'))  }}
                    </div>

                </div>

                <div class="row injury_bg">
                    <div class="col-md-12 marg-btm">
                        <label >Miejsce zdarzenia:</label>
                    </div>
                    <div class="col-md-4 col-lg-4 marg-btm">
                        {{ Form::text('event_city', '', array('class' => 'form-control bold  upper', 'id' => 'city', 'placeholder' => 'miasto'))  }}
                    </div>
                    <div class="col-md-4 col-lg-4 marg-btm">
                        {{ Form::text('event_street', '', array('class' => 'form-control bold  upper', 'id' => 'street', 'placeholder' => 'ulica'))  }}
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
                                    <input type="radio" name="zdarzenie" class="required" id="zdarzenie{{ $v->id }}" value="{{ $v->id }}" >
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
                    {{ Form::textarea('remarks', '', array('class' => 'form-control bold ', 'placeholder' => 'Opis szkody'))  }}
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
				        	<button target="{{ URL::route('insurance_companies-create') }}" class="btn btn-default add-insurance_company tips" data-toggle="modal" data-target="#modal"  type="button" title="Dodaj nowy ZU"><span class="fa fa-plus"></span></button>
				      	</span>
						<span class="input-group-btn">
				        	<button class="btn btn-default show-insurance_company" type="button" ><span class="fa fa-search"></span></button>
				      	</span>
			      	</span>
                </div>
            </div>
            <div class="row injury_bg">
                <div class="col-md-4 col-lg-3 marg-btm">
                    <label >Odbiór odszkodowania:</label>
                    <select name="receives" id="receives" class="form-control" >
                        <option value="">---wybierz---</option>
                        <?php foreach($receives as $k => $v){
                            echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                        }?>
                    </select>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <label >Odbiór faktury:</label>
                    <select name="invoicereceives" id="invoicereceives" class="form-control required" >
                        <option value="">---wybierz---</option>
                        <?php foreach($invoicereceives as $k => $v){
                            echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                        }?>
                    </select>
                </div>
            </div>
            <div class="row injury_bg marg-btm">
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
        {{Form::hidden('object_id', '', array('id' => 'object_id'))}}
        {{-- adm-administator, inf-infolinia --}}
        {{Form::hidden('insert_role', 'adm')}}

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
                        <button type="button"  class="btn btn-default" data-dismiss="modal">Zamknij</button>
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
                center: new google.maps.LatLng(52.528846,17.071874)
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
                var btn = $('#addInjurySubmit');
                btn.attr('disabled', 'disabled');

                if($("form").valid()){
                    self.submit();
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

            var focus = 0;

            $( "#nr_contract" ).focusout(function(){
                if($(this).val().length > 0  && focus == 0){
                    focus = 1;
                    if($('#object_id').val() != ''){
                        $.ajax({
                            url: "<?php echo  URL::route('dos.other.object.checkInjuries');?>",
                            data: {
                                object_id: $('#object_id').val(),
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
                    }else{
                        $.ajax({
                            url: "<?php echo  URL::route('dos.other.object.checkIfExist');?>",
                            data: {
                                term: $(this).val(),
                                _token: $('input[name="_token"]').val()
                            },
                            async: false,
                            cache: false,
                            type: "POST",
                            success: function( data ) {
                                if(data != '0')
                                {
                                    focus = 0;
                                    $('#object_id').val(data);
                                    $.ajax({
                                        url: "<?php echo  URL::route('dos.other.injuries.getObjectData');?>",
                                        data: {
                                            object_id: data,
                                            _token: $('input[name="_token"]').val()
                                        },
                                        async: false,
                                        cache: false,
                                        type: "POST",
                                        success: function( data ) {
                                            data = tryParseJSON( data );
                                            $('#client_id').val(data.client_id);
                                            $('#search_client').val(data.client_name);
                                            $('#owner_id').val(data.owner_id);
                                            $('#factoryNbr').val(data.factoryNbr);
                                            $('#description').val(data.description);
                                            $('#assetType_id option[value="'+data.assetType.id+'"]').attr("selected", "selected");
                                            $('#year_production').val(data.year_production);
                                            $('#end_leasing').val(data.end_leasing);
                                            $('#expire').val(data.expire);
                                            $('#insurance_company_id').val(data.insurance_company_id);
                                            $('#contract_status').val(data.contract_status);
                                        }
                                    });

                                    setTimeout(function(){
                                        $('#nr_contract').focusout();
                                    },500);
                                }else{
                                    $.ajax({
                                        url: "<?php echo  URL::route('dos.other.object.searchInInsurances');?>",
                                        data: {
                                            term: $( "#nr_contract" ).val(),
                                            _token: $('input[name="_token"]').val()
                                        },
                                        async: false,
                                        cache: false,
                                        type: "POST",
                                        dataType: 'json',
                                        success: function (data) {
                                            if(data.status == '1')
                                            {
                                                $('#client_id').val(data.client_id);
                                                $('#search_client').val(data.client_name);
                                                $('#owner_id').val(data.owner_id);
                                                $('#expire').val(data.expire);
                                                $('#insurance_company_id').val(data.insurance_company_id);

                                                if(isset(data.object))
                                                {
                                                    $('#factoryNbr').val(data.factoryNbr);
                                                    $('#description').val(data.description);
                                                    $('#assetType_id option[value="'+data.assetType.id+'"]').attr("selected", "selected");
                                                    $('#year_production').val(data.year_production);
                                                }else{
                                                    $.get( "{{ URL::route('dos.other.object.selectInsuranceObject') }}", { objects : data.objects } , function( data ) {
                                                        $('#modal-lg .modal-content').html(data);
                                                        $('#modal-lg').modal('show');
                                                    });
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }else{

                    if($(this).val() != ''){
                        $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
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
                        url: "<?php echo  URL::route('dos.other.contract.getListNonIsdl');?>",
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
                    $('#object_id').val(ui.item.id);

                    $.ajax({
                        url: "<?php echo  URL::route('dos.other.injuries.getObjectData');?>",
                        data: {
                            object_id: $('#object_id').val(),
                            _token: $('input[name="_token"]').val()
                        },
                        async: false,
                        cache: false,
                        type: "POST",
                        success: function( data ) {
                            data = tryParseJSON( data );

                            $('#client_id').val(data.client_id);
                            $('#search_client').val(data.client_name);
                            $('#owner_id').val(data.owner_id);
                            $('#factoryNbr').val(data.factoryNbr);
                            $('#description').val(data.description);
                            $('#assetType_id option[value="'+data.assetType.id+'"]').attr("selected", "selected");
                            $('#year_production').val(data.year_production);
                            $('#end_leasing').val(data.end_leasing);
                            $('#expire').val(data.expire);
                            $('#insurance_company_id').val(data.insurance_company_id);
                            $('#contract_status').val(data.contract_status);
                        }
                    });

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

            $('#search_client').focusout(function(){
                if($(this).val().length > 0 && isset($('#client_id').val()) ){

                }else{
                    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                    $('#modal .modal-title').html('Komunikat');
                    $('#modal .modal-body').html('Wprowadzono błędnego leasingobiorcę');
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                    $('#modal').modal('show');

                }
            }).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "<?php echo  URL::route('dos.other.client.search');?>",
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
                minLength: 3,
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function(event, ui) {
                    $('#client_id').val(ui.item.id);
                    setTimeout(function(){
                        $('#search_client').focusout();
                    },500);
                }
            }).bind("keypress", function(e) {
                if( e.which == 13 ){
                    setTimeout(function(){
                        $('#search_client').focusout();
                    },500);
                }else{
                    $('#client_id').val('');
                }
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
                    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                    $('#modal .modal-title').html('Dane właściciela');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //podgląd danych klienta?>
            $('.show-client').click(function(){
                var client_id = $('#client_id').val();
                if(isset(client_id)) {
                    var hrf = '/injuries/client/show/' + client_id + '/';

                    $.get(hrf, function (data) {
                        $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                        $('#modal .modal-title').html('Dane leasingobiorcy');
                        $('#modal .modal-body').html(data);
                        $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                    });
                }else{
                    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                    $('#modal .modal-title').html('Dane leasingobiorcy');
                    $('#modal .modal-body').html('<i>nie wybrano leasingobiorcy</i>');
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                }
            });
            <?php //edycja właściciela?>
            $('.edit-owner').click(function(){
                var id = $('#owner_id').val();
                if(id == '') id = 0;

                var hrf='/injuries/owner/edit/'+id+'/';
                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                    $('#modal .modal-title').html('Zmiana właściciela pojazdu');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //edycja klienta?>
            $('.edit-client').click(function(){
                var id = $('#client_id').val();
                if(id == '') id = 0;

                var hrf='/injuries/client/edit/'+id+'/';
                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                    $('#modal .modal-title').html('Zmiana klienta');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });



            <?php //podgląd danych ubezpieczalni?>
            $('.show-insurance_company').click(function(){
                var hrf='/injuries/insurance_company/show/'+$('#insurance_company_id').val()+'/';

                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body"></div><div class="modal-footer"></div>');
                    $('#modal .modal-title').html('Dane ubezpieczalni');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                    $('#modal').modal('show');
                });
            });

            <?php //dodawanie ubezpieczalni/kategorii?>
            $('.add-insurance_company, .add-category').click(function(){
                var hrf=$(this).attr('target');

                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(data);
                });
            });

            $('#end_leasing').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });;
            $('#expire').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });;

            $('.upper').focusout(function(){
                $(this).val($(this).val().toUpperCase());
            });

            $('#modal').on('click', '#save', function(){
                $('#insurance_companies-form').validate();

                if($('#insurance_companies-form').valid() ){
                    $.post(
                            $('#insurance_companies-form').prop( 'action' ),

                            $('#insurance_companies-form').serialize()
                            ,
                            function( data ) {
                                if(data == '0'){

                                    var hrf="<?php echo URL::route('insurance_companies-list');?>";

                                    $.get( hrf, function( data ) {
                                        $('#modal').modal('hide');
                                        $('#insurance_company_id').html(data);
                                    });

                                }else{
                                    $('#modal .modal-content').html(data);
                                }
                            },
                            'json'
                    );
                    return false;
                }

            });

            $('#modal').on('click', '#add_category', function(){
                $('#category-form').validate();
                $(this).attr('disabled', 'disabled');

                if($('#category-form').valid() ){
                    $.post(
                        $('#category-form').prop( 'action' ),
                        $('#category-form').serialize()
                        ,
                        function( data ) {
                            if(data.status == 'success'){
                                $('#modal').modal('hide');
                                $('#modal .modal-content').html('');
                                $('#assetType_id').html(data.html);
                            }else{
                                $('#modal .modal-content .modal-body').html(data.msg);
                            }
                        },
                        'json'
                    );
                    return false;
                }else{
                    $(this).removeAttr('disabled');
                }
            });

            $('#modal').on('click', '#addClient', function(){
                $('#create-form').validate();

                if($('#create-form').valid() ){
                    $.post(
                            $('#create-form').prop( 'action' ),
                            $('#create-form').serialize(),
                            function( data ) {
                                if(data == '0'){
                                    $('#client_id').val(data);
                                    $('#modal .modal-content').html('');
                                    $('#modal').modal('hide');
                                }else{
                                    $('#modal .modal-content').html(data);
                                }
                            },
                            'json'
                    );
                    return false;
                }

            });


        });



    </script>

@stop

