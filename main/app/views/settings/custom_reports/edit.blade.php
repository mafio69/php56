<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja użytkowników dla <i>{{ $report->desc }}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        {{ Form::open(array('url' => URL::route('settings.custom_reports', array('update', $report->id)), 'id' => 'dialog-form')) }}
            @foreach($users as $user)
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="users[]" value="{{ $user->id }}"
                        @if(isset($reportUsers[$user->id]))
                           checked
                        @endif> {{ $user->name }}
                </label>
            </div>
            @endforeach
        {{ Form::close() }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
</div>