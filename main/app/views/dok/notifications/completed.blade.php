@extends('layouts.main')

@section('header')

Zgłoszenia - zakończone

@include('dok.notifications.menu-top')

@stop

@include('dok.notifications.nav')

@section('main')

	@include('dok.notifications.menu')

	<div class="table-responsive" style="clear:both;">

		<table class="table  table-hover  table-condensed" >
			<thead>
				<Th style="width:30px;">lp.</th>
				<th>nr sprawy</th>
				<th>typ zgłoszenia</th>
				<th>samochód</th>
				<th>rejestracja</th>
				<th >data zgłoszenia</th>
				<th>przyjmujący</th>
			</thead>

		<?php $lp = (($notifications->getCurrentPage()-1)*$notifications->getPerPage()) + 1;?>
		@foreach($notifications as $notifi)
		<tr class="odd gradeX"
			@if(Session::has('last_notification') && $notifi->id == Session::get('last_notification'))
				style="background-color: honeydew;"
				<?php Session::forget('last_notification');?>
			@endif
		>
			<td>{{$lp++}}.</td>
			<td><a type="button" class="btn btn-link" href="{{ URL::route('dok.notifications.info', array($notifi->id)) }}" >{{$notifi->case_nr}}</a></td>
			<td>
				{{ $notifi->process->name }}
			</td>
			<td>{{checkObjectIfNotNull($notifi->vehicle->brand, 'name', $notifi->vehicle->brand)}} {{checkObjectIfNotNull($notifi->vehicle->model, 'name', $notifi->vehicle->model)}}</td>
			<td>{{$notifi->vehicle->registration}}</td>
			<td>{{substr($notifi->created_at, 0, -3)}}</td>
			<td>
				{{ $notifi->user->name }}
			</td>
		</tr>
		@endforeach
		</table>
		<div class="pull-right" style="clear:both;">{{ $notifications->links() }}</div>
	</div>



@stop
