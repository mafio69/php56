@extends('layouts.main')

@section('header')

Zgłoszenia - nowe

@include('dok.notifications.menu-top')

@stop

@include('dok.notifications.nav')

@section('main')

	@include('dok.notifications.menu')

	<div class="table-responsive " style="clear:both;">

		<table class="table  table-hover  table-condensed" >
			<thead>
                <Th style="width:30px;">lp.</th>
                <Th></Th>
                <th style="width: 200px;"></th>
                <th style="width: 80px;">nr sprawy</th>
                <th>typ zgłoszenia</th>
                <th>samochód</th>
                <th style="width: 110px;">rejestracja</th>
                <th style="width: 135px;">data zgłoszenia</th>
                <th style="width: 200px;">przyjmujący</th>
                <th style="width: 85px;"></th>
			</thead>

		<?php $lp = (($notifications->getCurrentPage()-1)*$notifications->getPerPage()) + 1;?>
		@foreach($notifications as $notifi)
		<tr class="odd gradeX "
			@if(Session::has('last_notification') && $notifi->id == Session::get('last_notification'))
				style="background-color: honeydew;"
				<?php Session::forget('last_notification');?>
			@endif
		>
			<td>{{$lp++}}.</td>
			<td>
			    @if($notifi->priority >= 1)
                    <i class="fa fa-bolt red"></i>
                @endif
			</td>
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
				<button class="btn btn-warning btn-xs modal-open-lg" target="{{ URL::route('dok.notifications.getChangeProcess', array($notifi->id)) }}" data-toggle="modal" data-target="#modal-lg">zmień typ</button>
			</td>
			<td>{{ checkObjectIfNotNull($notifi->vehicle->brand, 'name', $notifi->vehicle->brand) }} {{checkObjectIfNotNull($notifi->vehicle->model, 'name', $notifi->vehicle->model)}}</td>
			<td>{{$notifi->vehicle->registration}}</td>
			<td>{{substr($notifi->created_at, 0, -3)}}</td>
			<td>
				{{ $notifi->user->name }}
			</td>
			<td>
				<div class="btn-group " style="min-width:130px;" >
					<button target="{{ URL::route('dok.notifications.getInprogress', array($notifi->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm" >przyjmij</button>
					<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" target="{{ URL::route('dok.notifications.getInprogress', array($notifi->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przyjmij</a></li>
							<li class="divider"></li>
						<li><a href="#" target="{{ URL::route('dok.notifications.getCancel', array($notifi->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">anuluj zgłoszenie	</a></li>
					</ul>
				</div>
			</td>
		</tr>

		@endforeach
		</table>
		<div class="pull-right" style="clear:both;">{{ $notifications->links() }}</div>

	</div>



@stop
