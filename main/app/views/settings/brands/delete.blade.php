<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie marki samochodu</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('brands-delete', array($brand->id)) }}" method="post"  id="edit-brand-form"> 
            
            <fieldset>
                Czy na pewno usunąć {{$brand->name}}?
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save-brand">Usuń</button>
</div>