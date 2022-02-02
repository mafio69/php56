@extends('layouts.main')

@section('styles')
    @parent
    <link rel="stylesheet" href="/css/bootstrap-table.min.css">
@stop

@section('header')

    Uprawnienia grupy {{ $group->name }}

    <div class="pull-right">
        <a href="{{ URL::to('settings/user/groups') }}" class="btn btn-sm btn-default"><i class="fa fa-arrow-left fa-fw"></i> powrót</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12 text-center marg-btm">
            <a href="{{ URL::to('settings/user/groups/manage-permissions', [$group->id]) }}" class="btn btn-info marg-right">
                <i class="fa fa-cogs fa-fw"></i> zarządzaj
            </a>
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
        <div class="col-sm-12 col-lg-8 col-lg-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Uprawnienia
                </div>
                <table class="table table-hover table-condensed" data-sort-name="module_id" data-sort-order="asc" id="permissions-table">
                    <thead>
                        <th data-sortable="true" data-field="module_id">Moduł</th>
                        <th data-sortable="true" data-field="path">Ścieżka</th>
                        <th data-sortable="true" data-field="name">Nazwa</th>
                    </thead>
                    @foreach($group->permissions as $permission)
                        <tr>
                            <td>{{ $permission->module->name }}</td>
                            <td>{{ $permission->path }}</td>
                            <td>{{ $permission->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
        <div class="col-sm-12 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Historia zmian uprawnień
                </div>
                <table class="table table-hover table-condensed" data-sort-name="date" data-sort-order="asc" id="permissions-history-table">
                    <thead>
                    <th data-sortable="true" data-field="module_id">moduł</th>
                    <th data-sortable="true" data-field="path">ścieżka</th>
                    <th data-sortable="true" data-field="name">nazwa</th>
                    <th data-sortable="true" data-field="action">akcja</th>
                    <th data-sortable="true" data-field="executor">wykonywujący</th>
                    <Th data-sortable="true" data-field="date">data zdarzenia</Th>
                    </thead>
                    @foreach ($group->permissionHistories as $lp => $history)
                        <tr class="vertical-middle">
                            <td>
                                {{ $history->permission->module->name }}
                            </td>
                            <td>
                                {{ $history->permission->path }}
                            </td>
                            <td>
                                {{ $history->permission->name }}
                            </td>
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
                            <td>{{ $history->triggererUser ? $history->triggererUser->name : ''}}</td>
                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop


@section('headerJs')
    @parent
    <script src="/js/bootstrap-table.min.js"></script>
    <script src="/js/bootstrap-table-pl-PL.min.js"></script>
    <script>
        $('#permissions-table, #permissions-history-table').bootstrapTable({
            locale:'pl-PL'
        });
    </script>
@stop
