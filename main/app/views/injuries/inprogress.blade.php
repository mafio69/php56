@extends('layouts.main')


@section('header')
<span class="pull-left">
Zlecenia (szkody) - w obsłudze
</span>
@include('injuries.menu-top')

@stop


@include('injuries.nav')

@section('main')

	@include('injuries.menu')

	<div  id="injuries-container">

		<table class="table  table-hover  table-condensed" id="users-table">
			<thead>
				<Th style="width:30px;">lp.</th>
				<th style="min-width:20px;"></th>
				<th>nr sprawy</th>
				<th>samochód</th>
				<th>nr umowy</th>
				<th>rejestracja</th>
				<Th>właściciel</Th>
				<Th>nr szkody</th>
				<th>data zgłoszenia</th>
				<th>data i miejsce zdarzenia</th>
				<th>etap</th>
				<th>upoważnienie</th>
				<th>przyjmujący</th>
				<th>prowadzący</th>
				<th></th>
			</thead>

			<?php
				$lp = (($injuries->getCurrentPage()-1)*$injuries->getPerPage()) + 1;
				$searched_row = '';
				foreach ($injuries as $k => $injury)
				{ ?>
				<tr class="odd gradeX"
				@if(Session::has('last_injury') && $injury->id == Session::get('last_injury'))
					style="background-color: honeydew;"
					<?php Session::forget('last_injury');?>
				@endif
				>
					<td>{{$lp++}}.</td>
					<td>@include('injuries.includes.inprogress_statuses')</td>
					<td>
						@if(Auth::user()->can('kartoteka_szkody#wejscie'))
							<a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($injury->id))}}" >
								{{$injury->case_nr}}
							</a>
						@else
							{{$injury->case_nr}}
						@endif
					</td>
					<td>
						@if($injury->vehicle)
						{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) }} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)  }}
						@endif
					</td>
					<td>
						@if($injury->vehicle)
						{{ $injury->vehicle->nr_contract }}
						@endif
					</td>
					<Td>
						@if($injury->vehicle)
							@if(Auth::user()->can('kartoteka_szkody#wejscie'))
								<a type="button" class="btn btn-link" href="{{ URL::route('injuries-info', array($injury->id)) }}" >{{$injury->vehicle->registration}}</a>
							@else
								{{$injury->vehicle->registration}}
							@endif
						@endif
					</td>
					<td>
						@if($injury->vehicle)
						<span class="tips" title="{{ $injury->vehicle->owner->name }}">{{ $injury->vehicle->owner->short_name }}</span>
						@endif
					</td>
					<td @if($injury->dsp_notification) class="bg-danger tips" title="zgłoszenie DSP" data-container="body" @endif>
						@if($injury->injury_nr != NULL && $injury->injury_nr != '')
							{{$injury->injury_nr}}
						@else
							---
						@endif
					</td>
					<td>
						{{substr($injury->created_at, 0, -3)}}
					</td>
					<td>
						{{$injury->event_city.' '.$injury->event_street.'
                        <br>
                        '.$injury->date_event}}
					</td>
					<td>
						@if($injury->stepStage)
							<span class="bold">{{ $injury->stepStage->name }}</span>
						@endif
					</td>
					<td>
						@if ($injury->task_authorization == 0)
							<i class="fa fa-exclamation md-ico task" orygin="fa-exclamation" task="task_authorization" val="1" id_injury="{{$injury->id}}"></i>
						@else
							<i class="fa fa-check md-ico task" orygin="fa-check" task="task_authorization" val="0" id_injury="{{$injury->id}}"></i>
						@endif
					</td>
					<td>

						{{ $injury->user->name }}
					</td>
					<td>
						@if($injury->leader)
							{{ $injury->leader->name }}
						@endif

					</td>
                    @include('injuries.includes.inprogress_options')
				</tr>
				<?php }
			 ?>
		</table>
		@include('injuries.legend')
		<div class="pull-right" style="clear:both;">{{ $injuries->appends(Input::all())->links() }}</div>
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

			   $.ajax({
				   type: "POST",
				   url: "{{ URL::route('injuries-setTask') }}",
				   data: { task: task, val: val, '_token': $('input[name=_token]').val(), id_injury: id_injury },
				   assync:false,
				   cache:false,
                   beforeSend: function(){
                       element.hide();
                       element.after('<i class="fa fa-spinner fa-pulse loading-changes md-ico "></i>');
                   },
                   success: function() {
					   if(val == 1){
                            element.attr('val',0);
                            element.removeClass('fa-exclamation');
                            element.addClass('fa-check');
					   }else{
                            element.attr('val',1);
                            element.removeClass('fa-check');
                            element.addClass('fa-exclamation');
                       }
                       element.parent().find('.loading-changes').remove();
                       element.show();
				   },
                   error: function(){
                       element.parent().find('.loading-changes').remove();
                       element.show();
                       $.notify({
                           icon: "fa fa-exclamation-triangle",
                           message: "Wystąpił błąd przy zmianie statusu zadania."
                       },{
                           type: 'danger',
                           placement: {
                               from: 'bottom',
                               align: 'right'
                           },
                           delay: 2500,
                           timer: 500
                       });
                   },
                   dataType: 'json'
			   });
	       	});

			$('.show-repair-stages').on('click', function(){
			    var injury_id = $(this).data('injury');

			    $('.repair-stages-container[data-injury="'+injury_id+'"]').toggle();
			});
	    });

    </script>

@stop
