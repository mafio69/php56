<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dane właściela</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="form-group">
        <label>Nazwa:</label>
        <p class="form-control">{{ $owner->contractor_name }}</p>
    </div>
    <div class="form-group">
        <label>NIP:</label>
        <p class="form-control">{{ $owner->contractor_nip }}</p>
    </div>
    <div class="form-group">
        <label>Regon:</label>
        <p class="form-control">{{ $owner->contractor_regon }}</p>
    </div>
    <div class="form-group">
        <label>Kod klienta:</label>
        <p class="form-control">{{ $owner->contractor_code_client }}</p>
    </div>

    <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
    <div class="form-group">
        <label>Kod pocztowy:</label>
        <p class="form-control">{{ $owner->contractor_office_post_code }}</p>
    </div>
    <div class="form-group">
        <label>Miasto:</label>
        <p class="form-control">{{ $owner->contractor_office_city }}</p>
    </div>
    <div class="form-group">
        <label>Ulica:</label>
        <p class="form-control">{{ $owner->contractor_office_street }}</p>
    </div>

    <h4 class="inline-header"><span>Adres kontaktowy:</span></h4>
    <div class="row">
        <div class="col-md-6 ">
            <label>Kod pocztowy:</label>
            <p class="form-control">{{ $owner->contractor_office_correspondence_post_code }}</p>
        </div>
        <div class="col-md-6 ">
            <label>Miasto:</label>
            <p class="form-control">{{ $owner->contractor_office_correspondence_city }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <label>Ulica:</label>
            <p class="form-control">{{ $owner->contractor_office_correspondence_street }}</p>
        </div>
    </div>

    <div class="form-group">
        <label>Telefon:</label>
        <p class="form-control">{{ implode($owner->contractor_office_phone, ', ') }}</p>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <p class="form-control">{{ $owner->contractor_office_email }}</p>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
