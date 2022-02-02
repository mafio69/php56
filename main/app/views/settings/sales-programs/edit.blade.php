<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ url('settings/sales-programs/update', $dls_program->id) }}" method="post"  id="dialog-form">
            {{Form::token()}}
            <fieldset>
                <div class="form-group">
                  <label>Kod programu</label>
                    {{ Form::text('name_key', $dls_program->name_key, array('class' => 'form-control required', 'placeholder' => 'Kod programu')) }}
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..."  id="set">Zapisz</button>
</div>
