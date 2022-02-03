@extends('layouts.main')


@section('header')

    Opiekunowie floty dla {{ $company->owner->name }} <small><i>( {{ $company->name }} )</i></small>

    <div class="pull-right">
        <a href="{{ URL::action('VmanageCompanyGuardiansController@getCreate', [$company->id]) }}" class="btn btn-small btn-primary">
            <i class="fa fa-plus fa-fw"></i> Dodaj opiekuna floty
        </a>
        <a href="{{ URL::action('VmanageCompaniesController@getIndex') }}" class="btn btn-small btn-default">
            Powrót
        </a>
    </div>
@stop

@section('main')
    <div class="table-responsive">
        <table class="table table-hover table-condensed">
            <thead>
                <th>lp.</th>
                <th>login</th>
                <th>nazwisko</th>
                <th ></th>
                <th></th>
                <Th></Th>
            </thead>
            <?php
            $lp = (($guardians->getCurrentPage()-1)*$guardians->getPerPage()) + 1;
            foreach ($guardians as $guardian)
            { ?>
            <tr class="vertical-middle">
                <td width="20px">{{$lp++}}.</td>
                <Td>{{ $guardian->login}}</td>
                <td>{{ $guardian->name }}</td>
                <td>
                    <button target="{{ URL::action('VmanageCompanyGuardiansController@getEdit', array($guardian->id)) }}" class="btn btn-warning btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i> edytuj</button>
                </td>
                <td>
                    <button target="{{ URL::action('VmanageCompanyGuardiansController@getPassword', array($guardian->id)) }}" class="btn btn-warning btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-key"></i> hasło</button>
                </td>
                <td>
                    <button target="{{ URL::action('VmanageCompanyGuardiansController@getDelete', array($guardian->id, $company->id)) }}" class="btn btn-danger btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-trash-o"></i> usuń</button>
                </td>
            </tr>
            <?php }
            ?>


        </table>
        <div class="pull-right" style="clear:both;">{{ $guardians->links() }}</div>
    </div>



@stop


