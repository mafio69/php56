@extends('gap.manage.index_template')

@section('page-title')
    GAP umowy - {{$status->description}}
@stop

@section('table-content')
        <table class="table table-hover  table-condensed" id="users-table">
            <thead>
                <Th style="width:30px;">lp.</th>
                <th>Numer umowy</th>
                <th>GAP</th>
                <th>Typ GAP</th>
                <Th>Nazwa przedmiotu leasingu</Th>
                <Th>Rodzaj mienia</Th>
                <Th>Klasyfikacja</th>
            </thead>
            <?php $lp = (($agreements->getCurrentPage()-1)*$agreements->getPerPage()) + 1;?>
            @foreach ($agreements as $agreement)
                <tr class="odd gradeX vertical-middle"
                    @if(Session::has('last_agreement') && $agreement->id == Session::get('last_agreement'))
                        style="background-color: honeydew;"
                        <?php Session::forget('last_agreement');?>
                    @endif
                >
                    <td>{{$lp++}}.</td>
                    <td>
                        <a type="button" class="btn btn-link"  href="">
                            {{ $agreement->agreement_number }}
                        </a>
                    </td>

                    <td>{{ ($agreement->group) ? $agreement->group->name : ''}}</td>
                    <td>{{ ($agreement->type) ? $agreement->type->name : ''}}</td>
                    <td>{{ ($agreement->object) ? $agreement->object->name : ''}}</td>
                    <td>{{ ($agreement->object&&$agreement->object->type) ? $agreement->object->type->code : '' }}</td>
                    <td>{{ ($agreement->object&&$agreement->object->group) ? $agreement->object->group->name : ''}}</td>
                </tr>
            @endforeach
        </table>
        <div class="pull-right" style="clear:both;">{{ $agreements->appends(Input::query())->links() }}</div>
@stop
