@extends('layouts.main')

@section('header')
   Przypisania indywidualne
    <a class="btn btn-primary btn-xs pull-right" href="{{ URL::to('tasks/assignments/create') }}" >
        <i class="fa fa-plus fa-fw"></i>
        dodaj
    </a>
@stop

@section('main')

    <div class="row">
        <div class="panel panel-default col-sm-12 col-md-10 col-lg-8 col-lg-offset-2">
            <table class="table table-hover  table-condensed">
                <thead>
                <Th style="width:30px;">lp.</th>
                <th>email nadawcy</th>
                <th>pracownik</th>
                <th></th>
                </thead>
                @foreach ($assignments as $k => $assignment )
                    <tr>
                        <td>{{ ++ $k }}.</td>
                        <td>
                            {{ $assignment->email_from }}
                        </td>
                        <td>{{ $assignment->user->name }}</td>
                        <td>
                            <span class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/assignments/delete', [$assignment->id]) }}" >
                                <i class="fa fa-trash-o fa-fw"></i>
                                usuń
                            </span>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop

