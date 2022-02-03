<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja procesów</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.processes.set-node', array($process->id)) }}" method="post"  id="edit-form">

            <div class="form-group">
                <label>Waga procesu:</label>
                {{ Form::text('weight', $process->weight, array('class' => 'form-control required', 'placeholder' => 'waga procesu', 'autofocuse' => ''))  }}
            </div>
            <div class="form-group">
                <label>Limit czasu obsługi [h]:</label>
                {{ Form::text('time_limit', $process->time_limit, array('class' => 'form-control required', 'placeholder' => 'Limit czasu obsługi', 'autofocuse' => ''))  }}
            </div>

            {{Form::token()}}
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save">Zapisz zmiany</button>
</div>