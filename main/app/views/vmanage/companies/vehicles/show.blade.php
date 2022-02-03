@extends('layouts.main')

@section('header')

    Kartoteka pojazdu {{ $vehicle->registration }}

    <div class="pull-right">
        <a href="{{ URL::action('VmanageVehiclesController@getIndex', [$vehicle->vmanage_company_id])}}" class="btn btn-default">Powrót</a>
    </div>
@stop

@section('main')
    @include('vmanage.companies.vehicles.info.nav')
    <div class="tab-content">
        @include('vmanage.companies.vehicles.info.vehicle-data')
        @include('vmanage.companies.vehicles.info.users')
    </div>



@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            var hash = window.location.hash;
            $('#info_tabs a[href="' + hash + '"]').tab('show');
            $('.nav-tabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                if(history.pushState) {
                    history.pushState(null, null, e.target.hash);
                }
                else {
                    location.hash = e.target.hash;
                }
            });

        });
    </script>

@stop