<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie modelu samochodu</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('/settings/brand/'.$model->id.'/models/destroy')}}" method="post"  id="dialog-form">
            
            <fieldset>
                Czy na pewno usunąć {{$model->name}}?
                {{Form::token()}}	
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>