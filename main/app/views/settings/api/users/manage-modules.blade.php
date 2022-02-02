<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie właściciela do użytkownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/api/users/append-modules', [$user->id]) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <div class="row">
                        @foreach($modules as $module)
                            <div class="col-lg-6">
                                <div class="input-group marg-top-min" style="overflow: hidden;">
                                    <label class="input-group-addon">
                                        <input type="checkbox" value="{{ $module->id }}" name="modules[]"
                                               @if($user->apiModules->contains($module->id)) checked @endif
                                        >
                                    </label>
                                    <span class="form-control" >
                                        {{ $module->name }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
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

