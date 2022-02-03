<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisz prowadzącego do sprawy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.set', ['setAssignLeader', $injury_id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <label class="control-label">Wybierz użytkownika</label>
            {{ Form::select('leader_id', $users, null, ['class' => 'form-control']) }}
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>
