<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja czasu pracy w {{ $work_hour->name }}</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::route('settings.hours.set', array($work_hour->id)) }}" method="post"  id="edit-form">
            
            <div class="form-group">
                <label>Dzień pracy od:</label>
            	{{ Form::text('work_from',  $work_hour->work_from, array('id' => 'work_from', 'class' => 'form-control required timepick'))  }}
            </div>

            <div class="form-group">
                <label>Dzień pracy do:</label>
                {{ Form::text('work_to', $work_hour->work_to, array('id' => 'work_to','class' => 'form-control required timepick'))  }}
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="free" value="1"
                        @if($work_hour->free == 1)
                        checked
                        @endif
                        />
                    Dzień wolny
                </label>
            </div>
            {{Form::token()}}	
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="save">Zapisz zmiany</button>
</div>



<script type="text/javascript">
    $(document).ready(function() {
        $('.timepick').timepicker({
            minuteStep: 5,
            showInputs: false,
            disableFocus: false,
            showMeridian:false
        });

        $('input[name="free"]').change(function(){
            if($(this).is (':checked'))
                $('.timepick').prop('disabled', true);
            else
                $('.timepick').prop('disabled', false);
        }).change();

    });
</script>
