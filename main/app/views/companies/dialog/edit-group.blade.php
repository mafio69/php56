<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja grupy warsztatów</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('companies/update-group', [$group->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <label >Nazwa grupy</label>
            {{ Form::text('name', $group->name, ['class' => 'form-control required']) }}
        </div>
        <label >Przypisani właściciele:</label>

        <div class="form-group">
            @foreach($owners as $owner_id => $owner)
                <div class="col-sm-6">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="owners[]" value="{{ $owner_id }}" @if($group->owners->contains($owner_id)) checked @endif>
                            {{ $owner }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
</div>