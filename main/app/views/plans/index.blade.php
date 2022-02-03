@extends('layouts.main')


@section('header')
    Programy
    @if(Auth::user()->can('programy#zarzadzaj'))
    <div class="pull-right">
        <a href="{{ URL::to('plans/create') }}" class="btn btn-small btn-primary iframe"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj program</a>
    </div>
    @endif

@stop

@section('main')
    <div class="table-responsive">
        <table class="table  table-hover  table-condensed">
            <thead>
            <th>#</th>
            <th>nazwa</th>
            <th>kod programu sprzedażowego</th>
            <th>liczba grup</th>
            <th>liczba serwisów</th>
            <th></th>
            <th></th>
            </thead>
            <?php $lp = (($plans->getCurrentPage()-1)*$plans->getPerPage()) + 1; ?>
            @foreach ($plans as $plan)
                <tr class="vertical-middle">
                    <td>{{ $lp++ }}.</td>
                    <td>
                        {{ $plan->name  }}
                    </td>
                    <td>{{$plan->sales_program}}</td>
                    <td>{{count($plan->groups)}}</td>
                    <td>
                        <?php $ct = 0;?>
                        @foreach($plan->groups as $group)
                            <?php $ct += count($group->branchPlanGroups);?>
                        @endforeach
                        {{ $ct }}
                    </td>
                    <td>
                        <a href="{{ URL::to('plans/show', [$plan->id]) }}" class="btn btn-primary btn-xs">
                            <i class="fa fa-search fa-fw"></i> podgląd
                        </a>
                    </td>
                    <td>
                        @if(Auth::user()->can('programy#zarzadzaj'))
                            <span target="{{ URL::to('plans/delete',[$plan->id]) }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-trash fa-fw"></i> usuń
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="pull-right" style="clear:both;">{{ $plans->appends(Input::all())->links() }}</div>
    </div>


@stop

@section('headerJs')
    @parent
    <script type="text/javascript">

    </script>
@stop
