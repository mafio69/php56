@extends('layouts.main')


@section('header')

    {{$plan->name }}

    <div class="pull-right">
        <a href="{{ URL::to('plans') }}" class="btn btn-default">Powrót</a>
    </div>

@stop

@section('main')

    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-primary small">
                <div class="panel-heading">Dane programu
                    @if(Auth::user()->can('programy#zarzadzaj'))
                        <a href="{{ URL::to('plans/edit', array($plan->id)) }}" class="btn btn-warning btn-xs pull-right"><i class="fa fa-pencil fa-fw"></i> edytuj</a>
                    @endif
                </div>
                <table class="table">
                    <tr>
                        <td class="text-right"><label>Nazwa:</label></td>
                        <td>{{ $plan->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Kod programu:</label></td>
                        <td>{{ $plan->sales_program }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-8">
            <div class="panel panel-default small">
                <div class="panel-heading">
                    Lista grup

                    @if(Auth::user()->can('programy#zarzadzaj'))
                        <a href="{{ URL::to('plan/groups/create', array($plan->id)) }}" class="btn btn-xs btn-primary pull-right">
                            <span class="glyphicon glyphicon-plus-sign"></span> Dodaj grupę
                        </a>
                    @endif
                </div>
                <table class="table  table-hover table-condensed" >
                    <thead>
                    <th></th>
                    <th>nazwa</th>
                    <th>kolejność</th>
                    <th>liczba serwisów</th>
                    <th>lista warunkowa</th>
                    <th></th>
                    <th></th>
                    </thead>
                    @foreach ($plan->groups as $k => $group)
                        <tr class="vertical-middle">
                            <Td width="10px">{{ ++$k }}.</Td>
                            <td>{{ $group->name }}</td>
                            <td>
                               {{ $group->ordering }}
                            </td>
                            <td>
                                <span class="badge">
                                    {{ $group->branchPlanGroups->count() }}
                                </span>
                            </td>
                            <td>
                                @if($group->companyGroups->count() > 0)
                                    <i class="fa fa-check"></i>
                                @else
                                    <i class="fa fa-minus"></i>
                                @endif
                            </td>
                            <td width="100">
                                <a href="{{ URL::to('plan/groups/show', array($group->id)) }}" class="btn btn-primary btn-xs">
                                    <i class="fa fa-search fa-fw"></i> podgląd
                                </a>
                            </td>
                            <td width="100">
                                @if(Auth::user()->can('programy#zarzadzaj'))
                                    <span target="{{ URL::to('plan/groups/delete', [$group->id]) }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-trash fa-fw"></i> usuń
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>



@stop
@section('headerJs')
    @parent
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
