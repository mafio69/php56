<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisanie nowego finansującego</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('insurances/manage-actions/change-owner', [$agreement->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 text-danger marg-btm">
                    <label>Dla umowy nr {{ $agreement->nr_contract }} wykryto przypisanie nieaktywnego w systemie finansującego. Proszę wybrać nowego finansującego.</label>
                </div>
                <div class="col-sm-12">
                    {{ Form::select('owner_id', $owners, 0, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wprowadzania zmian..." id="set">Zapisz</button>
</div>

