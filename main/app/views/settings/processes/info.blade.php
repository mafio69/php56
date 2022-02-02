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
	                <label>Waga procesu:</label>
	            	{{ $process->weight }} 
	            	<button type="button" class="btn btn-warning btn-xs modal-open" target="{{ URL::route('settings.processes.edit', array($process->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i></button>                              
	            </div>
	        </div>    
	        <div class="col-md-6">    
	            <div class="form-group">
	                <label>Limit czasu obsługi [h]:</label>
	            	{{ $process->time_limit }}   
	            	<button type="button" class="btn btn-warning btn-xs modal-open" target="{{ URL::route('settings.processes.edit', array($process->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i></button>                               
	            </div>
	        </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Opis:</label>
                <button type="button" class="btn btn-warning btn-xs modal-open" target="{{ URL::route('settings.processes.edit', array($process->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i></button><br>
                {{ $process->description }}
            </div>
        </div>


		<h4>Przypisani pracownicy: <button type="button" class="btn btn-primary btn-sm pull-right modal-open" target="{{ URL::route('settings.processes.getAppendUser', array($process->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-plus"></i> dodaj pracownika</button></h4>
		<table class="table table-hover">
			<thead>
				<th>lp.</th>
				<th>Nazwisko</th>
				<th></th>
			</thead>
			<?php $lp = 1; ?>
			@foreach($process->users as $user)
			<tr>
				<td>{{ $lp++ }}.</td>
				<td>{{ $user->user->name }}</td>
				<td><button type="button" class="btn btn-danger btn-xs modal-open" target="{{ URL::route('settings.processes.getDeleteUser', array($user->id)) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-trash-o"></i> usuń</button></td>
			</tr>
			@endforeach
		</table>
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
                url: '{{ URL::route('settings.processes.priority', array($process->id)) }}',
                data: '_token='+$('input[name=_token]').val()+'&val='+val,
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