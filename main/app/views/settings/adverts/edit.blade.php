<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja odnośnika reklamy</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.adverts.update', $id) }}" method="post"  id="dialog-form">

            <div class="form-group">
                <label>Adres przekierowania:</label>
                {{ Form::text('url',  $advert->url, array( 'class' => 'form-control'))  }}
            </div>

            {{Form::token()}}

        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="submit">Zapisz zmiany</button>
</div>