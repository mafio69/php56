@extends('layouts.main')

@section('header')
    Lista wykluczonych spraw z raportu

    <a href="/dos/other/reports#completed_orders" class="btn btn-xs btn-default pull-right">
        <i class="fa fa-arrow-left fa-fw"></i>
        powrót
    </a>
@stop

@section('main')
    <table class="table table-hover  table-condensed">
        <thead>
            <Th style="width:30px;">lp.</th>
            <th>nr sprawy</th>
            <th>obiekt sprawy</th>
            <th>nr umowy</th>
            <th>kategoria</th>
            <Th>nr szkody</th>
            <th>status</th>
            <th ></th>
        </thead>
        <?php $lp = (($injuries->getCurrentPage()-1)*$injuries->getPerPage()) + 1; ?>
        @foreach ($injuries as $injury)
            <tr class="vertical-middle">
                <td>{{$lp++}}.</td>
                <td>
                    <a type="button" class="btn btn-link" target="_blank" href="{{URL::route('dos.other.injuries.info', array($injury->id))}}" >
                        {{$injury->case_nr}}
                    </a>
                </td>
                <td>
                    {{ $injury->object ? $injury->object->description : '' }}
                </td>
                <td><a type="button" class="btn btn-link" href="{{ URL::route('dos.other.injuries.info', array($injury->id)) }}" >{{$injury->object->nr_contract}}</a></td>
                <td>
                    {{ checkObjectIfNotNull($injury->object->assetType, 'name') }}
                </td>
                <td>
                    @if($injury->injury_nr != NULL && $injury->injury_nr != '')
                        {{$injury->injury_nr}}
                    @else
                        ---
                    @endif
                </td>
                <td>
                    <b>{{ $injury->status->name }}</b>
                </td>
                <td>
                    <form action="{{ URL::to('dos/other/reports/revert', [$injury->id]) }}" method="post">
                        {{ Form::token() }}
                        <button type="submit" class="btn btn-xs btn-warning">
                            <i class="fa fa-rotate-left"></i> przywróć
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $injuries->links() }}</div>
@stop



