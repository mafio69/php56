@extends('layouts.main')

@section('header')

Zgłoszenia - nowe

@include('dok.notifications-user.menu-top')

@stop



@section('main')

	@include('dok.notifications-user.menu')

 	@if($notifications_priority->count() > 0)
 	<div class="row">
		<div class="col-md-12">
			<h4 class="text-danger"><i class="fa fa-bolt"></i> Zgłoszenia priorytetowe</h4>
			<div class="table-responsive marg-btm marg-top">
				<table class="table  table-hover  table-condensed" >
					<thead>
						<Th style="width:30px;">lp.</th>
                        <th style="width: 200px;"></th>
						<th style="width: 80px;">nr sprawy</th>
						<th>typ zgłoszenia</th>
						<th>samochód</th>
						<th style="width: 110px;">rejestracja</th>
						<th style="width: 135px;">data zgłoszenia</th>
						<th style="width: 200px;">przyjmujący</th>
						<th style="width: 85px;"></th>
					</thead>

				<?php $lp = (($notifications_priority->getCurrentPage()-1)*$notifications_priority->getPerPage()) + 1;?>
				@foreach($notifications_priority as $notifi)
				<tr class="odd gradeX "
					@if(Session::has('last_notification') && $notifi->id == Session::get('last_notification'))
						style="background-color: honeydew;"
						<?php Session::forget('last_notification');?>
					@endif
				>
					<td>{{$lp++}}.</td>
                    <td>
                        @if( $notifi->process->time_limit > 0)
                        <?php
                        $progress = round(get_working_hours($notifi->created_at, date('Y-m-d H:i:s')) / ($notifi->process->time_limit*60) * 100, 2);
                        ?>
                        <div class="progress">
                            <div class="progress-bar
                                @if($progress < 50)
                                    progress-bar-info
                                @elseif($progress < 80)
                                    progress-bar-warning
                                @else
                                    progress-bar-danger
                                @endif
                                progress-bar-striped" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" style="width:
                                @if($progress <= 100)
                                    {{ $progress }}%
                                @else
                                    100%
                                @endif
                                ">
                                {{ convertToHoursMins( ($notifi->process->time_limit*60)- get_working_hours($notifi->created_at, date('Y-m-d H:i:s')) ) }} h
                            </div>

                        </div>
                        @endif
                    </td>
					<td><a type="button" class="btn btn-link" href="{{ URL::route('dok.notifications.info', array($notifi->id)) }}" >{{$notifi->case_nr}}</a></td>
					<td>
						{{ $notifi->process->name }}
					</td>
					<td>{{ checkObjectIfNotNull($notifi->vehicle->brand, 'name', $notifi->vehicle->brand) }} {{checkObjectIfNotNull($notifi->vehicle->model, 'name', $notifi->vehicle->model)}}</td>
					<td>{{$notifi->vehicle->registration}}</td>
					<td>{{substr($notifi->created_at, 0, -3)}}</td>
					<td>
						{{ $notifi->user->name }}
					</td>
					<td>
						<button target="{{ URL::route('dok.notifications.getInprogress', array($notifi->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm" >przyjmij</button>
					</td>
				</tr>
				@endforeach
				</table>
				<div class="pull-right" style="clear:both;">{{ $notifications_priority->links() }}</div>
				<h4 class="inline-header "></h4>
			</div>
		</div>
	</div>
	@endif

	@if($notifications->count() > 0)

	<div class="row">
		<div class="col-md-12 table">
			<div class="alert alert-info marg-top" role="alert">
				<button target="{{ URL::route('dok.notifications.getInprogress', array($notifications->first()->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm" style="margin-right:20px;">Przyjmij kolejne zgłoszenie</button>
				Liczba oczekujących zgłoszeń - <strong>{{ $notifications->count() }}</strong>
			</div>
		</div>
	</div>
	@endif


@stop

