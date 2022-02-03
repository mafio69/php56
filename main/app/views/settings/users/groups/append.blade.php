<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie użytkownika do grupy <i>{{ $group->name }}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/user/groups/append', [$group->id]) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <label>Wybierz użytkownika:</label>
                    <select name="user_id" class="form-control required">
                        <option value="0">---</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                                @if( $user->group )
                                    - {{ $user->group->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
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
