<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie pisma</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('routes.post', ['injuries', 'card', 'appendLetter', $letter_id, $injury_id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12">
                    Potwierdź przypisanie pisma do szkody.
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa przypisywanie..." id="set">Przypisz</button>
</div>

