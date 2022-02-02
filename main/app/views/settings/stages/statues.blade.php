<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dokumenty wgrywane {{ $stage->name }}</h4>
</div>
<div class="modal-body">
    <form action="{{ URL::route('routes.post', array('settings', 'stages', 'updateStatues', $stage->id, $type)) }}" method="post" class="form-inline"  id="dialog-form">
        <div class="row">
            @foreach($statues as $status_id => $status)
                <div class="col-sm-12 col-md-6">
                    <div class="checkbox ">
                        <label>
                            <input type="checkbox" name="types[]" value="{{$status_id}}"

                            @if( $stage->steps()->contains($status_id))
                                checked="checked"
                            @endif

                            />
                            {{$status}}
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
