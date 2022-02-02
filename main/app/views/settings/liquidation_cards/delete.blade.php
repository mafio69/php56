<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie karty <i>{{$card->number}}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.liquidation_cards', array('destroy', $card->id)) }}" method="post"  id="dialog-form">

            <fieldset>
                <div class="form-group">
                    <label>Potwierdź usunięcie karty.</label>
                {{Form::token()}}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>
