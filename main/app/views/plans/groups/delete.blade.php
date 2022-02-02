<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" >Usuwanie grupy programu</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('plan/groups/delete', [$plan_group->id]) }}" method="post"  id="dialog-form">
            <div class="form-group">
                Potwierdź usunięcie grupy programu <strong>{{ $plan_group->name }}</strong> z systemu.
                {{Form::token()}}
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>
