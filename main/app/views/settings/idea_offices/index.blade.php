@extends('layouts.main')

@section('header')

Baza oddziałów IdeaLeasing

<div class="pull-right">
	<a href="{{{ URL::route('idea.offices.create') }}}" class="btn btn-small btn-primary "><span class="glyphicon glyphicon-plus-sign"></span> Dodaj oddział</a>
</div>
@stop

@section('main')

	<div >
		<table class="table table-auto  table-hover " id="users-table">
			<thead>

					<th >lp.</th>
					<th >Nazwa oddziału</th>
					<th>Adres</th>
					<th>Telefon</th>
                    <th></th>
                    <th></th>
			</thead>

            @foreach ($offices as $k => $office)
				<tr class="odd gradeX">
					<td>{{++$k}}.</td>
					<Td>{{$office->name}}</td>
                    <td>{{ $office->post }} {{ $office->city }}, {{ $office->street }}</td>
                    <td>{{ $office->phone }}</td>
                    <td><a href="{{ URL::route('idea.offices.edit', array($office->id)) }}" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> edytuj</a> </td>
                    <td>
                        <button target="{{ URL::route('idea.offices.delete', array($office->id)) }}" class="btn btn-danger btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-trash-o"></i> usuń</button>
                    </td>
				</tr>
            @endforeach
		</table>
	</div>



@stop

