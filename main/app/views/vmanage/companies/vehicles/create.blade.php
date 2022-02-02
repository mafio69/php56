@extends('layouts.main')

@section('header')

Dodawanie pojazdu dla firmy {{ $company->name }}

@stop

@section('main')

{{ Form::open(array('url' => URL::action('VmanageVehiclesController@postStore'))) }}
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
		</div>
	</div>
    <input type="hidden" name="vmanage_company_id" value="{{ $company->id }}"/>
	<div class="row">

        <div class="form-group">
            <h4 class="inline-header"><span>Dane techniczne pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Typ pojazdu:</label>
                        {{ Form::select('type', array('1' => 'osobowy', '2' => 'ciężarowy'), 1 , array('id'=>'type', 'class' => 'form-control tips required', 'required', 'placeholder' => 'Typ pojazdu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Marka pojazdu:</label>
                        {{ Form::text('brand', '', array('id'=>'brand', 'class' => 'form-control  ', 'required', 'placeholder' => 'marka pojazdu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Model pojazdu:</label>
                        {{ Form::text('model', '', array('id'=>'model', 'class' => 'form-control  ', 'required', 'placeholder' => 'model pojazdu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Generacja modelu:</label>
                        {{ Form::select('generation_id', array(), null, array('id'=>'generation', 'class' => 'form-control tips', 'placeholder' => 'generacja modelu', 'title' => 'generacja modelu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Wersja:</label>
                        {{ Form::text('version', '', array('id'=>'version', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label for="year_production">Rok produkcji:</label>
                        {{ Form::text('year_production', '', ['class' => 'form-control'])}}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Nadwozie:</label>
                        {{ Form::select('car_category_id', $car_category, null, array('id'=>'car_category', 'class' => 'form-control tips', 'placeholder' => 'typ nadwozia', 'title' => 'typ nadwozia'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Liczba drzwi:</label>
                        {{ Form::text('doors_nb', '0', array('id'=>'doors_nb', 'class' => 'form-control tips spinner',  'desc' => 'liczba drzwi', 'title' => 'liczba drzwi'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Typ silnika:</label>
                        {{ Form::select('car_engine_id', $engines, null, array('id'=>'car_engine', 'class' => 'form-control tips',  'title' => 'typ silnika'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Skrzynia biegów:</label>
                        {{ Form::select('car_gearbox_id', $gearboxes, null, array('id'=>'car_gearbox', 'class' => 'form-control tips', 'title' => 'typ skrzyni biegów'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Pojemność silnika:</label>
                        {{ Form::text('engine_capacity', '0', array('id'=>'engine_capacity', 'class' => 'form-control tips', 'title' => 'pojemność silnika w cm szcześciennych'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Moc silnika:</label>
                        {{ Form::text('horse_power', '0', array('id'=>'horse_power', 'class' => 'form-control tips', 'title' => 'moc silnika w KM'))  }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Dane pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group tips" title = 'numer rejestracyjny pojazdu' >
                        <label for="brand">Numer rejestracyjny:</label>
                        {{ Form::text('registration', '', array('id'=>'registration', 'class' => 'form-control ', 'required'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group tips" title = 'unikalny nr VIN pojazdu'>
                        <label for="brand">Numer VIN:</label>
                        {{ Form::text('vin', '', array('id'=>'vin', 'class' => 'form-control tips',  'required'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group tips">
                        <label for="brand">Nr umowy leasingowej:</label>
                        {{ Form::text('nr_contract', null, array('id'=>'nr_contract', 'class' => 'form-control tips'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label for="first_registration">Data pierwszej rejestracji:</label>
                        {{ Form::text('first_registration', '', ['class' => 'form-control date'])}}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Dane bierzące:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group "  >
                        <label for="brand">Bieżący użytkownik:</label>
                        <div class="input-group">
                            {{ Form::text('search_user', '', array('class' => 'form-control', 'id' => 'get_search_user', 'placeholder' => 'wprowadź imię lub nazwisko użytkownika'))  }}
                            {{ Form::hidden('vmanage_user_id')}}
                            <span class="input-group-addon btn tips modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::action('VmanageUsersController@getCreate', [$company->id]) }}" title="utwórz nowego użytkownika"><i class="fa fa-plus"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Miejsce użytkowania:</label>
                        {{ Form::text('place_of_usage', '', array('class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Przebieg deklarowany:</label>
                        {{ Form::text('declare_mileage', '', array('id'=>'declare_mileage', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Przebieg bieżący:</label>
                        {{ Form::text('actual_mileage', '', array('id'=>'actual_mileage', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Termin badania technicznego:</label>
                        {{ Form::text('technical_exam_date', '', array('id'=>'technical_exam_date', 'class' => 'form-control date'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Termin przeglądu:</label>
                        {{ Form::text('servicing_date', '', array('id'=>'servicing_date', 'class' => 'form-control date'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label for="insurance_expire_date">Termin ważności polisy:</label>
                        {{ Form::text('insurance_expire_date', '', ['class' => 'form-control date'])}}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label for="insurance">Suma ubezpieczenia AC:</label>
                        {{ Form::text('insurance', 0, ['class' => 'form-control currency_input'])}}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label for="insurance">Assistance:</label>
                        {{ Form::text('assistance', '', ['class' => 'form-control currency_input'])}}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="cfm" id="cfm" value="1"> CFM
                        </label>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="if_vip" id="if_vip" value="1"> pojazd VIP
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Klient:</span></h4>
            <div class="row"  id="search_client">
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label>Wyszukaj klienta po nazwie:</label>
                        {{ Form::text('search_client', '', array('class' => 'form-control', 'id' => 'get_search_client', 'placeholder' => 'wprowadź nazwę klienta'))  }}
                        {{ Form::hidden('client_id') }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm alert alert-danger" role="alert" id="client_alert" style="display: none;">Nie znaleziono klienta w bazie</div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group marg-top">
                        <span class="btn btn-primary btn-sm btn-block" id="add_new_client">
                            <i class="fa fa-user-plus"></i> wprowadź nowego klienta
                        </span>
                    </div>
                </div>
            </div>
            <div id="create_new_client" style="display:none;">
                <div class="row">
                    <div class="col-sm-12 marg-btm">
                        <label >Nazwa:</label>
                        {{ Form::text('name', null, array('class' => 'form-control  required', 'id'=>'name',  'placeholder' => 'nazwa')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >NIP:</label>
                        {{ Form::text('NIP', null, array('class' => 'form-control  required', 'id'=>'NIP',  'placeholder' => 'NIP')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >REGON:</label>
                        {{ Form::text('REGON', null, array('class' => 'form-control  ', 'id'=>'REGON',  'placeholder' => 'REGON')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Kod klienta:</label>
                        {{ Form::text('firmID', null, array('class' => 'form-control  ', 'id'=>'firmID',  'placeholder' => 'kod klienta')) }}
                    </div>
                </div>
                <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
                <div class="row">
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Kod pocztowy:</label>
                        {{ Form::text('registry_post', null, array('class' => 'form-control  ', 'id'=>'registry_post',  'placeholder' => 'kod pocztowy')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Miasto:</label>
                        {{ Form::text('registry_city', null, array('class' => 'form-control  ', 'id'=>'registry_city',  'placeholder' => 'Miasto')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Ulica:</label>
                        {{ Form::text('registry_street', null, array('class' => 'form-control  ', 'id'=>'registry_street',  'placeholder' => 'ulica')) }}
                    </div>
                </div>
                <h4 class="inline-header"><span>Dane kontaktowe:</span></h4>
                <div class="row">
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Kod pocztowy:</label>
                        {{ Form::text('correspond_post', null, array('class' => 'form-control  ', 'id'=>'correspond_post',  'placeholder' => 'kod pocztowy')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Miasto:</label>
                        {{ Form::text('correspond_city', null, array('class' => 'form-control  ', 'id'=>'correspond_city',  'placeholder' => 'Miasto')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Ulica:</label>
                        {{ Form::text('correspond_street', null, array('class' => 'form-control  ', 'id'=>'correspond_street',  'placeholder' => 'ulica')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Tefelon:</label>
                        {{ Form::text('phone', null, array('class' => 'form-control  ', 'id'=>'phone',  'placeholder' => 'telefon')) }}
                    </div>
                    <div class="col-sm-12 col-md-6 marg-btm">
                        <label >Email:</label>
                        {{ Form::text('email', null, array('class' => 'form-control email ', 'id'=>'email',  'placeholder' => 'email')) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Właściciel pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="if_leasing">Pojazd w leasingu:</label>
                        {{ Form::select('if_leasing', [1 => 'nie', 2 => 'tak'], 1, array('id'=>'if_leasing', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        <label for="owner_id">Właściciel:</label>
                        <p class="form-control" id="owner_company_name">{{ $company->name }}</p>
                        <div class="input-group" id="owner_id_group">
                            {{ Form::select('owner_id', $owners, null, array('id'=>'owner_id', 'class' => 'form-control'))  }}
                            <span class="input-group-addon btn tips modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::action('VmanageOwnersController@getCreate') }}" title="utwórz nowego właściciela">
                                <i class="fa fa-plus"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
    <div class="row marg-btm">
        <h4 class="inline-header "></h4>
        <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
            {{ Form::submit('Zapisz',  array('id'=>'store', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }}
        </div>
    </div>
    {{ Form::hidden('brand_id') }}
    {{ Form::hidden('model_id') }}
{{ Form::close() }}



@stop

@section('headerJs')
	@parent
    <script type="text/javascript" >

        $(document).ready(function(){
			$("form").submit(function(e) {
			    var self = this;
			    e.preventDefault();
                var btn = $('#store');
                btn.attr('disabled', 'disabled');

			    if($("form").valid()){
                    self.submit();
			    }else{
                    btn.removeAttr('disabled');
			    }
			    return false; //is superfluous, but I put it here as a fallback
			});

			$('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });

            $('#type').on('change', function(){
                $('input[name="brand_id"]').val('');
                $('input[name="model_id"]').val('');
                $('#model').val('');
                $('#brand').val('').focus();
                $('#generation').html('');
                $.ajax({
                    url: "<?php echo  URL::action('VmanageVehiclesController@postCategories');?>",
                    data: {
                        type: $('#type').val(),
                        _token: $('input[name="_token"]').val()
                    },
                    type: "POST",
                    success: function( data ) {
                        $('#car_category').html(data);
                    }
                });
            });

			$('#brand').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "<?php echo  URL::action('VmanageVehiclesController@postBrands');?>",
                        data: {
                            term: request.term,
                            type: $('#type').val(),
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
                    if(ui.item.id != $('input[name="brand_id"]').val())
                    {
                        $('input[name="brand_id"]').val(ui.item.id);
                        $('input[name="model_id"]').val('');
                        $('#model').val('');
                        $('#model').focus();
                    }
                }
            }).bind("keypress", function(e) {
                if(e.which == 13){
                    setTimeout(function(){
                        $('#brand').focusout();
                    },500);
                 }else{
                    $('input[name="brand_id"]').val('');
                    $('input[name="model_id"]').val('');
                    $('#model').val('');
                    $('#generation').html('');
                 }
            });

            $('#model').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "<?php echo  URL::action('VmanageVehiclesController@postModels');?>",
                        data: {
                            term: request.term,
                            brand: $('input[name="brand_id"]').val(),
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
                    if(ui.item.id != $('input[name="model_id"]').val())
                    {
                        $('input[name="model_id"]').val(ui.item.id);
                        $.ajax({
                            url: "<?php echo  URL::action('VmanageVehiclesController@postGenerations');?>",
                            data: {
                                model: ui.item.id,
                                _token: $('input[name="_token"]').val()
                            },
                            type: "POST",
                            success: function( data ) {
                                $('#generation').html(data);
                            }
                        });
                        $('#generation').focus();
                    }

                }
            }).bind("keypress", function(e) {
                if(e.which == 13){
                    setTimeout(function(){
                        $('#model').focusout();
                    },500);
                 }else{
                    $('input[name="model_id"]').val('');
                    $('#generation').html('');
                 }
            });

            $('.spinner').TouchSpin({
                min: 0,
                max: 12,
                stepinterval: 1,
                prefix: ' '
            }).each(function(){
                var desc = $(this).attr('desc');
                $(this).prev().html(desc).css('border-right','0px');
            });

            $('#if_leasing').on('change', function(){
                if( $(this).val() == 1) {
                    $('#owner_id_group').hide();
                    $('#owner_company_name').show();
                }else {
                    $('#owner_company_name').hide();
                    $('#owner_id_group').show();
                }
            }).change();

            $('#modal').on('click', '#add-user', function(){
                var btn = $(this);
                btn.attr('disabled', 'disabled');
                if($('#dialog-form').valid()){
                    $.ajax({
                        type: "POST",
                        url: $('#dialog-form').prop( 'action' ),
                        data: $('#dialog-form').serialize(),
                        assync:false,
                        cache:false,
                        success: function( data ) {
                            if(data.code == 0)
                            {
                                $('#get_search_user').val(data.user.value);
                                $('input[name="vmanage_user_id"]').val(data.user.id);
                                $('#modal .modal-body').html('');
                                $('#modal').modal('hide');
                            }else{
                                $('#modal .modal-body').html(data.message);
                            }
                        },
                        dataType: 'json'
                    });
                }
                return false;
            });

            $('#modal').on('click', '#add-owner', function(){
                var btn = $(this);
                btn.attr('disabled', 'disabled');
                if($('#dialog-form').valid()){
                    $.ajax({
                        type: "POST",
                        url: $('#dialog-form').prop( 'action' ),
                        data: $('#dialog-form').serialize(),
                        assync:false,
                        cache:false,
                        success: function( data ) {
                            if(data.code == 0)
                            {
                                $('#owner_id').append('<option value="'+data.owner.id+'" selected>'+data.owner.value+'</option>');
                                $('#modal .modal-body').html('');
                                $('#modal').modal('hide');
                            }else{
                                $('#modal .modal-body').html(data.message);
                            }
                        },
                        dataType: 'json'
                    });
                }
                return false;
            });

            $('#get_search_user').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ URL::action('VmanageVehicleInfoController@postSearchUser') }}",
                        data: {
                            term: request.term,
                            vmanage_company_id: "{{ $company->id }}",
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            if (jQuery.isEmptyObject(data))
                            {
                                $.notify({
                                    icon: "fa fa-thumbs-down",
                                    message: 'Nie znaleziono użytkownika w bazie'
                                },{
                                    type: 'danger',
                                    placement: {
                                        from: 'bottom',
                                        align: 'right'
                                    },
                                    delay: 2500,
                                    timer: 500
                                });
                            }

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
                    if(ui.item.id != $('input[name="vmanage_user_id"]').val())
                    {
                        $('input[name="vmanage_user_id"]').val(ui.item.id);
                    }
                }
            }).bind("keypress", function(e) {
                if(e.which == 13){
                    setTimeout(function(){
                        $('#get_search_user').focusout();
                    },500);
                }else{
                    $('input[name="vmanage_user_id"]').val('');
                }
            });

            $('#add_new_client').on('click', function(){
                $('#search_client').remove();
                $('#create_new_client').show();
            });

            $('#get_search_client').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "<?php echo  URL::action('VmanageVehicleInfoController@postSearchClient');?>",
                        data: {
                            term: request.term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            if (jQuery.isEmptyObject(data))
                                $('#client_alert').show();

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
                    if(ui.item.id != $('input[name="client_id"]').val())
                    {
                        $('input[name="client_id"]').val(ui.item.id);
                    }
                }
            }).bind("keypress", function(e) {
                if(e.which == 13){
                    setTimeout(function(){
                        $('#get_search_client').focusout();
                    },500);
                }else{
                    $('input[name="client_id"]').val('');
                    $('#client_alert').hide();
                }
            });

      });

    </script>

@stop

