<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przywrócenie szkody</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::route('injuries-setRestoreCompleted', array($id)) }}" method="post"  id="dialog-injury-form">
        {{Form::token()}}
        @if($injury->step == 21)
            <label>Szkoda zostanie przywrócana na status</label><br />
        @else
            <label>Szkoda zostanie przywrócana na status <br />"w obsłudze".</label><br />
        @endif
        {{-- <select name="step" id="step" class="form-control">
                <php
                foreach($steps as $k => $v){
                echo '<option value="'.$v->id.'"';
                echo $v->id==10?"selected=\"selected\"":"";
                echo '>'.$v->name.'</option>';
                ?>
        </select> --}}
        <label >Przyczyna przywrócenia:</label>
        {{ Form::textarea('content', '', array('class' => 'form-control required', 'id'=>'content',  'placeholder' => 'wprowadź przyczynę przywrócenia szkody')) }}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set-injury">Potwierdź</button>
</div>