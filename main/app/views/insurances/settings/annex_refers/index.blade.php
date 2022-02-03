@extends('layouts.main')

@section('header')

    <button target="/settings/insurance-annex-refer/create/" class="btn btn-primary btn-sm modal-open pull-right" data-toggle="modal" data-target="#modal">
        <i class="fa fa-plus"></i> dodaj
    </button>
  Słownik odniesień aneksów ubezpieczeń

@stop

@section('main')
    <div class="marg-top">
        <table class="table table-bordered table-condensed table-middle table-auto  table-hover ">
            <thead>
                <th >lp.</th>
                <th>Nazwa</th>
                <th></th>
            </thead>
            @foreach ($annex_refers as $k => $refer)
                <tr>
                    <td>{{++$k}}.</td>
                    <td>
                        {{ $refer->name }}
                    </td>
                      <td>
                          <button target="/settings/insurance-annex-refer/edit/{{$refer->id}}" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal">
                              <i class="fa fa-edit"></i> edytuj
                          </button>
                      </td>
            @endforeach
        </table>
    </div>


@stop
