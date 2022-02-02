@extends('layouts.main')


@section('header')
    Użytkownicy
    @if(Auth::user()->can('uzytkownicy#dodawanie_uzytkownika#wejscie'))
        <div class="pull-right">
            <button target="{{ URL::to('settings/users/create') }}" class="btn btn-small btn-primary modal-open" data-toggle="modal" data-target="#modal">
                <span class="fa fa-plus"></span> Dodaj użytkownika
            </button>
        </div>
    @endif
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <nav class="navbar navbar-default navbar-sm marg-top-min" >
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                        <form class="navbar-form navbar-left allow-confirm flex" style="display: flex;" role="search" id="search-form">
                            <div class="form-group form-group-sm text-center" style="border-right: 1px solid #000; padding-right: 10px; margin-right:10px; width: 130px;">
                                <label>Filtrowanie</label><br/>
                                <button type="submit" class="btn btn-xs btn-primary">
                                    <i class="fa fa-search fa-fw"></i> filtruj <span class="badge">{{ $users->getTotal() }}</span>
                                </button><br />
                                <a class="btn btn-xs btn-danger marg-top-min" href="{{ Request::url() }}">
                                    <i class="fa fa-remove fa-fw"></i> usuń filtry
                                </a>
                            </div>
                            <div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group form-group-sm ">
                                            <label>Login:</label><br>
                                            <input class="form-control" autocomplete="off" name="filter_login" type="text" value="{{ Request::has('filter_login')?Request::get('filter_login'):'' }}">
                                        </div>
                                        <div class="form-group form-group-sm ">
                                            <label>Nazwisko:</label><br>
                                            <input class="form-control" autocomplete="off" name="filter_name" type="text" value="{{ Request::has('filter_name')?Request::get('filter_name'):'' }}">
                                        </div>
                                        <div class="form-group form-group-sm ">
                                            <label>Email:</label><br>
                                            <input class="form-control" autocomplete="off" name="filter_email" type="text" value="{{ Request::has('filter_email')?Request::get('filter_registration'):'' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col-sm-12">
            <div class="table-auto">
                <table class="table table-condensed table-hover" style="display: block;">
                    <thead>
                        <th >lp.</th>
                        <th >Login</th>
                        <th >Nazwisko</th>
                        <th>Email</th>
                        <th>Data rejestracji</th>
                        <th>Ostatnie logowanie</th>
                        <th>Data wygaśnięcia hasła</th>
                        <th>Status</th>
                        <th ></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </thead>
                    <?php $lp = (($users->getCurrentPage()-1)*$users->getPerPage()) + 1;?>
                    @foreach ($users as $k => $user)
                        <tr class="@if($user->locked_at) bg-danger @elseif(! $user->logins->first() || ($user->password_expired_at && \Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)<1)) bg-warning @endif ">
                            <td>{{$lp++}}.</td>
                            <Td>{{$user->login}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                {{ $user->logins->first() ? $user->logins->reverse()->first()->created_at->format('Y-m-d H:i') : '---' }}
                            </td>
                            <td>
                                {{ $user->password_expired_at ? $user->password_expired_at->format('Y-m-d') : '' }}
                            </td>
                            <td>
                                @if( ! $user->logins->first())
                                    <span class="label label-warning marg-right">
                                        pierwsze logowanie
                                    </span>
                                @endif

                                @if(($user->password_expired_at && \Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)<1))
                                    <span class="label label-default marg-right">
                                        hasło wygasło
                                    </span>
                                @endif

                                @if($user->locked_at)
                                    <span class="label label-danger">
                                        konto zablokowane
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('uzytkownicy#podglad_uzytkownika#wejscie'))
                                    <a href="{{ url('settings/users/show', [$user->id]) }}" class="btn btn-primary btn-xs">
                                        <i class="fa fa-search fa-fw"></i> karta użytkownika
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($user->signature)
                                <span class="btn btn-xs btn-info" data-toggle="popover" data-trigger="hover" data-content="<img style='max-width:250px;' src='{{ url('settings/users/show-signature', [$user->signature]) }}'/>">
                                    <i class="fa fa-picture-o fa-fw"></i> podpis
                                </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('settings/users/footers', [$user->id]) }}"
                                      class="btn btn-info btn-xs">
                                    <i class="fa fa-envelope fa-fw"></i> stopki
                                    <span class="badge">{{ $user->footers->count() }}</span>
                                </a>
                            </td>
                            <td>
                                @if(Auth::user()->can('uzytkownicy#lista_uzytkownikow#ustawianie_podpisu'))
                                    <span class="btn btn-xs btn-warning modal-open"
                                          target="{{ url('settings/users/signature', [$user->id]) }}"
                                          data-toggle="modal"
                                          data-target="#modal">
                                        <i class="fa fa-upload fa-fw"></i> podpis
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="pull-left" style="clear:both;">{{  $users->links()  }}</div>
            </div>
        </div>
    </div>

@stop

@section('headerJs')
    @parent
    <script>
        $(function () {
            $('[data-toggle="popover"]').popover({ html: true})
        })
    </script>
@endsection
