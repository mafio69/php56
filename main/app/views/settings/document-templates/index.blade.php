@extends('layouts.main')

@section('header')

    Przypisywanie szablonów dokumentów

@stop

@section('main')

    <div class="row">
        <div class="col-sm-6">
            <table class="table table-hover ">
                <thead>
                <th>lp.</th>
                <th>Właściciel</th>
                <th>NIP</th>
                <th>Szablon główny (lub umowa '/ROK')</th>
                <th>Szablon warunkowy</th>
                <th></th>
                </thead>
                @foreach ($owners as $k => $owner)
                    <tr>
                        <td>{{++$k}}.</td>
                        <Td>
                            {{$owner->name}}
                            @if($owner->old_name)
                                <br>
                                <span class="small">
                                    <i>
                                        {{ $owner->old_name }}
                                    </i>
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $owner->nip->first() ? $owner->nip->first()->value : '' }}
                        </td>
                        <td>
                            {{ $owner->documentTemplate ? $owner->documentTemplate->name : '---' }}
                        </td>
                        <td>
                            {{ $owner->conditionalDocumentTemplate ? $owner->conditionalDocumentTemplate->name : '---' }}
                        </td>
                        <td>
                            <span class="modal-open btn btn-xs btn-warning"
                                  target="{{ url('settings/document-templates/edit', [$owner->id]) }}"
                                  data-toggle="modal" data-target="#modal">
                                <i class="fa fa-pencil fa-fw"></i> zarządzaj
                            </span>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="col-sm-6">
            @foreach($templates as $template)
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $template->name }}</div>
                    <div class="panel-body">
                        <img src="{{ url('templates-src/templates/'.$template->slug.'_header.jpg') }}" class="img-responsive" >
                        <img src="{{ url('templates-src/templates/'.$template->slug.'_footer.jpg') }}" class="img-responsive" >
                    </div>
                </div>
            @endforeach
        </div>
    </div>



@stop
