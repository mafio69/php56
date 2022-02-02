<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Aktualizacje danych dostawcy</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::action('VmanageVehicleInfoController@postUpdateSeller', [$vehicle->id]) }}" method="post"  id="dialog-form">
        {{ Form::token() }}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Nazwa:</label>
                    {{ Form::text('name', $vehicle->seller->name, array('class' => 'form-control  required', 'id'=>'name',  'placeholder' => 'nazwa')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >NIP:</label>
                    {{ Form::text('nip', $vehicle->seller->nip, array('class' => 'form-control  required', 'id'=>'NIP',  'placeholder' => 'NIP')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Telefon:</label>
                    {{ Form::text('phone', $vehicle->seller->phone, array('class' => 'form-control  ', 'placeholder' => 'telefon')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Adres:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('registry_post', $vehicle->seller->post, array('class' => 'form-control  ',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('registry_city', $vehicle->seller->city, array('class' => 'form-control  ',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('registry_street', $vehicle->seller->street, array('class' => 'form-control  ',   'placeholder' => 'ulica')) }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="Trwa zapisywanie zmian...">Zapisz</button>
</div>
