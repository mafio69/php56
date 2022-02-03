@extends('layouts.main')


@section('header')
<span class="pull-left">
Zlecenia (szkody) - nieprzetworzone
</span>

@include('dos.other_injuries.partials.menu-top')

@stop

@include('dos.other_injuries.partials.nav')

@section('main')

	@include('dos.other_injuries.partials.menu')

	<div id="injuries-container">
		<table class="table  table-hover  table-condensed">
			<thead>
					<Th style="width:30px;">lp.</th>
					<th style="min-width:20px;"></th>
					<th >nr umowy</th>
					<th >data zdarzenia</th>
					<th >miejsce zdarzenia</th>
					<th>typ szkody</th>
					<th>zgłaszający</th>
					<Th>data zgłoszenia</th>
					<th>przesłane zdjęcia</th>
					<th ></th>
			</thead>

			<?php $lp = (($injuries->getCurrentPage()-1)*$injuries->getPerPage()) + 1;?>
			@foreach ($injuries as $k => $injury)
				<tr class="odd gradeX"
				@if(Session::has('last_injury') && $injury->id == Session::get('last_injury'))
					style="background-color: honeydew;"
					<?php Session::forget('last_injury');?>
				@endif
				>
					<td>{{$lp++}}.</td>
					<td>
						@if($injury->source == 1)
							<i class="fa fa-laptop "></i>
						@elseif($injury->source == 0)
							<i class="fa fa-mobile font-large"></i>
						@else
							<i class="fa fa-file-excel-o "></i>
						@endif
					</td>
					<td>{{ checkIfEmpty($injury->nr_contract) }}</td>
					<td>{{ checkIfEmpty($injury->date_event) }}</td>
					<Td>{{ checkIfEmpty($injury->event_city) }}</td>
					<td>
						@if( ($injury->source == 0 || $injury->source == 3)  && $injury->injuries_type()->first())
							{{ $injury->injuries_type()->first()->name }}
						@else
							@if($injury->injuries_type == 2)
								komunikacyjna OC
							@elseif($injury->injuries_type == 1)
								komunikacyjna AC
							@elseif($injury->injuries_type == 3)
								komunikacyjna kradzież
							@elseif($injury->injuries_type == 4)
								majątkowa
							@elseif($injury->injuries_type == 5)
								majątkowa kradzież
							@elseif($injury->injuries_type == 6)
								komunikacyjna AC - regres
							@endif
						@endif
					</td>
					<td>
						{{ $injury->notifier_email }}
						<br>
						{{ $injury->notifier_surname }} {{ $injury->notifier_name }} {{ $injury->notifier_phone }}
					</td>
					<td>{{substr($injury->created_at, 0, -3)}}</td>
					<td>
						@if($injury->files->count() > 0)
							<a href="#" target="{{ URL::route('injuries-getUploadesPictures', array($injury->id)) }}" class="modal-open btn btn-success btn-sm" data-toggle="modal" data-target="#modal"><i class="fa fa-search"></i> pokaż</a>
						@else
							---
						@endif
					</td>
					<td>
						<a href="{{ URL::route('injuries.unprocessed.print', array($injury->id)) }}" target="_blank" class="btn btn-primary btn-sm tips" title="drukuj zgłoszenie"><i class="fa fa-print"></i></a>
					</td>
					@include('dos.other_injuries.actions.unprocessed_options')
				</tr>
			@endforeach
		</table>
		@include('dos.other_injuries.partials.legend')
		<div class="pull-right" style="clear:both;">{{ $injuries->links() }}</div>
	</div>

	{{Form::token()}}



@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {
	       $('#modal-sm').on('click', '#set-injury', function(){
	       		var btn = $(this);
				btn.attr('disabled', 'disabled');
	       		$.ajax({
                  type: "POST",
                  url: $('#dialog-injury-form').prop( 'action' ),
                  data: $('#dialog-injury-form').serialize(),
                  assync:false,
                  cache:false,
                  success: function( data ) {
	                if(data.code == '0') location.reload();
	                else if(data.code == '1') self.location = data.url;
	                else{
	                	$('#modal-sm .modal-body').html( data.error);
	                	if(isset(data.url) && data.url != ''){
                            $('#modal-sm').on('hidden.bs.modal', function (e) {
                              self.location = data.url;
                            })
                        }
	                	$('#set-injury').attr('disabled',"disabled");
	                }
                  },
                  dataType: 'json'
                });
				return false;
	       });

	       $('.task').click(function(){
	       		var task = $(this).attr('task');
	       		var val = $(this).attr('val');
	       		var id_injury = $(this).attr('id_injury');
	       		var element = $(this);
	       		$.post( "<?php echo URL::route('injuries-setTask'); ?>",
	       		  { task: task, val: val, '_token': $('input[name=_token]').val(), id_injury: id_injury })
				  .done(function( data ) {
					  	if(val == 1){
		       				element.attr('orygin', 'fa-check');
		       				element.attr('val',0);
		       				element.removeClass('fa-exclamation');
	       					element.addClass('fa-check');
		       			}else{
		       				element.attr('orygin', 'fa-exclamation');
		       				element.attr('val',1);
		       				element.removeClass('fa-check');
	       					element.addClass('fa-exclamation');
		       			}
				  });
	       	});



	    });

    </script>

@stop

