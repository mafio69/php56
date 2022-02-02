@extends('layouts.main')

@section('header')
    <span class="pull-left">
        @yield('page-title')
    </span>
    @include('injuries.letters.partials.menu-top')
@stop

@section('main')

    @include('injuries.letters.partials.menu')
    <div class="table-responsive " id="table-container">
        @yield('table-content')
    </div>


@stop

