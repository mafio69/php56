<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana właściciela pojazdu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-postEditVehicleOwner', array($id)) }}" method="post"  id="dialog-injury-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Właściciel:</label>
                    {{ Form::select('owner_id', $owners, $vehicle->owner_id, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="col-sm-12">
                    <label >Samochód rejestrowany w AS:</label>
                    <select name="register_as" class="form-control">
                        <option value="0" @if($vehicle->register_as == 0) selected @endif>nie</option>
                        <option value="1" @if($vehicle->register_as == 1) selected @endif>tak</option>
                    </select>
                </div>
            </div>
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
</div>
