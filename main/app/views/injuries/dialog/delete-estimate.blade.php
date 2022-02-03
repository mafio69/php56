<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie kosztorysu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-setDeleteEstimate', array($estimate->id)) }}" method="post"  id="dialog-form">

        {{Form::token()}}
        Potwierdź usunięcię kosztorysu z systemu.

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>
