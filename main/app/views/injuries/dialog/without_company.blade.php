<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Procedowanie bez serwisu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-setWithoutCompany', array($injury->id)) }}" method="post"
          id="dialog-injury-form">
        {{Form::token()}}
        Potwierdź przeniesienie szkody do etapu 'w obsłudze' bez procedowania serwisu.
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
</div>
