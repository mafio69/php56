<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dezaktywowanie klucza</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/api/modules/disactivate-key', [$apiKey->id]) }}" method="post" id="dialog-form">
            <fieldset>
                Potwierdź dezaktywowanie klucza API
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa wykonywanie">Dezaktywuj</button>
</div>
