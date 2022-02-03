<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Zmiana typu zgłoszenia zgłoszenia</h4>
</div>
<form action="{{ URL::route('dok.notifications.setChangeProcess', array($notification->id)) }}" method="post"  id="dialog-form">
<div class="modal-body" style="overflow:hidden;">
    {{Form::token()}}
    <div class="row">
        <div class="col-md-3">
            <div class="btn-group-vertical notifi-list" data-toggle="buttons">
                @foreach($processes as $k => $process)
                    <label class="btn btn-default">
                        <input type="radio" class="notifi-process sr-only required" count="1" value="{{ $process->id }}" required> {{ $process->name }}
                        @if($process->description != '')
                        <i class="fa fa-info-circle blue tips pull-right" title="{{ $process->description }}"></i>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    {{Form::hidden('process_id', '', array('id' => 'process_id', 'class' => 'required'))  }}


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" disabled>Potwierdź</button>
</div>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('change', '.notifi-process', function(){
            boxNotifi = $(this);
            process_id = $(this).val();
            count = $(this).attr('count');

            $.ajax({
                url: "<?php echo  URL::route('dok.notifications.create.processes');?>",
                data: {
                    process_id: process_id,
                    count: count,
                    _token: $('input[name="_token"]').val()
                },
                type: "POST",
                success: function( data ) {
                    if(data == 0){
                        boxNotifi.parent().parent().parent().nextAll('.child_process').remove();
                        $('#process_id').val(boxNotifi.val());
                        $('#set').removeAttr('disabled');
                    }else{
                        boxNotifi.parent().parent().parent().nextAll('.child_process').remove();
                        $( data ).insertAfter( boxNotifi.parent().parent().parent() );
                        $('#process_id').val('');
                        $('#set').attr('disabled', 'disabled');
                    }
                }
            });
        });
    });
</script>