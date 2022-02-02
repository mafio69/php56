@extends('layouts.main')


@section('header')

    Grupy użytkowników

    @if(Auth::user()->can('grupy#dodawanie_grupy#wejscie'))
        <div class="pull-right">
            <button target="{{ URL::to('settings/user/groups/create') }}" class="btn btn-small btn-primary modal-open" data-toggle="modal" data-target="#modal" >
                <i class="fa fa-plus fa-fw"></i> Utwórz grupę użytkowników
            </button>
        </div>
    @endif
@stop

@section('main')

    <div class="row marg-top">
        <div class="col-sm-12">
            <table class="table table-condensed table-hover" style="display: block">
                <thead>
                    <th>lp.</th>
                    <th>nazwa grupy</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>

                <?php $lp = 1;?>
                @foreach ($groups as $group)
                <tr >
                    <td width="20px">{{$lp++}}.</td>
                    <Td>{{ $group->name}}</td>
                    <td>
                        <a href="{{ URL::to('settings/user/groups/permissions', [$group->id]) }}" class="btn btn-primary btn-xs" @if(! Auth::user()->can('grupy#edycja_grupy#uprawnienia_dla_grupy')) disabled="disabled" @endif>
                            <span class="pull-left"><i class="fa fa-database fa-fw"></i> uprawnienia</span>
                            <span class="badge pull-right marg-left">{{ $group->permissions->count() }}</span>
                        </a>
                    </td>
                    <td>
                        <a href="{{ URL::to('settings/user/groups/show', [$group->id]) }}" class="btn btn-primary btn-xs"  @if(! Auth::user()->can('grupy#edycja_grupy#uzytkownicy_dla_grupy')) disabled="disabled" @endif>
                            <span class="pull-left"><i class="fa fa-users fa-fw"></i> użytkownicy</span>
                            <span class="badge pull-right marg-left">{{ $group->users->count() }}</span>
                        </a>
                    </td>
                    <td>
                        @if(Auth::user()->can('grupy#edycja_grupy#wejscie'))
                            <span class="btn btn-warning btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ url('settings/user/groups/edit', [$group->id]) }}">
                                <i class="fa fa-fw fa-pencil"></i>
                                edytuj ustawienia grupy
                            </span>
                        @endif
                    </td>
                    <Td>
                        @if(Auth::user()->can('grupy#lista_grup#usuwanie_grupy'))
                            <button target="{{ URL::to('settings/user/groups/delete', [$group->id]) }}" class="btn btn-xs btn-danger modal-open" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-trash-o"></i> usuń
                            </button>
                        @endif
                    </Td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>


@stop


