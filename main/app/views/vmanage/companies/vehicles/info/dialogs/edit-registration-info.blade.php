<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych rejestracyjnych pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::action('VmanageVehicleInfoController@postRegistrationInfo', [$vehicle->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Numer rejestracyjny:</label>
                    {{ Form::text('registration', $vehicle->registration, array('id'=>'registration', 'class' => 'form-control ', 'required'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Numer VIN:</label>
                    {{ Form::text('vin', $vehicle->vin, array('id'=>'vin', 'class' => 'form-control tips',  'required'))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Nr umowy leasingowej:</label>
                    {{ Form::text('nr_contract', $vehicle->nr_contract, array('id'=>'nr_contract', 'class' => 'form-control '))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="brand">Status umowy:</label>
                    {{ Form::text('contract_status', $vehicle->contract_status, array('id'=>'contract_status', 'class' => 'form-control '))  }}
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label for="first_registration">Data pierwszej rejestracji:</label>
                    {{ Form::text('first_registration', ($vehicle->first_registration == '0000-00-00')?'':$vehicle->first_registration, ['class' => 'form-control date'])}}
                </div>

                <div class="form-group col-sm-12 marg-btm text-center">
                    <label>
                        <input type="checkbox" name="update_all" value="1"> Zaktualizuj dane do wszystkich szkód na pojeździe
                    </label>
                </div>
                <div class="col-sm-12">
                    <hr/>
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label>
                        <input type="checkbox" name="cfm" id="cfm" value="1"
                               @if($vehicle->cfm == 1)
                               checked
                                @endif
                        > CFM
                    </label>
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label>
                        <input type="checkbox" name="if_vip" id="if_vip" value="1"
                               @if($vehicle->if_vip == 1)
                               checked
                                @endif
                        > pojazd VIP
                    </label>
                </div>
                <div class="form-group col-sm-12 col-md-6 marg-btm">
                    <label>
                        <input type="checkbox" name="if_return" id="if_return" value="1"
                               @if($vehicle->if_return == 1)
                               checked
                                @endif
                        > Pojazd zwrócony
                    </label>
                </div>

                <div class="form-group col-sm-12 marg-btm text-center">
                    <label>
                        <input type="checkbox" name="update_all_vip" value="1"> Zaktualizuj do wszystkich szkód na pojeździe
                    </label>
                </div>
            </div>
        </div>
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
</script>
