@extends('layouts.main')

@section('header')
    Historia zmian uprawnień dla roli <i>{{ $role->name }}</i>

    <a href="{{ url('settings/roles') }}" class="btn btn-default pull-right">
        <i class="fa fa-arrow-left fa-fw"></i> powrót
    </a>
@stop

@section('main')
    <div class="col-sm-12 col-md-8">
        <div id="table-container">
            <table class="table table-hover  table-condensed" id="users-table">
                <thead>
                <Th style="width:30px;">#</th>
                <th>uprawnienie</th>
                <th>akcja</th>
                <th>wykonywujący</th>
                <Th>data zdarzenia</Th>
                </thead>
                @foreach ($role->histories as $lp => $history)
                    <tr class="vertical-middle">
                        <td>{{ ++$lp }}.</td>
                        <td>{{ $history->permission->display_name }}</td>
                        <td>
                            @if($history->mode == 'attach')
                                <span class="label label-success">
                                    <i class="fa fa-plus fa-fw"></i> nadanie
                                </span>
                            @else
                                <span class="label label-danger">
                                    <i class="fa fa-minus fa-fw"></i> odebranie
                                </span>
                            @endif
                        </td>
                        <td>{{ $history->triggererUser->name }}</td>
                        <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="col-sm-12 col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                Aktualne uprawnienia roli
            </div>
            <div class="panel-body">
                <table class="table table-hover table-condensed">
                    @foreach($role->perms as $perm)
                        <tr>
                            <td>
                                {{ $perm->display_name }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop

