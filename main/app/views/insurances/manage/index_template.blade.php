@extends('layouts.main')

@section('header')
    <span class="pull-left">
        @yield('page-title')
    </span>
    @include('insurances.manage.partials.menu-top')
@stop

@include('insurances.manage.partials.nav')

@section('main')
    @include('insurances.manage.partials.menu')
    <div id="table-container">
        @yield('table-content')
    </div>

@stop


@section('headerJs')
    @parent
    <script>

        $('[data-toggle="tooltip"]').tooltip()

    </script>
@endsection
