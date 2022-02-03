@extends('layouts.main')


@section('header')

    Karta konta <i>{{ $user->name }}</i>

    <div class="pull-right">
        <a href="{{ URL::to('settings/api/users') }}" class="btn btn-default">powrót</a>
    </div>
@stop

@section('main')
    <div class="row">
        @if($user->trashed())
            <div class="alert alert-danger text-center">
                Konto zablokowane dnia {{ $user->deleted_at->format('Y-m-d H:i') }}
            </div>
        @endif
        <div class="col-sm-12 text-center marg-btm">
                <span target="{{ URL::to('settings/api/users/reset-password', [$user->id]) }}" class="btn btn-warning marg-right modal-open" data-toggle="modal" data-target="#modal">
                    <i class="fa fa-key fa-fw"></i> ustaw hasło
                </span>

                @if($user->trashed() )
                    <span target="{{ URL::to('settings/api/users/unlock-account', [$user->id]) }}" class="btn btn-primary marg-left modal-open" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-unlock fa-fw"></i> odblokuj konto
                    </span>
                @elseif(!$user->trashed() )
                    <span target="{{ URL::to('settings/api/users/lock-account', [$user->id]) }}" class="btn btn-danger marg-left modal-open" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-ban fa-fw"></i> zablokuj konto
                    </span>
                @endif
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-4 ">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Dane konta
                    <span target="{{ URL::to('settings/api/users/edit', [$user->id]) }}" class="btn btn-warning btn-xs modal-open pull-right" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-pencil fa-fw"></i> edytuj
                    </span>
                </div>
                <table class="table table-condensed table-hover">
                    <tr>
                        <th class="text-right">Login:</th>
                        <td>{{ $user->login }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Nazwa:</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Moduły API
                    <span target="{{ URL::to('settings/api/users/manage-modules', [$user->id]) }}" class="btn btn-warning btn-xs modal-open-lg pull-right" data-toggle="modal" data-target="#modal-lg">
                        <i class="fa fa-pencil fa-fw"></i> edytuj
                    </span>
                </div>
                <table class="table table-condensed table-hover">
                    @foreach($user->apiModules as $k => $module)
                        <tr>
                            <td>{{ ++$k }}. {{ $module->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading pointer" data-toggle="collapse" href="#collapse-logins" aria-expanded="false" aria-controls="collapse-logins">
                    Historia zapytań <span class="badge">{{ $user->apiHistories->count() }}</span>
                </div>
                <div class="panel-body collapse" id="collapse-logins">
                    <table class="table table-hover table-condensed">
                        <thead>
                        <Th style="width:30px;">#</th>
                        <th>data</th>
                        <th>ip</th>
                        </thead>
                        @foreach($user->apiHistories as $k => $login)
                            <tr>
                                <td>{{ ++$k }}.</td>
                                <td>{{ $login->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $login->ip }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop


