<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuń prowadzącego sprawę</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('injuries/manage/remove-leader', array($injury_id)) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        Potwierdź usunięcie prowadzącego sprawę.
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>