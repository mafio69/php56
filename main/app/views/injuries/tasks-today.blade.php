@extends('layouts.main')

@section('header')

Zlecenia (szkody) - wyszukiwanie
@if( Request::segment(4) == Auth::user()->id)
	swoich
@endif
spraw z alertem na dzisiaj

@include('injuries.menu-top')

@stop

@section('headerJs')
	@parent
	@include('injuries.nav')
@stop

@section('main')

	@include('injuries.menu')

	<div  id="injuries-container">
		<table class="table table-hover">
			<thead>
				<Th>lp</th>
				<th></th>
				<th></th>
				<th>nr sprawy</th>
				<th>samochód</th>
				<th>rejestracja</th>
				<Th>nr szkody</th>
				<th>data zgłoszenia</th>
				<th>osoba zgłaszająca</th>
				<th>miejsce zdarzenia</th>
				<th>status</th>
				<th>przyjmujący</th>
                <th></th>
			</thead>
			@foreach($injuries as $k => $injury)
			<tr>
				<td>{{$k+1}}.</td>
				<td>

					@if ($injury->if_courtesy_car == 1)
					<span class="ico ico_car tips" title="potrzebne auto zastępcze"></span>
					@endif

					@if ($injury->if_towing == 1)
					<span class="ico ico_holowanie tips" title="potrzebne holowanie"></span>
					@endif

					@if($injury->if_theft == 1)
					<i class="fa fa-chain-broken tips sm-ico red" title="kradzież pojazdu"></i>
					@endif

					@if($injury->type_incident_id == 13)
						<i class="fa fa-tag tips sm-ico red" title="przywłaszczenie"></i>
						@endif

					@foreach($injury->chat as $chat)
						@if($chat->active == 0)
							<i class="fa fa-comments-o blue font-large"></i>
							<?php break;?>
						@endif
					@endforeach

					@foreach($injury->chat as $chat)
						@if($chat->deadline != '')
							<i class="fa fa-clock-o
							    @if($chat->deadline == date('Y-m-d'))
                                    green
                                @elseif(strtotime($chat->deadline) < time())
                                    red
                                @else
                                    blue
                                @endif
							 font-large"></i>
						@endif
					@endforeach

					@foreach($injury->chat as $chat)

						@foreach($chat->messages as $message)
							<?php $status = get_receivers($message->status); ?>
							@if($status[0] == 1 && $message->dos_read  == '' && get_chat_group() == 1)
								<i class="fa fa-envelope-o red font-large"></i>
								<?php break 2;?>
							@elseif($status[1] == 1 && $message->branch_read  == '' && get_chat_group() == 2)
								<i class="fa fa-envelope-o red font-large"></i>
								<?php break 2;?>
							@elseif($status[2] == 1 && $message->info_read  == '' && get_chat_group() == 3)
								<i class="fa fa-envelope-o red font-large"></i>
								<?php break 2;?>
							@endif
						@endforeach
					@endforeach

					 @if($injury->user->typ() == 3)
					<i class="fa fa-info blue font-large"></i>
					@endif
				</td>
				<td>
				    @include('injuries.partials.alerts_today')
				</td>
				<td>
					@if(Auth::user()->can('kartoteka_szkody#wejscie'))
						<a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($injury->id))}}" >
							{{$injury->case_nr}}
						</a>
					@else
						{{$injury->case_nr}}
					@endif
				</td>
				<td>{{checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand)}} {{checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)}}</td>
				<Td>
					@if(Auth::user()->can('kartoteka_szkody#wejscie'))
						<a type="button" class="btn btn-link" href="{{ URL::route('injuries-info', array($injury->id)) }}" >{{$injury->vehicle->registration}}</a>
					@else
						{{$injury->vehicle->registration}}
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
					{{$injury->notifier_surname.' '.$injury->notifier_name.'<br>
					tel:'.$injury->notifier_phone.' email:'.$injury->notifier_email}}
				</td>
				<td>
					{{$injury->event_city.' '.$injury->event_street.'
					<br>
					'.$injury->date_event}}
				</td>
				<td>
					<b>
						{{ $injury->status->name }}
					</b>
				</td>
				<td>
					{{ $injury->user->name }}
				</td>
                <td>
					@include('injuries.includes.'.Config::get('definition.injuriesStepOptionsIncludes.'.$injury->step).'_options')
                </td>
			</tr>
			@endforeach
		</table>
		@include('injuries.legend')
		<div class="pull-right" style="clear:both;">{{ $injuries->appends(Input::all())->links() }}</div>
</div>



@stop

@section('headerJs')
	@parent
	<script type="text/javascript">

	    $(document).ready(function() {

	       	$('#modal-lg').on('click', '#set-branch', function(){
				var btn = $(this);
	       		if($('#id_warsztat').val() != '' ){
					btn.attr('disabled', 'disabled');
	       			$.ajax({
	                  type: "POST",
	                  url: $('#assign-branch-form').prop( 'action' ),
	                  data: $('#assign-branch-form').serialize(),
	                  assync:false,
	                  cache:false,
	                  success: function( data ) {
		                if(data.code == '0') location.reload();
		                else if(data.code == '1') self.location = data.url;
		                else{
		                	$('#modal-lg .modal-body').html( data.error);
		                	$('#set-branch').attr('disabled',"disabled");
		                }
	                  },
	                  dataType: 'json'
	                });

					return false;
	       		}else{
	       			alert('Proszę przypisać serwis.');
	       		}

	      	});

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
	                	$('#set-injury').attr('disabled',"disabled");
	                }
                  },
                  dataType: 'json'
                });
				return false;
	       	});

	    });

    </script>

@stop

