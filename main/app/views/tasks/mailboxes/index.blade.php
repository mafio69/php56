@extends('layouts.main')

@section('header')
    Skrzynki pocztowe
    <a class="btn btn-primary btn-xs pull-right" href="{{ URL::to('tasks/mailboxes/create') }}" >
        <i class="fa fa-plus fa-fw"></i>
        dodaj skrzynkę
    </a>
@stop

@section('main')

    <div class="row">
        <div class="panel panel-default col-sm-12 col-md-10">
            <table class="table table-hover  table-condensed">
                <thead>
                    <Th style="width:30px;">lp.</th>
                    <th></th>
                    <th>nazwa</th>
                    <th>serwer</th>
                    <th>login</th>
                    <th>źródło w systemie</th>
                    <th>domyślna grupa zadań</th>
                    <th></th>
                    <th></th>
                </thead>
                @foreach ($mailboxes as $k => $mailbox )
                    <tr class="vertical-middle @if(! $mailbox->is_valid ) danger @endif">
                        <td>{{ ++ $k }}.</td>
                        <td>
                            @if(! $mailbox->is_valid )
                                <span class="btn btn-xs btn-danger">
                                    <i class="fa fa-info-circle tips" data-placement="right"  title="błąd połączenia ze skrzynką"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $mailbox->name }}
                        </td>
                        <td>{{ $mailbox->server }}</td>
                        <td>{{ $mailbox->login }}</td>
                        <td>{{ $mailbox->taskSource->name }}</td>
                        <td>
                            @if($mailbox->taskGroup)
                                {{ $mailbox->taskGroup->name }}
                            @else
                                ---
                            @endif
                        <td>
                            <a class="btn btn-warning btn-xs" href="{{ URL::to('tasks/mailboxes/edit', [$mailbox->id]) }}" >
                                <i class="fa fa-fw fa-pencil"></i>
                                edytuj
                            </a>
                        </td>
                        <td>
                            <span class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('tasks/mailboxes/delete', [$mailbox->id]) }}" >
                                <i class="fa fa-trash-o fa-fw"></i>
                                usuń
                            </span>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop

