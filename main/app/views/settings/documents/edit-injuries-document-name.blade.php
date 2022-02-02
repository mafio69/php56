<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja {{ $documentType->name }}</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::route('settings.documents', array('updateInjuriesDocumentName', $documentType->id)) }}" method="post" id="dialog-form">
        {{Form::token()}}
        <div class="row">
            <div class="col-sm-12 marg-top">
                <div class="form-group">
                    <label>Nazwa dokumentu</label>
                    {{ Form::text('name', $documentType->name, ['class' => 'form-control required' , 'required'] ) }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..." id="set">Zapisz</button>
</div>
