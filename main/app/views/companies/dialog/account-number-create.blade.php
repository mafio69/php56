<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Dodawanie numeru rachunku</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('company/account-numbers/create', [$company->id]) }}" method="post" id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Numer rachunku:</label>
                    {{ Form::text('account_number', '', array('class' => 'form-control required', 'id'=>'account_number', 'required')) }}
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa dodawanie..." id="set">Dodaj</button>
</div>
