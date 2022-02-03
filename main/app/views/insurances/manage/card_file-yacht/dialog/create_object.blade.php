<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Tworzenie przedmiotu umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-dialog/store-object') }}" method="post"  id="dialog-form">
        <fieldset>
            {{ Form::hidden('leasing_agreement_id', $agreement_id) }}
            <div class="form-group">
                <label>Nazwa</label>
                {{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa przedmiotu umowy')) }}
            </div>
            <div class="form-group">
                <label>Producent</label>
                {{ Form::text('producer', $object->producer, array('class' => 'form-control', 'placeholder' => 'producent jachtu')) }}
            </div>
            <div class="form-group">
                <label>Kategoria</label>
                {{ Form::select('object_assetType_id', $categories, '', array('class' => 'form-control  ')) }}
            </div>
            <div class="form-group">
                <label>Wart. z faktury netto przedm. umowy pożyczki</label>
                {{ Form::text('net_value', '', array('class' => 'form-control  number currency_input')) }}
            </div>
            <div class="form-group">
                <label>Wart. brutto przedm. umowy pożyczki</label>
                {{ Form::text('gross_value', '', array('class' => 'form-control  number currency_input')) }}
            </div>
            <div class="form-group">
                <label>Nr fabryczny:</label>
                {{ Form::text('fabric_number', '', array('class' => 'form-control ', 'placeholder' => 'numer fabryczny jachtu')) }}
            </div>
            <div class="form-group">
                <label>Nr rejestracyjny:</label>
                {{ Form::text('registration_number', '', array('class' => 'form-control ', 'placeholder' => 'numer rejestracyjny jachtu')) }}
            </div>
        </fieldset>
        {{Form::token()}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa dodawanie..." id="set">Dodaj</button>
</div>
