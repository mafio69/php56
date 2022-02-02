<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Szablony dokumentów dla <i>{{ $owner->name }}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        {{ Form::open(array('url' => url('settings/document-templates/update', [$owner->id]), 'id' => 'dialog-form')) }}
            <div class="form-group">
                <label>Szablon główny (lub umowa '/ROK')</label>
                {{ Form::select('document_template_id', $documentTemplates, $owner->document_template_id, ['class' => 'form-control']) }}
            </div>
        <div class="form-group">
            <label>Szablon warunkowy</label>
            {{ Form::select('conditional_document_template_id', $documentTemplates, $owner->conditional_document_template_id, ['class' => 'form-control']) }}
        </div>
        {{ Form::close() }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
</div>