<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie pojazdu <i>{{$vehicle->registration}}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::action('VmanageVehiclesController@postDestroy', [$vehicle->id]) }}" method="post"  id="dialog-form">

            <fieldset>
                <div class="form-group">
                    <label>Potwierdź usunięcie pojazdu {{ $vehicle->registration }}.</label>
                    {{Form::token()}}
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa usuwanie" id="set">Usuń</button>
</div>
