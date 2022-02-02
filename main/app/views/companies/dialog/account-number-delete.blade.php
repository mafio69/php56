<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Serwis {{ $number->company->name }}</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('company/account-numbers/delete', [$number->id]) }}" method="post" id="dialog-form">
                <div class="form-group">
                    <label>Potwierdź usunięcie numeru konta {{$number->account_number}}.</label>
                    {{Form::token()}}
                </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>

