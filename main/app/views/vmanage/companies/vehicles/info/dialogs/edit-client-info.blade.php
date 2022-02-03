<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Aktualizacje danych klienta</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::action('VmanageVehicleInfoController@postUpdateClient', [$vehicle->id]) }}" method="post"  id="dialog-form">
        {{ Form::token() }}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Nazwa:</label>
                    {{ Form::text('name', $vehicle->client->name, array('class' => 'form-control  required', 'id'=>'name',  'placeholder' => 'nazwa')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >NIP:</label>
                    {{ Form::text('NIP', $vehicle->client->NIP, array('class' => 'form-control  required', 'id'=>'NIP',  'placeholder' => 'NIP')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >REGON:</label>
                    {{ Form::text('REGON', $vehicle->client->REGON, array('class' => 'form-control  ', 'id'=>'REGON',  'placeholder' => 'REGON')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod klienta:</label>
                    {{ Form::text('firmID', $vehicle->client->firmID, array('class' => 'form-control  ', 'id'=>'firmID',  'placeholder' => 'kod klienta')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('registry_post', $vehicle->client->registry_post, array('class' => 'form-control  ', 'id'=>'registry_post',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('registry_city', $vehicle->client->registry_city, array('class' => 'form-control  ', 'id'=>'registry_city',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('registry_street', $vehicle->client->registry_street, array('class' => 'form-control  ', 'id'=>'registry_street',  'placeholder' => 'ulica')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Dane kontaktowe:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('correspond_post', $vehicle->client->correspond_post, array('class' => 'form-control  ', 'id'=>'correspond_post',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('correspond_city', $vehicle->client->correspond_city, array('class' => 'form-control  ', 'id'=>'correspond_city',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('correspond_street', $vehicle->client->correspond_street, array('class' => 'form-control  ', 'id'=>'correspond_street',  'placeholder' => 'ulica')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Tefelon:</label>
                    {{ Form::text('phone', $vehicle->client->phone, array('class' => 'form-control  ', 'id'=>'phone',  'placeholder' => 'telefon')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Email:</label>
                    {{ Form::text('email', $vehicle->client->email, array('class' => 'form-control email ', 'id'=>'email',  'placeholder' => 'email')) }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="Trwa zapisywanie zmian...">Zapisz</button>
</div>
