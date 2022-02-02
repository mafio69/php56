<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przenoszenia serwisu {{ $company->name }}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('companies/move', [$company->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="form-group">
            <label>Wybierz nową grupę serwisową:</label>
            {{ Form::select('company_group_id', $groups, null, ['class' => 'form-control']) }}
        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Przenieś</button>
</div>