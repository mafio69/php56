@extends('layouts.main')


@section('header')

    Karta użytkownika <i>{{ $user->name }}</i>

    <div class="pull-right">
        <a href="{{ URL::to('settings/users') }}" class="btn btn-default">powrót</a>
    </div>
@stop

@section('main')
    <div class="row">
        @if($user->locked_at)
            <div class="alert alert-danger text-center">
                Konto zablokowane dnia {{ $user->locked_at->format('Y-m-d H:i') }}
            </div>
        @endif
        @if($user->password_expired_at && \Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)<1)
            <div class="alert alert-warning text-center">

                @if(! $user->password)
                    Konto wymaga ustawienia hasła startowego.
                @elseif(! $user->logins->first())
                    Konto oczekuje na pierwsze logowanie.
                @else
                    Konto nieaktywne z dniem {{ $user->password_expired_at->format('Y-m-d') }}
                @endif
            </div>
        @endif
        <div class="col-sm-12 text-center marg-btm">
            @if(Auth::user()->can('uzytkownicy#podglad_uzytkownika#ustawianie_hasla'))
                <span target="{{ URL::to('settings/users/reset-password', [$user->id]) }}" class="btn btn-warning marg-right modal-open" data-toggle="modal" data-target="#modal">
                    <i class="fa fa-key fa-fw"></i> ustaw hasło startowe
                </span>
            @endif

            @if(Auth::user()->can('uzytkownicy#podglad_uzytkownika#blokowanie_konta'))
                @if($user->locked_at )
                    <span target="{{ URL::to('settings/users/unlock-account', [$user->id]) }}" class="btn btn-primary marg-left modal-open" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-unlock fa-fw"></i> odblokuj konto
                    </span>
                @elseif(!$user->locked_at )
                    <span target="{{ URL::to('settings/users/lock-account', [$user->id]) }}" class="btn btn-danger marg-left modal-open" data-toggle="modal" data-target="#modal">
                        <i class="fa fa-ban fa-fw"></i> zablokuj konto
                    </span>
                @endif
            @endif
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-4 ">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Dane użytkownika
                    @if(Auth::user()->can('uzytkownicy#edycja_uzytkownika#wejscie'))
                        <span target="{{ URL::to('settings/users/edit', [$user->id]) }}" class="btn btn-warning btn-xs modal-open pull-right" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-pencil fa-fw"></i> edytuj
                        </span>
                    @endif
                </div>
                <table class="table table-condensed table-hover">
                    <tr>
                        <th class="text-right">Login:</th>
                        <td>{{ $user->login }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Imię/Nazwisko:</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Email:</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Data ważności konta</th>
                        <td>
                            @if($user->active_to)
                                {{ $user->active_to->format('Y-m-d') }}
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Pracownik zewnętrzny</th>
                        <td>@if($user->is_external) <i class="fa fa-check"></i> @else <i class="fa fa-minus"></i> @endif</td>
                    </tr>
                    <tr>
                        <th class="text-right">Bez ograniczeń właściciel</th>
                        <td>
                            @if($user->without_restrictions)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Bez ograniczeń flota</th>
                        <td>
                            @if($user->without_restrictions_vmanage)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Bez ograniczeń zadania</th>
                        <td>
                            @if($user->without_restrictions_task_group)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Numer telefonu</th>
                        <td>
                            {{ $user->phone_number ? $user->phone_number : '<i class="fa fa-minus"></i>' }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Dział</th>
                        <td>
                            @if($user->department)
                                {{ $user->department->name }}
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right">Zespół</th>
                        <td>
                            @if($user->team)
                                {{ $user->team->name }}
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Alternatywne adresy email
                    @if(Auth::user()->can('uzytkownicy#edycja_uzytkownika#wejscie'))
                        <span target="{{ URL::to('settings/users/add-email', [$user->id]) }}" class="btn btn-default btn-xs modal-open pull-right" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-plus fa-fw"></i> dodaj
                        </span>
                    @endif
                </div>
                <table class="table table-condensed table-hover">
                    @foreach($user->emails as $email)
                        <tr>
                            <th class="text-right">
                                {{ $email->email }}
                            </td>
                            <td>
                                 <span target="{{ url('settings/users/delete-email', [$email->id]) }}"
                                       class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-trash-o fa-fw"></i> usuń email
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Grupy użytkownika
                    @if(Auth::user()->can('uzytkownicy#edycja_uzytkownika#grupy'))
                        <span target="{{ URL::to('settings/users/add-groups', [$user->id]) }}" class="btn btn-warning btn-xs modal-open pull-right" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-pencil fa-fw"></i> edytuj
                        </span>
                    @endif
                </div>
                <table class="table table-condensed table-hover">
                    @foreach($user->groups as $k => $group)
                        <tr>
                            <td>{{ ++$k }}. {{ $group->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Właściciele
                    <span target="{{ URL::to('settings/users/manage-contractors', [$user->id]) }}" class="btn btn-warning btn-xs modal-open-lg pull-right" data-toggle="modal" data-target="#modal-lg">
                        <i class="fa fa-pencil fa-fw"></i> edytuj
                    </span>
                </div>
                <table class="table table-condensed table-hover">
                    @foreach($user->owners as $k => $owner)
                        <tr>
                            <td>{{ ++$k }}. {{ $owner->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Floty
                    <span target="{{ URL::to('settings/users/manage-companies', [$user->id]) }}" class="btn btn-warning btn-xs modal-open-lg pull-right" data-toggle="modal" data-target="#modal-lg">
                        <i class="fa fa-pencil fa-fw"></i> edytuj
                    </span>
                </div>
                <table class="table table-condensed table-hover">
                    @foreach($user->vmanage_companies as $k  => $vmanage_company)
                        <tr>
                            <td>{{ ++$k }}. {{ $vmanage_company->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Grupy zadań
                    <span target="{{ URL::to('settings/users/manage-tasks', [$user->id]) }}" class="btn btn-warning btn-xs modal-open-lg pull-right" data-toggle="modal" data-target="#modal-lg">
                        <i class="fa fa-pencil fa-fw"></i> edytuj
                    </span>
                </div>
                <table class="table table-condensed table-hover">
                    @foreach($user->taskGroups as $k => $taskGroup)
                        <tr>
                            <td>
                                {{ ++$k }}.
                                {{ $taskGroup->name }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading pointer" data-toggle="collapse" href="#collapse-group-history" aria-expanded="false" aria-controls="collapse-group-history">
                    Historia zmian grup <span class="badge">{{ $user->groupHistories->count() }}</span>
                </div>
                <div class="panel-body collapse" id="collapse-group-history">
                    <table class="table table-hover  table-condensed">
                        <thead>
                        <Th style="width:30px;">#</th>
                        <th>grupa</th>
                        <th>akcja</th>
                        <th>wykonywujący</th>
                        <Th>data zdarzenia</Th>
                        </thead>
                        @foreach ($user->groupHistories as $lp => $history)
                            <tr class="vertical-middle">
                                <td>{{ ++$lp }}.</td>
                                <td>{{ $history->userGroup->name }}</td>
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
            <div class="panel panel-default">
                <div class="panel-heading pointer" data-toggle="collapse" href="#collapse-contractors-history" aria-expanded="false" aria-controls="collapse-contractors-history">
                    Historia zmian właścicieli <span class="badge">{{ $user->ownerHistories->count() }}</span>
                </div>
                <div class="panel-body collapse" id="collapse-contractors-history">
                    <table class="table table-hover  table-condensed">
                        <thead>
                        <Th style="width:30px;">#</th>
                        <th>właściciel</th>
                        <th>akcja</th>
                        <th>wykonywujący</th>
                        <Th>data zdarzenia</Th>
                        </thead>
                        @foreach ($user->ownerHistories as $lp => $history)
                            <tr class="vertical-middle">
                                <td>{{ ++$lp }}.</td>
                                <td>{{ $history->owner->name }}</td>
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
            <div class="panel panel-default">
                <div class="panel-heading pointer" data-toggle="collapse" href="#collapse-companies-history" aria-expanded="false" aria-controls="collapse-companies-history">
                    Historia zmian floty <span class="badge">{{ $user->vmanage_companyHistories->count() }}</span>
                </div>
                <div class="panel-body collapse" id="collapse-companies-history">
                    <table class="table table-hover  table-condensed">
                        <thead>
                        <Th style="width:30px;">#</th>
                        <th>firma</th>
                        <th>akcja</th>
                        <th>wykonywujący</th>
                        <Th>data zdarzenia</Th>
                        </thead>
                        @foreach ($user->vmanage_companyHistories as $lp => $history)
                            <tr class="vertical-middle">
                                <td>{{ ++$lp }}.</td>
                                <td>{{ $history->vmanage_company->name }}</td>
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

            <div class="panel panel-default">
                <div class="panel-heading pointer" data-toggle="collapse" href="#collapse-task-groups-history" aria-expanded="false" aria-controls="collapse-task-groups-history">
                    Historia zmian gróp zadań <span class="badge">{{ $user->taskGroupHistories->count() }}</span>
                </div>
                <div class="panel-body collapse" id="collapse-task-groups-history">
                    <table class="table table-hover  table-condensed">
                        <thead>
                        <Th style="width:30px;">#</th>
                        <th>grupa</th>
                        <th>akcja</th>
                        <th>wykonywujący</th>
                        <Th>data zdarzenia</Th>
                        </thead>
                        @foreach ($user->taskGroupHistories as $lp => $history)
                            <tr class="vertical-middle">
                                <td>{{ ++$lp }}.</td>
                                <td>{{ $history->taskGroup->name }}</td>
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

            <div class="panel panel-default">
                <div class="panel-heading pointer" data-toggle="collapse" href="#collapse-logins" aria-expanded="false" aria-controls="collapse-logins">
                    Historia logowań <span class="badge">{{ $user->logins->count() }}</span>
                </div>
                <div class="panel-body collapse" id="collapse-logins">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <Th style="width:30px;">#</th>
                            <th>data</th>
                            <th>ip</th>
                        </thead>
                        @foreach($user->logins as $k => $login)
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


