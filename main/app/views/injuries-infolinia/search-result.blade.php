<table class="table table-hover">
	<thead>
		<Th>lp</th>
		<th></th>
		<th>nr sprawy</th>
		<th>samochód</th>
		<Th>nr szkody</th>
		<th>data zgłoszenia</th>
		<th>osoba zgłaszająca</th>
		<th>miejsce zdarzenia</th>
		<th>status</th>
		<th>przyjmujący</th>
	</thead>
	@foreach($injuries as $k => $injury)
	<tr class="vertical-middle">
		<td>{{$k+1}}.</td>
		<td>


			@if ($injury->if_courtesy_car == 1)
			<span class="ico ico_car tips" title="potrzebne auto zastępcze"></span>
			@endif

			@if ($injury->if_towing == 1)
			<span class="ico ico_holowanie tips" title="potrzebne holowanie"></span>
			@endif

			@if($injury->if_theft == 1)
			<i class="fa fa-chain-broken tips sm-ico red sm-ico" title="kradzież pojazdu"></i>
			@endif

            @foreach($injury->chat as $chat)
                @if($chat->active == 0)
                    <i class="fa fa-comments-o blue sm-ico"></i>
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
					 sm-ico"></i>
				@endif
			@endforeach

			@foreach($injury->chat as $chat)

				@foreach($chat->messages as $message)
					<?php $status = get_receivers($message->status); ?>
					@if($status[0] == 1 && $message->dos_read  == '' && get_chat_group() == 1)
						<i class="fa fa-envelope-o red sm-ico"></i>
						<?php break 2;?>
					@elseif($status[1] == 1 && $message->branch_read  == '' && get_chat_group() == 2)
						<i class="fa fa-envelope-o red sm-ico"></i>
						<?php break 2;?>
					@elseif($status[2] == 1 && $message->info_read  == '' && get_chat_group() == 3)
						<i class="fa fa-envelope-o red sm-ico"></i>
						<?php break 2;?>
					@endif
				@endforeach
			@endforeach

			@if($injury->locked_status == 5)

			  		<i class="fa fa-lock unlock red  tips  sm-ico" title="zablokowane zarządzanie szkodą"></i>

			 @endif
			 @if($injury->locked_status == '-5')

			  		<i class="fa fa-unlock lock red  tips  sm-ico" title="odblokowane zarządzanie szkodą"></i>

			 @endif

			 @if($injury->user->typ() == 3)
			<i class="fa fa-info blue sm-ico"></i>
			@endif
		</td>
		<td>

			<a type="button" class="btn btn-link" href="{{URL::route('injuries-infolinia-info', array($injury->id))}}" >
				{{$injury->case_nr}}
			</a>
		</td>
		<td>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) }} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)  }}</td>
		<td>
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
	</tr>
	@endforeach
</table>
@include('injuries.legend')
