<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja szablonu</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.sms-templates.update', $template->id) }}" method="post"  id="dialog-form">

            <fieldset>
                <div class="form-group">
                    <label>Nazwa szablonu:</label>
                    {{ Form::text('name', $template->name, array('class' => 'form-control required', 'placeholder' => 'nazwa szablonu', 'autofocuse' => ''))  }}
                </div>
                <div class="form-group">
                    <label>Treść szablonu:</label>
                    {{ Form::textarea('body', $template->body, array('class' => 'form-control required', 'placeholder' => 'treść szablonu', 'autofocuse' => ''))  }}
                </div>
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="submit">Zapisz</button>
</div>