@extends('layouts.main')


@section('header')
    Kucze API dla {{ $module->name }}

    <div class="pull-right">
        <a href="{{ URL::to('settings/api/modules') }}" class="btn btn-small btn-default">
            <span class="fa fa-arrow-left"></span> Powrót
        </a>
        <button target="{{ URL::to('settings/api/modules/create-key', [$module->id]) }}" class="btn btn-small btn-primary modal-open" data-toggle="modal" data-target="#modal">
            <span class="fa fa-plus"></span> Dodaj klucz
        </button>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="table-auto">
                <table class="table table-condensed table-hover" style="display: block;">
                    <thead>
                        <th>lp.</th>
                        <th>Klucz API</th>
                        <th></th>
                    </thead>
                    @foreach ($keys as $lp => $key)
                        <tr @if($key->trashed()) class="bg-danger" @endif>
                            <td>{{++$lp}}.</td>
                            <Td>{{$key->api_key}}</td>
                            <td>
                                @if(! $key->trashed())
                                 <span class="btn btn-xs btn-warning modal-open"
                                       target="{{ url('settings/api/modules/disactivate-key', [$key->id]) }}"
                                       data-toggle="modal"
                                       data-target="#modal">
                                        <i class="fa fa-ban fa-fw"></i> dezaktywuj
                                    </span>
                                @else
                                    <span class="label label-danger">{{ $key->deleted_at->format('Y-m-d H:i') }}</span>
                                @endif
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
