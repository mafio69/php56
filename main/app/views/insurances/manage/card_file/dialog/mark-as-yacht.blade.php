<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Oznaczenie umowy jako jachtowej</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/info-dialog/set-as-yacht-agreement', [$agreement->id]) }}" method="post"  id="dialog-form">
        <fieldset>
            <label>Potwierdź oznaczenie umowy jako jachtowej.</label>
        </fieldset>
        {{Form::token()}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary " data-loading-text="trwa usuwanie..." id="set">Potwierdź</button>
</div>
