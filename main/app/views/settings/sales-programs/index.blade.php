@extends('layouts.main')

@section('header')


    Słownik kodów programów sprzedaży

@stop

@section('main')
    <div class="row">
        <div class="col-sm-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Kody DLS
                </div>
                <table class="table table-bordered table-condensed table-middle  table-hover ">
                    <thead>
                    <th width="20">lp.</th>
                    <th class="text-center">Nazwa</th>
                    <th class="text-center">Kod programu</th>
                    <th></th>
                    </thead>
                    @foreach ($dls_programs as $k => $dls_program)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td>
                                {{ $dls_program->name }}
                            </td>
                            <td>
                                {{ $dls_program->name_key }}
                            </td>
                            <td>
                                <button target="{{ url('settings/sales-programs/edit', $dls_program->id) }}" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-edit"></i> edytuj
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Kody SYJON
                </div>
                <table class="table table-bordered table-condensed table-middle  table-hover ">
                    <thead>
                    <th width="20">lp.</th>
                    <th class="text-center">Nazwa</th>
                    <th class="text-center">Kod programu</th>
                    </thead>
                    @foreach ($syjon_programs as $k => $syjon_program)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td>
                                {{ $syjon_program->name }}
                            </td>
                            <td>
                                {{ $syjon_program->name_key }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>



@stop
