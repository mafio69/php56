<div class="panel panel-default" id="process_info_panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            {{ $process->name }}
            {{ Form::token() }}
            <button type="button" class="close close_panel" ><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="btn-group pull-right marg-right" data-toggle="buttons">
                <label class="btn btn-danger btn-xs
                @if($process->priority == 1)
                    active
                @endif
                priority">
                    <input type="checkbox"
                    @if($process->priority == 1)
                    checked
                    @endif
                    name="priority" value="1">
                    <i class="fa fa-bolt"></i> obligatoryjnie priorytetowe
                </label>
            </div>
        </h3>

    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Waga procesów:</label>
                    {{ $process->weight }}
                    <button type="button" class="btn btn-warning btn-xs modal-open" target="{{ URL::route('settings.processes.edit-node', array($process->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i></button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Limit czasów obsługi [h]:</label>
                    {{ $process->time_limit }}
                    <button type="button" class="btn btn-warning btn-xs modal-open" target="{{ URL::route('settings.processes.edit-node', array($process->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i></button>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.close_panel').click(function(){
            $('#process_info_panel').remove();
        });

        $('input[name=priority]').on('change', function(){
            if( $(this).prop('checked') )
                val = 1;
            else
                val = 0;

            $.ajax({
                type: "POST",
                url: '{{ URL::route('settings.processes.priority-node', array($process->id)) }}',
                data: '_token='+$('input[name=_token]').val()+'&priority='+val,
                assync:false,
                cache:false,
                success: function( data ) {
                    if(data.code == '0'){
                        $('#response-alert-info').html(data.message).fadeIn(300).delay(2500).fadeOut(300, function(){
                            $(this).html('');
                        });
                    }

                },
                dataType: 'json'
            });
        });
    });
</script>