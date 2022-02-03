<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przywracanie umowy {{ $agreement->nr_contract}}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/manage-dialog/set-restore', [$agreement->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row" id="withdraw_reason">
                <div class="col-sm-12">
                    <label>Podaj przyczynę przywrócenia umowy:</label>
                    {{ Form::textarea('restore_reason','', ['class' => 'form-control'])}}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa przywracanie..." id="set">Wycofaj</button>
</div>
