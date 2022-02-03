<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie oddziału <i>{{ $office->name }}</i></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        {{ Form::open(array('url' => URL::route('idea.offices.destroy', array($office->id)), 'method' => 'DELETE', 'id' => 'dialog-form')) }}
            <fieldset>
                Czy na pewno usunąć <i>{{$office->name}}</i>?
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>