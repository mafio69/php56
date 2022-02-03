@extends('layouts.main')


@section('header')
    Moduły API
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="table-auto">
                <table class="table table-condensed table-hover" style="display: block;">
                    <thead>
                        <th >lp.</th>
                        <th >Nazwa modułu</th>
                        <th >Klucze API</th>
                        <th>Historia</th>
                    </thead>
                    @foreach ($modules as $lp => $module)
                        <tr>
                            <td>{{++$lp}}.</td>
                            <Td>{{$module->name}}</td>
                            <td>
                                <a href="{{ url('settings/api/modules/keys', [$module->id]) }}" class="btn btn-primary btn-xs">
                                    <i class="fa fa-key fa-fw"></i> klucze API <span class="badge">{{ $module->apiKeys->count() }}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{ url('settings/api/modules/history', [$module->id]) }}" class="btn btn-primary btn-xs">
                                    <i class="fa fa-search fa-fw"></i> historia zapytań
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop

@section('headerJs')
    @parent

@endsection
