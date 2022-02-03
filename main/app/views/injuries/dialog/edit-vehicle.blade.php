<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-postEditVehicle', array($id)) }}" method="post"  id="dialog-injury-form">
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
                    {{ Form::text('VIN', $vehicle->VIN, array('class' => 'form-control upper ', 'placeholder' => 'nr VIN'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Marka:</label>
                    {{ Form::text('brand', $vehicle->brand, array('class' => 'form-control  ', 'placeholder' => 'marka'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Model:</label>
                    {{ Form::text('model', $vehicle->model, array('class' => 'form-control  ', 'placeholder' => 'model'))  }}
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
                    <label >Silnik:</label>
                    {{ Form::text('engine', $vehicle->engine, array('class' => 'form-control  ', 'placeholder' => 'silnik'))  }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Przebieg:</label>
                    {{ Form::text('mileage', $vehicle->mileage, array('class' => 'form-control  ', 'placeholder' => 'przebieg'))  }}
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

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).ready(function(){
            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd"});
        });

    });

</script>