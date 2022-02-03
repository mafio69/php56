<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dane właściela</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
            <div class="form-group">
                <label>Nazwa:</label>
                <p class="form-control">{{ $owner->name }}</p>
            </div>
            @if($owner->old_name)
                <div class="form-group">
                    <label>Dawna nazwa:</label>
                    <p class="form-control">{{ $owner->old_name }}</p>
                </div>
            @endif
            <h4 class="inline-header"><span>Adres:</span></h4>
            <div class="form-group">
                <label>Kod pocztowy:</label>
                <p class="form-control">{{ $owner->post }}</p>
            </div>
            <div class="form-group">
                <label>Miasto:</label>
                <p class="form-control">{{ $owner->city }}</p>
            </div>
            <div class="form-group">
                <label>Ulica:</label>
                <p class="form-control">{{ $owner->street }}</p>
            </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
