@extends('layouts.main')

@section('header')
	Pojazdy
	@if($if_truck == 0)
		< 3.5t
	@else
		> 3.5t
	@endif
@stop

@section('main')
    <div class="alert alert-danger text-center">Brak firm, których jesteś opiekunem.</div>
@stop


