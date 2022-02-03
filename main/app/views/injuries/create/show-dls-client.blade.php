<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dane klienta</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
            <div class="row">
                <div class="col-md-12 ">
                    <label>Nazwa:</label>
                   <p class="form-control">{{ $client->name }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 ">
                    <label>NIP:</label>
                    <p class="form-control">{{ $client->NIP }}</p>
                </div>
                <div class="col-md-6 ">
                    <label>REGON:</label>
                    <p class="form-control">{{ $client->REGON }}</p>
                </div>
                <div class="col-md-6 ">
                    <label>Kod klienta:</label>
                    <p class="form-control">{{ $client->firmID }}</p>
                </div>
            </div>
            <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
            <div class="row">
                <div class="col-md-6 ">
                    <label>Kod pocztowy:</label>
                    <p class="form-control">{{ $client->registry_post }}</p>
                </div>
                <div class="col-md-6 ">
                    <label>Miasto:</label>
                    <p class="form-control">{{ $client->registry_city }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 ">
                    <label>Ulica:</label>
                    <p class="form-control">{{ $client->registry_street }}</p>
                </div>
            </div>
            <h4 class="inline-header"><span>Adres kontaktowy:</span></h4>
            <div class="row">
                <div class="col-md-6 ">
                    <label>Kod pocztowy:</label>
                    <p class="form-control">{{ $client->correspond_post }}</p>
                </div>
                <div class="col-md-6 ">
                    <label>Miasto:</label>
                    <p class="form-control">{{ $client->correspond_city }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <label>Ulica:</label>
                    <p class="form-control">{{ $client->correspond_street }}</p>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6 ">
                    <label>Telefon:</label>
                    <p class="form-control">{{ $client->phone }}</p>
                </div>
                <div class="col-md-6 ">
                    <label>Email:</label>
                    <p class="form-control">{{ $client->email }}</p>
                </div>
            </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
