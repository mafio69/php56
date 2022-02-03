@extends('layouts.main')

@section('header')
    Książka adresowa
    <span target="{{ url('tasks/address-book/create') }}"
          class="btn btn-primary modal-open pull-right" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-plus fa-fw"></i> dodaj
                                </span>

@stop

@section('main')

    <div class="row">
        <div class="panel panel-default col-sm-12 col-md-10">
            <table class="table table-hover table-condensed table-auto">
                <thead>
                <Th style="width:30px;">lp.</th>
                <th>Email</th>
                <th>Nazwa</th>
                <th></th>
                <th></th>
                </thead>
                @foreach ($addresses as $k => $entity )
                    <tr class="vertical-middle">
                        <td>{{ ++ $k }}.</td>
                        <td>
                            {{ $entity->email }}
                        </td>
                        <td>
                            {{ $entity->name }}
                        </td>
                        <td>
                            <span target="{{ url('tasks/address-book/edit', [$entity->id]) }}"
                                  class="btn btn-warning btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-pencil fa-fw"></i> edytuj
                            </span>
                        </td>
                        <td>
                            <span target="{{ url('tasks/address-book/delete', [$entity->id]) }}"
                                  class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-trash-o fa-fw"></i> usuń
                            </span>
                        </td>
                    </tr>
                @endforeach
            </table>
            <div class="pull-right" style="clear:both;">{{ $addresses->appends(Input::query())->links() }}</div>
        </div>
    </div>
@stop


