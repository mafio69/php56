<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie modelu dla marki {{ $brand->name }}</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('/settings/brand/'.$brand->id.'/models/store') }}" method="post"  id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Nazwa modelu:</label>
                	{{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa modelu'))  }}
                </div>
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Dodaj</button>
</div>