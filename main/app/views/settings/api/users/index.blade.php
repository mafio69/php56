@extends('layouts.main')


@section('header')
    Konta API
    <div class="pull-right">
        <button target="{{ URL::to('settings/api/users/create') }}" class="btn btn-small btn-primary modal-open" data-toggle="modal" data-target="#modal">
            <span class="fa fa-plus"></span> Dodaj konto
        </button>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="table-auto">
                <table class="table table-condensed table-hover" style="display: block;">
                    <thead>
                    <th >lp.</th>
                    <th>Login</th>
                    <th>Nazwisko</th>
                    <th>Data rejestracji</th>
                    <th ></th>
                    </thead>
                    <?php $lp = (($users->getCurrentPage()-1)*$users->getPerPage()) + 1;?>
                    @foreach ($users as $k => $user)
                        <tr @if($user->trashed()) class="bg-danger" @endif >
                            <td>{{$lp++}}.</td>
                            <Td>{{$user->login}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ url('settings/api/users/show', [$user->id]) }}" class="btn btn-primary btn-xs">
                                    <i class="fa fa-search fa-fw"></i> karta konta
                                </a>
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

@endsection
