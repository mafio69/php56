@extends('layouts.main')

@section('header')
<span class="pull-left">
Zlecenia (szkody) - ea
</span>
@include('injuries.menu-top')

@stop

@include('injuries.nav')

@section('main')

@include('injuries.menu')

<div class="row">
    <div class="col-sm-2 text-center">
        <span class="lead ">
            Program sprzedaży
        </span>
    </div>
    <div class="col-sm-10">
        <ul class="nav nav-tabs">
            @foreach($sales_programs as $sales_program => $count)
                <li role="presentation" @if($sales_program == Request::segment(3)) class="active" @endif>
                    <a href="{{ url('injuries/ea', [$sales_program]) }}">
                        {{ $sales_program ? $sales_program : 'brak programu' }}
                        <span class="badge">{{ $count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="col-sm-12" id="injuries-container">
        <table class="table  table-hover  table-condensed" id="users-table">
            <thead>
                <Th style="width:30px;">lp.</th>
                <th >rejestracja</th>
                <th >nr umowy</th>
                <th >data zdarzenia</th>
                <th >miejsce zdarzenia</th>
                <th>typ szkody</th>
                <th>zgłaszający</th>
                <Th>data zgłoszenia</th>
                <th>uszkodzenia</th>
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
                        <Td>{{ checkIfEmpty($injury->vehicle_registration) }}</td>
                        <td>{{ checkIfEmpty($injury->contract_number) }}</td>
                        <td>{{ checkIfEmpty($injury->injury_event_date) }}</td>
                        <Td>{{ checkIfEmpty($injury->injury_event_city) }}</td>
                        <td>
                            {{ $injury->injury_type }}
                        </td>
                        <td>
                            {{ $injury->claimant_email }}
                            <br>
                            {{ $injury->claimant_surname }} {{ $injury->claimant_name }} {{ $injury->claimant_phone }}
                        </td>
                        <td>{{substr($injury->created_at, 0, -3)}}</td>
                        <td>
                            {{ $injury->injury_damage_description }}
                        </td>
                        @include('injuries.includes.ea_options')
                    </tr>
                    <?php }
            ?>

        </table>
        <div class="pull-right" style="clear:both;">{{ $injuries->appends(Input::all())->links() }}</div>
    </div>
</div>

@stop
