<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie ubezpieczalni</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('insurance_companies-delete', array($insurance_company->id)) }}" method="post"  id="dialog-form">
            
            <fieldset>
                Czy na pewno usunąć ubezpieczalnię {{$insurance_company->name}}?
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>