@extends('layouts.main')

@section('header')

Edycja pojazdu dla firmy {{ $company->name }}

@stop

@section('main')

@include('modules.flash_notification')

{{ Form::open(array('url' => URL::route('vmanage.post', array('vehicles', 'update', $vehicle->id)))) }}
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
		</div>
	</div>
    <input type="hidden" name="company_id" value="{{ $company->id }}"/>
	<div class="row">

        <div class="form-group">
            <h4 class="inline-header"><span>Dane techniczne pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Typ pojazdu:</label>
                        {{ Form::select('type', array('1' => 'osobowy', '2' => 'ciężarowy'), $vehicle->brand->typ , array('id'=>'type', 'class' => 'form-control tips required', 'required', 'placeholder' => 'Typ pojazdu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Marka pojazdu:</label>
                        {{ Form::text('brand', ($vehicle->brand) ? $vehicle->brand->name : '', array('id'=>'brand', 'class' => 'form-control  ', 'required', 'placeholder' => 'marka pojazdu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Model pojazdu:</label>
                        {{ Form::text('model', ($vehicle->model) ? $vehicle->model->name : '', array('id'=>'model', 'class' => 'form-control  ', 'required', 'placeholder' => 'model pojazdu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Generacja modelu:</label>
                        {{ Form::select('generation_id', $generations, $vehicle->generation_id, array('id'=>'generation', 'class' => 'form-control tips', 'placeholder' => 'generacja modelu', 'title' => 'generacja modelu'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Wersja:</label>
                        {{ Form::text('version', $vehicle->version, array('id'=>'version', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Nadwozie:</label>
                        {{ Form::select('car_category_id', $car_category, $vehicle->car_category_id, array('id'=>'car_category', 'class' => 'form-control tips', 'placeholder' => 'typ nadwozia', 'title' => 'typ nadwozia'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Liczba drzwi:</label>
                        {{ Form::text('doors_nb', $vehicle->doors_nb, array('id'=>'doors_nb', 'class' => 'form-control tips spinner', 'desc' => 'liczba drzwi', 'title' => 'liczba drzwi'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Typ silnika:</label>
                        {{ Form::select('car_engine_id', $engines, $vehicle->car_engine_id, array('id'=>'car_engine', 'class' => 'form-control tips',  'title' => 'typ silnika'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Skrzynia biegów:</label>
                        {{ Form::select('car_gearbox_id', $gearboxes, $vehicle->car_gearbox_id, array('id'=>'car_gearbox', 'class' => 'form-control tips', 'title' => 'typ skrzyni biegów'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Pojemność silnika:</label>
                        {{ Form::text('engine_capacity', $vehicle->engine_capacity, array('id'=>'engine_capacity', 'class' => 'form-control tips', 'title' => 'pojemność silnika w cm szcześciennych'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        <label for="brand">Moc silnika:</label>
                        {{ Form::text('horse_power', $vehicle->horse_power, array('id'=>'horse_power', 'class' => 'form-control tips', 'title' => 'moc silnika w KM'))  }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Dane rejestracyjne pojazdu:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group tips" title = 'numer rejestracyjny pojazdu' >
                        <label for="brand">Numer rejestracyjny:</label>
                        {{ Form::text('registration', $vehicle->registration, array('id'=>'registration', 'class' => 'form-control ', 'required'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group tips" title = 'unikalny nr VIN pojazdu'>
                        <label for="brand">Numer VIN:</label>
                        {{ Form::text('vin', $vehicle->vin, array('id'=>'vin', 'class' => 'form-control tips',  'required'))  }}
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
                            {{ Form::select('vmanage_user_id', $users, $vehicle->vmanage_user_id, array('id'=>'vmanage_user_id', 'class' => 'form-control tips', 'title' => 'Bieżący użytkownik pojazdu'))  }}
                            <span class="input-group-addon btn tips modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::route('vmanage.get', array('users', 'create', $company->id)) }}" title="utwórz nowego użytkownika"><i class="fa fa-plus"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Przebieg deklarowany:</label>
                        {{ Form::text('declare_mileage', $vehicle->declare_mileage, array('id'=>'declare_mileage', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Przebieg bieżący:</label>
                        {{ Form::text('actual_mileage', $vehicle->actual_mileage, array('id'=>'actual_mileage', 'class' => 'form-control'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Termin badania technicznego:</label>
                        {{ Form::text('technical_exam_date', $vehicle->technical_exam_date, array('id'=>'technical_exam_date', 'class' => 'form-control date'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group " >
                        <label for="brand">Termin przeglądu:</label>
                        {{ Form::text('servicing_date', $vehicle->servicing_date, array('id'=>'servicing_date', 'class' => 'form-control date'))  }}
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
    {{ Form::hidden('brand_id', $vehicle->brand_id) }}
    {{ Form::hidden('model_id', $vehicle->model_id) }}
    {{ Form::hidden('generation_id', $vehicle->generation_id) }}
{{ Form::close() }}



@stop

@section('headerJs')
	@parent
	<script type="text/javascript" >

      $(document).ready(function(){
      		$("form").validate();

      		$('form').bind("keyup keypress", function(e) {
			  return e.which !== 13
			});

			$('#modal').on('click', '#add-user', function(){
                btn = $(this);
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
                            $('#vmanage_user_id').append('<option value="'+data.user.id+'" selected>'+data.user.value+'</option>');
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

			$("form").submit(function(e) {
			    var self = this;
			    e.preventDefault();
                btn = $('#store');
                btn.attr('disabled', 'disabled');

			    if($("form").valid()){
                    self.submit();
			    }else{
                    btn.removeAttr('disabled');
			    }
			    return false; //is superfluous, but I put it here as a fallback
			});

			$('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true, dateFormat: 'yy-mm-dd'});

            $('#type').on('change', function(){
                $('input[name="brand_id"]').val('');
                $('input[name="model_id"]').val('');
                $('#model').val('');
                $('#brand').val('').focus();
                $('#generation').html('');
                $.ajax({
                    url: "<?php echo  URL::route('vmanage.get', array('vehicles', 'getCategories'));?>",
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
                        url: "<?php echo  URL::route('vmanage.get', array('vehicles', 'getBrands'));?>",
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
                        url: "<?php echo  URL::route('vmanage.get', array('vehicles', 'getModels'));?>",
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
                            url: "<?php echo  URL::route('vmanage.get', array('vehicles', 'getGenerations'));?>",
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
                desc = $(this).attr('desc');
                $(this).prev().html(desc).css('border-right','0px');
            });


      });

    </script>

@stop

