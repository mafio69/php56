<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dokumenty wygenerowanie {{ $stage->name }}</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::route('routes.post', array('settings', 'stages', 'updateDocumentTypes', $stage->id, $type)) }}" method="post" class="form-inline"  id="dialog-form">
        <div class="row">
            @foreach($documentTypes as $documentType_id => $documentType)
                <div class="col-sm-12 col-md-6">
                    <div class="checkbox ">
                        <label>
                            <input type="checkbox" name="types[]" value="{{$documentType_id}}"

                            @if( $stage->documentTypes->contains($documentType_id) )
                                checked="checked"
                            @endif

                            />
                            {{$documentType}}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        {{ Form::token() }}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz zmiany</button>
</div>
