@extends('layouts.main')


@section('header')

    {{$plan_group->name }}

    <div class="pull-right">
        <a href="{{ URL::to('plans/show', [$plan_group->plan_id]) }}" class="btn btn-default">
            <i class="fa fa-rotate-left fa-fw"></i>
            Powrót
        </a>
    </div>

@stop

@section('main')

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="panel panel-primary small">
                <div class="panel-heading">Dane grupy
                    @if(Auth::user()->can('programy#zarzadzaj'))
                        <a href="{{ URL::to('plan/groups/edit', array($plan_group->id)) }}" class="btn btn-warning btn-xs pull-right"><i class="fa fa-pencil fa-fw"></i> edytuj</a>
                    @endif
                </div>
                <table class="table">
                    <tr>
                        <td class="text-right"><label>Nazwa:</label></td>
                        <td>{{ $plan_group->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Kolejność:</label></td>
                        <td>{{ $plan_group->ordering }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Lista warunkowa:</label></td>
                        <td>{{ implode(',', $plan_group->companyGroups->lists('name')) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="panel panel-default small">
                <div class="panel-heading">
                    Dane programu
                </div>
                <table class="table">
                    <tr>
                        <td class="text-right"><label>Nazwa:</label></td>
                        <td>{{ $plan_group->plan->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Kod programu:</label></td>
                        <td>{{ $plan_group->plan->sales_program }}</td>
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
                    Lista serwisów

                    @if(Auth::user()->can('programy#zarzadzaj'))
                        <a href="{{ URL::to('plan/groups/add-branch', array($plan_group->id)) }}" class="btn btn-xs btn-primary pull-right">
                            <span class="glyphicon glyphicon-plus-sign"></span> Dodaj
                        </a>
                    @endif
                </div>
                <table class="table table-hover table-condensed" >
                    <thead>
                        <th></th>
                        <th>serwis</th>
                        <th>adres</th>
                        <th>nip</th>
                        <th>marki</th>
                        <th>sprzedał</th>
                        <th></th>
                        <th></th>
                    </thead>
                    @foreach($plan_group->branchPlanGroups as $k => $group)
                        @if($group->branchBrands->count() > 0)
                            @foreach($group->branchBrands as $i => $branchBrand)
                                <tr>
                                    @if($i == 0)
                                        <td rowspan="{{ count($group->branchBrands) }}" class="vertical-middle">
                                            {{ ++$k }}.
                                        </td>
                                        <td rowspan="{{ count($group->branchBrands) }}" class="vertical-middle">
                                            {{ $group->branch->short_name }}
                                        </td>
                                        <td rowspan="{{ count($group->branchBrands) }}" class="vertical-middle">
                                            {{ $group->branch->address }}
                                        </td>
                                        <td rowspan="{{ count($group->branchBrands) }}" class="vertical-middle">
                                            {{ $group->branch->nip }}
                                        </td>
                                    @endif

                                    <td>
                                        {{ $branchBrand->brand->name }}
                                    </td>
                                    <td>
                                        {{ $branchBrand->pivot->if_sold ? 'tak' : 'nie' }}
                                    </td>
                                    @if($i == 0)
                                        <td rowspan="{{ count($group->branchBrands) }}" class="vertical-middle">
                                            @if(Auth::user()->can('programy#zarzadzaj'))
                                                <a href="{{ url('plan/groups/edit-branch', [$group->id]) }}" class="btn btn-warning btn-xs">
                                                    <i class="fa fa-pencil fa-fw"></i>
                                                    zmień
                                                </a>
                                            @endif
                                        </td>
                                        <td rowspan="{{ count($group->branchBrands) }}" class="vertical-middle">
                                            @if(Auth::user()->can('programy#zarzadzaj'))
                                                <span class="btn btn-xs btn-danger modal-open" target="{{ url('plan/groups/delete-branch-plan-group', [$group->id]) }}" data-toggle="modal" data-target="#modal">
                                                    <i class="fa fa-trash fa-fw"></i>
                                                    usuń
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="vertical-middle">
                                    {{ ++$k }}.
                                </td>
                                <td class="vertical-middle">
                                    {{ $group->branch->short_name }}
                                </td>
                                <td>
                                    {{ $group->branch->address }}
                                </td>
                                <td>
                                    {{ $group->branch->nip }}
                                </td>
                                <td>
                                    ---
                                </td>
                                <td>
                                    ---
                                </td>
                                <td  class="vertical-middle">
                                    @if(Auth::user()->can('programy#zarzadzaj'))
                                        <a href="{{ url('plan/groups/edit-branch', [$group->id]) }}" class="btn btn-warning btn-xs">
                                            <i class="fa fa-pencil fa-fw"></i>
                                            zmień
                                        </a>
                                    @endif
                                </td>
                                <td  class="vertical-middle">
                                    @if(Auth::user()->can('programy#zarzadzaj'))
                                        <span class="btn btn-xs btn-danger modal-open" target="{{ url('plan/groups/delete-branch-plan-group', [$group->id]) }}" data-toggle="modal" data-target="#modal">
                                            <i class="fa fa-trash fa-fw"></i>
                                            usuń
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endif
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
