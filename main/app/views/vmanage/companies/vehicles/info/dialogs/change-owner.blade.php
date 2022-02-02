<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana właściciela</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::action('VmanageVehicleInfoController@postChangeOwner', [$vehicle->id]) }}" method="post"  id="dialog-form">
        {{ Form::token() }}

        <div class="row marg-btm" id="search_client">
            <div class="col-sm-12 marg-btm">
                <label>Wybierz z listy nowego właściciela pojazdu:</label>
                <select name="vmanage_company_id" class="form-control">
                    @foreach($vmanage_companies as $vmanage_company)
                        <option value="{{ $vmanage_company->id }}">{{ $vmanage_company->owner->name }} ({{ $vmanage_company->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">Zaktualizuj właściciela w szkodach na etapie:</div>
            @foreach($steps as $step_id => $step)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="{{ $step_id }}" name="steps[]"> {{ $step }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="Trwa wykonywanie...">Zapisz</button>
</div>
