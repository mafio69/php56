<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" >Usuwanie serwisu z grupy</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('company/garages/delete-branch-plan-group', [$group->id]) }}" method="post"  id="dialog-form">
            <div class="form-group">
                Potwierdź usunięcie  <strong>{{ $group->branch->short_name }}</strong> z grupy.
                {{Form::token()}}
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>
