<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych technicznych pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::action('VmanageVehicleInfoController@postTechnicalInfo', [$vehicle->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label class="control-label">Marka pojazdu:</label>
                    {{ Form::select('type', array('1' => 'osobowy', '2' => 'ciężarowy'), ($vehicle->brand) ? $vehicle->brand->typ : null, array('id'=>'type', 'class' => 'form-control tips required', 'required', 'placeholder' => 'Typ pojazdu'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Marka pojazdu:</label>
                    {{ Form::text('brand', ($vehicle->brand) ? $vehicle->brand->name : null, array('id'=>'brand', 'class' => 'form-control  ', 'required', 'placeholder' => 'marka pojazdu'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Model pojazdu:</label>
                    {{ Form::text('model', ($vehicle->model) ? $vehicle->model->name : null, array('id'=>'model', 'class' => 'form-control  ', 'required', 'placeholder' => 'model pojazdu'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Generacja modelu:</label>
                    {{ Form::select('generation_id', $generations, $vehicle->generation_id, array('id'=>'generation', 'class' => 'form-control tips', 'placeholder' => 'generacja modelu', 'title' => 'generacja modelu'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Wersja:</label>
                    {{ Form::text('version', $vehicle->version, array('id'=>'version', 'class' => 'form-control'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="year_production">Rok produkcji:</label>
                    {{ Form::text('year_production', $vehicle->year_production , ['class' => 'form-control'])}}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Nadwozie:</label>
                    {{ Form::select('car_category_id', $car_category, $vehicle->car_category_id, array('id'=>'car_category', 'class' => 'form-control tips', 'placeholder' => 'typ nadwozia', 'title' => 'typ nadwozia'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Liczba drzwi:</label>
                    {{ Form::text('doors_nb', $vehicle->doors_nb, array('id'=>'doors_nb', 'class' => 'form-control tips spinner', 'required', 'desc' => 'liczba drzwi', 'title' => 'liczba drzwi'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Typ silnika:</label>
                    {{ Form::select('car_engine_id', $engines, $vehicle->car_engine_id, array('id'=>'car_engine', 'class' => 'form-control tips',  'title' => 'typ silnika'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Pojemność silnika:</label>
                    {{ Form::text('engine_capacity', $vehicle->engine_capacity, array('id'=>'engine_capacity', 'class' => 'form-control tips', 'title' => 'pojemność silnika w cm szcześciennych'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Moc silnika:</label>
                    {{ Form::text('horse_power', $vehicle->horse_power, array('id'=>'horse_power', 'class' => 'form-control tips', 'title' => 'moc silnika w KM'))  }}
                </div>
                <div class="col-sm-12">
                    <hr/>
                </div>
                <div class="form-group col-sm-12 marg-btm text-center">
                    <label>
                        <input type="checkbox" name="update_all" value="1"> Zaktualizuj do wszystkich szkód na pojeździe
                    </label>
                </div>
            </div>
        </div>
        {{ Form::hidden('brand_id', $vehicle->brand_id) }}
        {{ Form::hidden('model_id', $vehicle->model_id) }}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..." id="set">Zapisz</button>
</div>

<script type="text/javascript">
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

</script>