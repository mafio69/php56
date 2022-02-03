@extends('layouts.main')

@section('header')


    Słownik zespołów

    <span class="btn btn-primary pull-right modal-open" data-toggle="modal" data-target="#modal" target="{{ url('settings/teams/create') }}">
        <i class="fa fa-plus fa-fw"></i> dodaj
    </span>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12 col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <table class="table table-bordered table-condensed table-middle  table-hover ">
                    <thead>
                        <th width="20">lp.</th>
                        <th class="text-center">Nazwa</th>
                        <th></th>
                        <th></th>
                    </thead>
                    @foreach ($teams as $k => $team)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td>
                                {{ $team->name }}
                            </td>
                            <td>
                                <button target="{{ url('settings/teams/edit', $team->id) }}" class="btn btn-warning btn-sm modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-edit fa-fw"></i> edytuj
                                </button>
                            </td>
                            <td>
                                <button target="{{ url('settings/teams/delete', $team->id) }}" class="btn btn-danger btn-sm modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-trash-o fa-fw"></i> usuń
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

        </div>
    </div>

    @include('modules.modals')
@stop
