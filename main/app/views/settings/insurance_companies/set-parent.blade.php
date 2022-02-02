<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Parowanie ubezpieczalni</h4>
</div>
<div class="modal-body">
    @if($insurance_company->parent_id)
            <span class="label label-warning system-warning">
                Sparowano z {{$insurance_company->parent?$insurance_company->parent->name:null}}<br>
            </span>
    @endif
    <div class="panel-body">
        <form action="{{ URL::route('insurance_companies-set-parent', [$insurance_company->id]) }}" method="post"  id="dialog-form">
        <h5>Ustaw nadrzędną ubezpieczalnie dla {{$insurance_company->name}}</h5>
            <fieldset>
                <select type="input" name="parent_id">
                    <option value="{{null}}">-- BRAK --</option>
                    @foreach ($insurance_companies as $object)
                        <option value="{{$object->id}}" {{$object->id == $insurance_company->parent_id?' selected':''}}>{{$object->name}}</option>
                    @endforeach
                </select>
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Paruj</button>
</div>