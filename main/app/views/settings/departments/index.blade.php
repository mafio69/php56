@extends('layouts.main')

@section('header')


    Słownik działów

    <span class="btn btn-primary pull-right modal-open" data-toggle="modal" data-target="#modal" target="{{ url('settings/departments/create') }}">
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
                    @foreach ($departments as $k => $department)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td>
                                {{ $department->name }}
                            </td>
                            <td>
                                <button target="{{ url('settings/departments/edit', $department->id) }}" class="btn btn-warning btn-sm modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-edit fa-fw"></i> edytuj
                                </button>
                            </td>
                            <td>
                                <button target="{{ url('settings/departments/delete', $department->id) }}" class="btn btn-danger btn-sm modal-open" data-toggle="modal" data-target="#modal">
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