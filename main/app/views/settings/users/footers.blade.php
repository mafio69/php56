@extends('layouts.main')


@section('header')
    Stopki użytkownika {{ $user_db->name }}
        <div class="pull-right">
            <a href="{{ url('settings/users/footer-add', [$user_db->id]) }}"
               class="btn btn-info btn-xs">
                <i class="fa fa-envelope fa-fw"></i> dodaj stopkę
            </a>
        </div>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="table-auto">
                <table class="table table-condensed table-hover" style="display: block;">
                    <thead>
                        <th >lp.</th>
                        <th>nazwa wewnętrzna</th>
                        <th></th>
                    </thead>
                    @foreach ($user_db->footers as $k => $footer)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td>{{ $footer->name }}</td>
                            <Td>
                                <a target="{{ url('settings/users/footer-show', [$footer->id]) }}"
                                   class="btn btn-info btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-search fa-fw"></i> podgląd
                                </a>
                            </td>
                            <Td>
                                <a href="{{ url('settings/users/footer-edit', [$footer->id]) }}"
                                   class="btn btn-warning btn-xs">
                                    <i class="fa fa-pencil fa-fw"></i> edytuj stopkę
                                </a>
                            </td>
                            <td>
                                <span target="{{ url('settings/users/delete-footer', [$footer->id]) }}"
                                   class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-trash-o fa-fw"></i> usuń stopkę
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

@stop

