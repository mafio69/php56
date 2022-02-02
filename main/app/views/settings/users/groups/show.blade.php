@extends('layouts.main')


@section('header')

   Użytkownicy w grupie {{ $group->name }}

    <div class="pull-right">
        <a href="{{ url('settings/user/groups/manage', [$group->id]) }}" class="btn btn-sm btn-info"><i class="fa fa-cogs fa-fw"></i> zarządzaj</a>
        <a href="{{ URL::previous() }}" class="btn btn-sm btn-default">powrót</a>
    </div>
@stop

@section('main')
    <div class="row marg-top">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-condensed table-hover" id="users-table">
                    <thead>
                    <th >lp.</th>
                    <th >Login</th>
                    <th>Email</th>
                    <th >Nazwisko</th>
                    <th >Dodany</th>
                    <th ></th>
                    </thead>
                    <?php $lp = 1;?>
                    @foreach ($group->users as $k => $user)
                        <tr class="odd gradeX">
                            <td>{{$lp++}}.</td>
                            <Td>{{$user->login}}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{$user->name}}</td>
                            <Td>{{ substr($user->created_at, 0, -3)}}</td>
                            <td>
                                <a href="{{ url('settings/users/show', [$user->id]) }}" class="btn btn-primary btn-xs">
                                    <i class="fa fa-search fa-fw"></i> karta użytkownika
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop


