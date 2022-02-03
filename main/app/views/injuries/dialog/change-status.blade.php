<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przepinanie szkody</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-setChangeStatus', array($injury->id)) }}" method="post"  id="dialog-form">

        {{Form::token()}}
        <label >Wybierz nowy status:</label>
        {{ Form::select('step', $statuses, null, ['class' => 'form-control']) }}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Potwierdź</button>
</div>