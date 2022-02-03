<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przywrócenie szkody</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('dos.other.injuries.set', array('setRestore', $id)) }}" method="post"  id="dialog-injury-form">
        {{Form::token()}}
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Etap przywrócenia:</label>
                    <select name="step" class="form-control">
                        <option value="0">zarejestrowana</option>
                        <option value="10">w obsłudze</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 marg-btm">
                    <label>Przyczyna przywrócenia:</label>
                    {{ Form::textarea('content', '', array('class' => 'form-control required', 'id'=>'content', 'maxlength' => '512',  'placeholder' => 'wprowadź przyczynę przywrócenia szkody')) }}
                </div>
            </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
</div>
