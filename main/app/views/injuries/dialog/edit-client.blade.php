<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych klienta</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-setEditInjuryClient', array($id)) }}" method="post"  id="dialog-form">

        {{ Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label >Nazwa:</label>
                    {{ Form::text('name', $injury->client->name, array('class' => 'form-control  required', 'id'=>'name',  'placeholder' => 'nazwa')) }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >NIP:</label>
                    {{ Form::text('NIP', $injury->client->NIP, array('class' => 'form-control  required', 'id'=>'NIP',  'placeholder' => 'NIP')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >REGON:</label>
                    {{ Form::text('REGON', $injury->client->REGON, array('class' => 'form-control  ', 'id'=>'REGON',  'placeholder' => 'REGON')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod klienta:</label>
                    {{ Form::text('firmID', $injury->client->firmID, array('class' => 'form-control  ', 'id'=>'firmID',  'placeholder' => 'Kod klienta')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('registry_post', $injury->client->registry_post, array('class' => 'form-control  ', 'id'=>'registry_post',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('registry_city', $injury->client->registry_city, array('class' => 'form-control  ', 'id'=>'registry_city',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('registry_street', $injury->client->registry_street, array('class' => 'form-control  ', 'id'=>'registry_street',  'placeholder' => 'ulica')) }}
                </div>
            </div>
            <h4 class="inline-header"><span>Dane kontaktowe:</span></h4>
            <div class="row">
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Kod pocztowy:</label>
                    {{ Form::text('correspond_post', $injury->client->correspond_post, array('class' => 'form-control  ', 'id'=>'correspond_post',  'placeholder' => 'kod pocztowy')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Miasto:</label>
                    {{ Form::text('correspond_city', $injury->client->correspond_city, array('class' => 'form-control  ', 'id'=>'correspond_city',  'placeholder' => 'Miasto')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Ulica:</label>
                    {{ Form::text('correspond_street', $injury->client->correspond_street, array('class' => 'form-control  ', 'id'=>'correspond_street',  'placeholder' => 'ulica')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Tefelon:</label>
                    {{ Form::text('phone', $injury->client->phone, array('class' => 'form-control  ', 'id'=>'phone',  'placeholder' => 'telefon')) }}
                </div>
                <div class="col-sm-12 col-md-6 marg-btm">
                    <label >Email:</label>
                    {{ Form::text('email', $injury->client->email, array('class' => 'form-control email ', 'id'=>'email',  'placeholder' => 'email')) }}
                </div>
            </div>
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
</div>
