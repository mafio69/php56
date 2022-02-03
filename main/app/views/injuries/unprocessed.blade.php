@extends('layouts.main')

@section('header')
<span class="pull-left">
Zlecenia (szkody) - nieprzetworzone
</span>
@include('injuries.menu-top')

@stop

@include('injuries.nav')

@section('main')

@include('injuries.menu')

<div  id="injuries-container">

    <table class="table  table-hover  table-condensed" id="users-table">
        <thead>
            <Th style="width:30px;">lp.</th>
            <th></th>
            <th >rejestracja</th>
            <th >nr umowy</th>
            <th >data zdarzenia</th>
            <th >miejsce zdarzenia</th>
            <th>typ szkody</th>
            <th>zgłaszający</th>
            <Th>data zgłoszenia</th>
            <th>uszkodzenia</th>
            <th>przesłane zdjęcia</th>
            <Th></Th>
            <th ></th>
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
                    <td>
                        @if($injury->source == 1)
                            <i class="fa fa-laptop "></i>
                        @elseif($injury->source == 0)
                            <i class="fa fa-mobile font-large"></i>
                        @else
                            <i class="fa fa-file-excel-o "></i>
                        @endif
                    </td>
					<Td>{{ checkIfEmpty($injury->registration) }}</td>
					<td>{{ checkIfEmpty($injury->nr_contract) }}</td>
					<td>{{ checkIfEmpty($injury->date_event) }}</td>
					<Td>{{ checkIfEmpty($injury->event_city) }}</td>
                    <td>
                        @if( ($injury->source == 0 || $injury->source == 3)  && $injury->injuries_type()->first())
                            {{ $injury->injuries_type()->first()->name }}
                        @else
                            @if($injury->injuries_type == 2)
                                komunikacyjna OC
                            @elseif($injury->injuries_type == 1)
                                komunikacyjna AC
                            @elseif($injury->injuries_type == 3)
                                komunikacyjna kradzież
                            @elseif($injury->injuries_type == 4)
                                majątkowa
                            @elseif($injury->injuries_type == 5)
                                majątkowa kradzież
                            @elseif($injury->injuries_type == 6)
                                komunikacyjna AC - Regres    
                            @endif
                        @endif
                    </td>
					<td>
                        {{ $injury->notifier_email }}
                        <br>
                        {{ $injury->notifier_surname }} {{ $injury->notifier_name }} {{ $injury->notifier_phone }}
                    </td>
					<td>{{substr($injury->created_at, 0, -3)}}</td>
                    <td>
                        @if($injury->damages->count() > 0)
                        <a href="#" target="{{ URL::route('injuries-getDamages', array($injury->id)) }}" class="modal-open btn btn-success btn-sm" data-toggle="modal" data-target="#modal"><i class="fa fa-search"></i> pokaż</a>
                        @else
                        ---
                        @endif
                    </td>
                    <td>
                        @if($injury->files->count() > 0)
                        <a href="#" target="{{ URL::route('injuries-getUploadesPictures', array($injury->id)) }}" class="modal-open btn btn-success btn-sm" data-toggle="modal" data-target="#modal"><i class="fa fa-search"></i> pokaż</a>
                        @else
                        ---
                        @endif
                    </td>
                    <td>
                        <a href="{{ URL::route('injuries.unprocessed.print', array($injury->id)) }}" target="_blank" class="btn btn-primary btn-sm tips" title="drukuj zgłoszenie"><i class="fa fa-print"></i></a>
                    </td>
                    @include('injuries.includes.unprocessed_options')
				</tr>
				<?php }
        ?>

    </table>
    @include('injuries.legend')
    <div class="pull-right" style="clear:both;">{{ $injuries->appends(Input::all())->links() }}</div>
</div>


@stop
