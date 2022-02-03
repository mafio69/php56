@extends('layouts.main')


@section('header')

    Baza nabywców

    <div class="pull-right"><a href="{{ URL::to('injuries/buyers/create') }}" class="btn btn-small btn-primary">
        <i class="fa fa-plus fa-fw"></i> Dodaj nabywcę</a>
    </div>
@stop

@section('main')
    <div class="marg-btm">
        <div class="pull-right" style="width: 350px;">
            {{ Form::open(array('url' => 'injuries/buyers', 'method' => 'get', 'id' => 'search-form', 'class' => 'allow-confirm')) }}
            <div class="input-group">
                <input type="text" name="term" value="{{ Input::get('term') }}" class="form-control" placeholder="nazwa/nip nabywcy...">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit" ><i class="fa fa-search"></i></button>
                </span>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-condensed">
            <thead>
            <th>lp.</th>
            <th>nazwa</th>
            <th>nip</th>
            <th>regon</th>
            <th>adres</th>
            <Th>telefon</Th>
            <th>email</th>
            <th>osoba kontaktowa</th>
            <th ></th>
            <th></th>
            <th></th>
            </thead>
            <?php $lp = (($buyers->getCurrentPage()-1)*$buyers->getPerPage()) + 1;?>
            @foreach ($buyers as $buyer)
            <tr class="vertical-middle">
                <td width="20px">{{$lp++}}.</td>
                <Td>{{ $buyer->name}}</td>
                <td>{{ $buyer->nip }}</td>
                <td>{{ valueIfNotNull($buyer->regon) }}</td>
                <td>{{ $buyer->address_code }} {{ $buyer->address_city }}, {{ $buyer->address_street }}</td>
                <td>{{$buyer->phone}}</td>
                <td>{{$buyer->email}}</td>
                <td>{{$buyer->contact_person}}</td>
                <td>
                    <a href="{{ URL::to('injuries/buyers/edit', [$buyer->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> edytuj</a>
                </td>
                <td>
                    @if($buyer->active == 0)
                        <span target="{{ URL::to('injuries/buyers/disable', [$buyer->id]) }}"class="btn btn-warning btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-power-off"></i> dezaktywuj</span>
                    @else
                        <span target="{{ URL::to('injuries/buyers/activate', [$buyer->id]) }}"class="btn btn-info btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-power-off"></i> aktywuj</span>
                    @endif
                </td>
                <td>
                    <button target="{{ URL::to('injuries/buyers/delete', [$buyer->id]) }}" class="btn btn-sm btn-danger modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-trash-o"></i> usuń</button>
                </td>
            </tr>
            @endforeach


        </table>
        <div class="pull-right" style="clear:both;">{{ $buyers->links() }}</div>
    </div>



@stop


