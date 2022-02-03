@extends('layouts.main')

@section('header')

    <button target="{{ url('settings/contractor-groups/create') }}" class="btn btn-primary btn-sm modal-open pull-right" data-toggle="modal" data-target="#modal">
        <i class="fa fa-plus"></i> dodaj
    </button>
  Słownik grup kontrahentów

@stop

@section('main')
    <div class="marg-top">
        <table class="table table-bordered table-condensed table-middle table-auto  table-hover ">
            <thead>
                <th >lp.</th>
                <th>Nazwa</th>
                <th></th>
                <th></th>
            </thead>
            @foreach ($contractor_groups as $k => $contractor_group)
                <tr>
                    <td>{{++$k}}.</td>
                    <td>
                        {{ $contractor_group->name }}
                    </td>
                      <td>
                          <button target="{{ url('settings/contractor-groups/edit', $contractor_group->id) }}" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal">
                              <i class="fa fa-edit"></i> edytuj
                          </button>
                      </td>
                    <td>
                        <button target="{{ url('settings/contractor-groups/delete', $contractor_group->id) }}" class="btn btn-danger btn-sm modal-open" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-trash-o fa-fw"></i> usuń
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>


@stop
