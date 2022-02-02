<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie użytkownika do grup</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/append-groups', [$user->id]) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Wybierz grupę:</label>

                    @foreach($groups as $group)
                        <div class="input-group marg-top-min">
                              <span class="input-group-addon">
                                <input type="checkbox" value="{{ $group->id }}" name="groups[]"
                                   @if($user->groups->contains($group->id)) checked @endif
                                >
                              </span>
                            <span class="form-control" >{{ $group->name }}</span>
                        </div>
                    @endforeach
                </div>
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Zapisywanie..."  id="set">Zapisz zmiany</button>
</div>
