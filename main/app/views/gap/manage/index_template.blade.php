@extends('layouts.main')

@section('header')
    <span class="pull-left">
        @yield('page-title')
    </span>
    @include('gap.manage.partials.menu-top')
@stop

@include('gap.manage.partials.nav')

@section('main')
    @include('gap.manage.partials.menu')
    <div id="table-container">
        @yield('table-content')
    </div>

@stop
