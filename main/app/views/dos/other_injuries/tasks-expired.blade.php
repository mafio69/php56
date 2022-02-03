@extends('layouts.main')

@section('header')

Zlecenia (szkody) - wyszukiwanie spraw z przedawnionym alertem

@include('dos.other_injuries.partials.menu-top')

@stop

@include('dos.other_injuries.partials.nav')

@section('main')

	@include('dos.other_injuries.partials.menu')

	<div class="table-responsive" id="injuries-container">
		<table class="table table-hover">
			<thead>
				<Th>lp</th>
				<th></th>
				<th></th>
				<th>obiekt sprawy</th>
                <th>kategoria</th>
                <th >nr umowy</th>
                <th>właściciel</th>
                <th >typ szkody</th>
                <Th>nr szkody</th>
                <th >data zdarzenia</th>
                <th >miejsce zdarzenia</th>
                <Th>data zgłoszenia</th>
				<th>status</th>
				<th>przyjmujący</th>
				<th ></th>
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

					 @if($injury->user && $injury->user->typ() == 3)
					<i class="fa fa-info blue font-large"></i>
					@endif
				</td>
				<td>
				    @include('injuries.partials.alerts_expired')
				</td>
				<td>
					<a type="button" class="btn btn-link" target="_blank" href="{{URL::route('dos.other.injuries.info', array($injury->id))}}" >
						{{$injury->case_nr}}
					</a>
				</td>
				<td>{{$injury->object ? $injury->object->description : ''}}</td>
                <td>{{ checkObjectIfNotNull($injury->object->assetType, 'name') }}</td>
                <Td>
                    <a type="button" class="btn btn-link" href="{{ URL::route('dos.other.injuries.info', array($injury->id)) }}" >{{$injury->object->nr_contract}}</a>
                </td>
                <td><span class="tips" title="{{ $injury->object->owner->name }}">{{ $injury->object->owner->short_name }}</span></td>
                <td>{{ $injury->injuries_type ? $injury->injuries_type->name : ''}}</td>
                <td>
                    @if($injury->injury_nr != NULL && $injury->injury_nr != '')
                        {{$injury->injury_nr}}
                    @else
                        ---
                    @endif
                </td>
                <td>{{$injury->date_event}}</td>
                <Td>{{$injury->event_city}}</td>
                <td>{{substr($injury->created_at, 0, -3)}}</td>
				<td>
					<b>
				<?php
				switch ($injury->step) {
				    case '-10':
				        echo "szkoda anulowana";
				        break;
				    case '-5':
				        echo "szkoda całkowita";
				        break;
				    case '-3':
				        echo "kradzież";
				        break;
				    case '0':
				        echo "nowe";
				        break;
				    case '5':
				    	echo "w obsłudze";
				        break;
				    case '10':
				    	echo "w trakcie naprawy";
				        break;
				    case '15':
				    	echo "zakończone w normalnym trybie";
				        break;
				    case '17':
				    	echo "zakończone bez likwidacji";
				        break;
				    case '19':
				    	echo "zakończone bez naprawy";
				        break;
				    case '20':
				    	echo "odmowa zakładu ubezpieczeń";
				        break;
				}
				?>
					</b>
				</td>
				<td>
					{{ $injury->user->name }}
				</td>
				@include('dos.other_injuries.includes.'.Config::get('definition.dosInjuriesStepOptionsIncludes.'.$injury->step).'_options')
			</tr>
			@endforeach
		</table>
		@include('dos.other_injuries.partials.legend')
</div>



@stop

@section('headerJs')
	@parent
	<script type="text/javascript">

	    $(document).ready(function() {

	       	$('#modal').on('click', '#set-branch', function(){
				btn = $(this);
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
		                	$('#modal .modal-body').html( data.error);
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
	       		btn = $(this);
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

