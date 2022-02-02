@extends('layouts.main')

@section('header')
    Pojazdy
    @if($if_truck == 0)
        < 3.5t
    @else
        > 3.5t
    @endif
    firmy <i>{{ $company->owner->name }}</i> <i><small><strong>({{ $company->name}})</strong></small></i>

    <div class="pull-right">
        <a href="{{ URL::action('VmanageVehiclesController@getCreate', [$company->id]) }}" class="btn btn-small btn-primary">
            <i class="fa fa-plus fa-fw"></i> Dodaj pojazd
        </a>
    </div>
@stop

@section('main')
    @include('vmanage.companies.vehicles.partials.menu')
    <div class="table-responsive">
        <table class="table  table-hover" >
            <thead>
            <th>lp.</th>
            <th>marka i model</th>
            <th>rejestracja</th>
            <th>VIN</th>
            <th>bieżący użytkownik</th>
            <th>bieżący przebieg</th>
            <Th></Th>
            <th></th>
            </thead>
            <?php
            $lp = (($vehicles->getCurrentPage()-1)*$vehicles->getPerPage()) + 1;
            foreach ($vehicles as $vehicle)
            { ?>
            <tr >
                <td width="20px">{{$lp++}}.</td>
                <Td>{{ ($vehicle->brand) ? $vehicle->brand->name : '' }} {{ ($vehicle->model) ? $vehicle->model->name : ''}}</td>
                <td>{{ $vehicle->registration }}</td>
                <td>{{ $vehicle->vin }}</td>
                <td>
                    @if($vehicle->user)
                        {{ $vehicle->user->name}} {{ $vehicle->user->surname}}
                    @else
                        ---
                    @endif
                </td>
                <td>{{ valueIfNotNull($vehicle->actual_mileage) }} km</td>
                <td>
                    <a href="{{ URL::action('VmanageVehiclesController@getShow',[$vehicle->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-file-o fa-fw"></i> kartoteka
                    </a>
                </td>
                <td>
                    <button target="{{ URL::action('VmanageVehiclesController@getDelete', [$vehicle->id]) }}" class="btn btn-sm btn-danger modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-trash-o"></i> usuń</button>
                </td>
            </tr>
            <?php }
            ?>


        </table>
        <div class="pull-right" style="clear:both;">{{ $vehicles->links() }}</div>
    </div>



@stop


