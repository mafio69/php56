<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja przedmiotu umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-dialog/update-object', [$object->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <div class="form-group">
                <label>Nazwa</label>
                {{ Form::text('name', $object->name, array('class' => 'form-control required', 'placeholder' => 'nazwa przedmiotu umowy')) }}
            </div>
            <div class="form-group">
                <label>Kategoria</label>
                {{ Form::select('object_assetType_id', $categories, $object->object_assetType_id, array('class' => 'form-control  ')) }}
            </div>
            <div class="form-group">
                <label>Wart. z faktury netto przedm. umowy pożyczki</label>
                {{ Form::text('net_value', $object->net_value, array('class' => 'form-control  number currency_input')) }}
            </div>
            <div class="form-group">
                <label>Wart. brutto przedm. umowy pożyczki</label>
                {{ Form::text('gross_value', $object->gross_value, array('class' => 'form-control  number currency_input')) }}
            </div>
        </fieldset>
        {{Form::token()}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa zapisywanie..." id="set">Zapisz</button>
</div>
