<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::action('VmanageVehicleInfoController@postInjuryVehicle', [$vehicle->id, $injury_id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Nr rejetracyjny:</label>
                    {{ Form::text('registration', $vehicle->registration, array('class' => 'form-control upper required', 'placeholder' => 'rejestracja', 'required'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Nr umowy leasingowej:</label>
                    {{ Form::text('nr_contract', $vehicle->nr_contract, array('class' => 'form-control upper required', 'placeholder' => 'nr umowy leasingowej', 'required'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Nr VIN:</label>
                    {{ Form::text('vin', $vehicle->vin, array('class' => 'form-control upper ', 'placeholder' => 'nr VIN'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label>Typ pojazdu:</label>
                    {{ Form::select('type', array('1' => 'osobowy', '2' => 'ciężarowy'), ($vehicle->brand) ? $vehicle->brand->typ : null, array('id'=>'type', 'class' => 'form-control tips required', 'required', 'placeholder' => 'Typ pojazdu'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Marka:</label>
                    {{ Form::text('brand', ($vehicle->brand) ? $vehicle->brand->name : '', array('class' => 'form-control  ', 'id'=>'brand', 'placeholder' => 'marka'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Model:</label>
                    {{ Form::text('model', ($vehicle->model) ? $vehicle->model->name : '', array('class' => 'form-control  ', 'id'=>'model', 'placeholder' => 'model'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Rok produkcji:</label>
                    {{ Form::text('year_production', $vehicle->year_production, array('class' => 'form-control  ', 'placeholder' => 'Rok produkcji'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Data pierwszej rejestracji:</label>
                    {{ Form::text('first_registration', $vehicle->first_registration, array('class' => 'form-control  date', 'placeholder' => 'data pierwszej rejestracji'))  }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    <label>
                        <input type="checkbox" name="cfm" id="cfm" value="1"
                               @if($vehicle->cfm == 1)
                               checked
                                @endif
                        > CFM
                    </label>
                </div>
            </div>
            @if($vehicle->owner->wsdl != '')
                <div class="row marg-btm">
                    <div class="col-sm-12">
                        <label >Samochód rejestrowany w AS:</label>
                        <select name="register_as" class="form-control">
                            <option value="0" @if($vehicle->register_as == 0) selected @endif>nie</option>
                            <option value="1" @if($vehicle->register_as == 1) selected @endif>tak</option>
                        </select>
                    </div>
                </div>
            @endif
        </div>
        {{ Form::hidden('brand_id', $vehicle->brand_id) }}
        {{ Form::hidden('model_id', $vehicle->model_id) }}

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).ready(function(){
            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd"});
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

    });

</script>