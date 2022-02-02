@extends('layouts.main')


@section('header')

Lista kart likwidacji szkód

<div class="pull-right"><a href="{{ URL::route('settings.liquidation_cards', array('create')) }}" class="btn btn-small btn-primary"><i class="fa fa-plus fa-fw"></i> Dodaj kartę</a></div>
@stop

@section('main')

@include('modules.flash_notification')

@include('settings.liquidation_cards.search')

	<div class="table-responsive">

		<table class="table  table-hover" >
			<thead>
					<th>lp.</th>
					<th>nr karty</th>
					<th>klient</th>
					<th>nr umowy</th>
					<th>pojazd</th>
					<th>data wystawienia</th>
					<th>data ważności</th>
					<th>wystawiający</th>
					<th ></th>

			</thead>

			<?php
				$lp = (($cards->getCurrentPage()-1)*$cards->getPerPage()) + 1;
				foreach ($cards as $card)
				{ ?>
				<tr >
					<td width="20px">{{$lp++}}.</td>
					<Td>{{$card->number}}</td>
					<td>{{ $card->vehicle->client->name }}</td>
					<Td>{{ $card->vehicle->nr_contract }}</Td>
					<td>{{$card->vehicle->registration}}</td>
					<td>{{$card->release_date}}</td>
					<td>
					    <span
					    @if(new DateTime($card->expiration_date) < new DateTime(date('Y-m-d')) )
					    style="color:red;"
					    @endif
					    >
					        {{$card->expiration_date}}
					    </span>
                    </td>
					<td>{{$card->user->name}}</td>
					<td>
					    <button target="{{ URL::route('settings.liquidation_cards', array('edit', $card->id)) }}" class="btn btn-xs btn-warning modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-pencil"></i> edytuj</button>
					    <button target="{{ URL::route('settings.liquidation_cards', array('delete', $card->id)) }}" class="btn btn-xs btn-danger modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-trash"></i> usuń</button>
					</td>
				</tr>
				<?php }
			 ?>


		</table>
		<div class="pull-right" style="clear:both;">{{ $cards->links() }}</div>
	</div>



@stop


