@extends('layouts.main')


@section('header')
<span class="pull-left">
Zlecenia (szkody) - całkowita
</span>
@include('injuries.menu-top')

@stop

@include('injuries.nav')




@section('main')

	@include('injuries.menu')

	<div id="injuries-container">

		<table class="table  table-hover  table-condensed" id="users-table">
			<thead>
					<Th style="width:30px;">lp.</th>
					<th></th>
					<Th></Th>
					<th style="width: 145px;">status</th>
					<th style="width: 145px;">etap</th>
					<th>nr sprawy</th>
					<th>samochód</th>
					<th>nr umowy</th>
					<th>rejestracja</th>
					<Th>właściciel</Th>
					<Th>nr szkody</th>
					<th>data zgłoszenia</th>
					<th>data i miejsce zdarzenia</th>
					<th>upoważnienie</th>
					<th>przyjmujący</th>
					<th>prowadzący</th>
					<th></th>
			</thead>

			<?php
				$lp = (($injuries->getCurrentPage()-1)*$injuries->getPerPage()) + 1;
				foreach ($injuries as $k => $injury)
				{ ?>
				<tr class="odd gradeX"
				@if(Session::has('last_injury') && $injury->id == Session::get('last_injury'))
					style="background-color: honeydew;"
					<?php Session::forget('last_injury');?>
				@endif
				>
					<td>{{$lp++}}.</td>
					<Td>@include('injuries.includes.total_statuses')</td>
					<td>@include('injuries.total_partials.alerts')</td>
					<td>
					    @if($injury->status)
							<span class="label label-border-info status-btn tips" title="{{ $injury->status->name }}">{{ $injury->status->name }}</span>
                        @endif
					</td>
					<td>
						@if($injury->totalStatus)
							@if($injury->totalStatus->manual_changeable != 0)
								<button type="button" class="btn btn-xs status-btn tips btn-info modal-open-sm " target="{{ URL::route('injuries.total.getChangeStatus', array($injury->id)) }}" data-toggle="modal" data-target="#modal-sm"
								title="{{ $injury->totalStatus->name }}">
								{{ $injury->totalStatus->name }}
								</button>
							@else
								<span class="label label-border-info status-btn tips" title="{{ $injury->totalStatus->name }}">{{ $injury->totalStatus->name }}</span>
							@endif
						@endif
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
					<td>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) }} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)  }}</td>
					<td>{{ $injury->vehicle->nr_contract }}</td>
					<Td>
						@if(Auth::user()->can('kartoteka_szkody#wejscie'))
							<a type="button" class="btn btn-link" href="{{ URL::route('injuries-info', array($injury->id)) }}" >{{$injury->vehicle->registration}}</a>
						@else
							{{$injury->vehicle->registration}}
						@endif
					</td>
					<td>
						<span class="tips" title="{{ $injury->vehicle->owner->name }}">{{ $injury->vehicle->owner->short_name }}</span>
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


                    @include('injuries.includes.total_options')
				</tr>
				<?php }
			 ?>
		</table>
		@include('injuries.legend')
		<div class="pull-right" style="clear:both;">{{ $injuries->appends(Input::all())->links() }}</div>
	</div>



@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {

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
