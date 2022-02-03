<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja <i>{{$parameter->name}}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('idea-set', array($owner_id, $parameter_id)) }}" method="post"  id="edit-form">

            <fieldset>
                <div class="form-group">
                    <label>{{$parameter->name}}:</label>
                	{{ Form::text('value', (isset($setting->value)) ? $setting->value : '', array('class' => 'form-control required', 'autofocuse' => ''))  }}
                </div>
                               
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save">Zapisz zmiany</button>
</div>