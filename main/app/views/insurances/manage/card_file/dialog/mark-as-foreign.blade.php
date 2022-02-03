<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana statusu umowy</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-dialog/mark-as-foreign-agreement', [$agreement->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <label>{{$agreement->if_foreign == 1 ? "Anuluj status umowy obca" : "Potwierdź zmianę statusy umowy na obca" }}.</label>
        </fieldset>
        {{Form::token()}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa zmiana..." id="set">Potwierdź</button>
</div>
