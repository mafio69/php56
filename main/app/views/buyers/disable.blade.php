<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dezaktywacja nabywcy</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('injuries/buyers/disable', [$buyer->id]) }}" method="post"  id="dialog-form">

            <fieldset>
                <div class="form-group">
                    <label>Potwierdź dezaktywację nabywcy {{ $buyer->name }}.</label>
                    {{Form::token()}}
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Dezaktywuj</button>
</div>
