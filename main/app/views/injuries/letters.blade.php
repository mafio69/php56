@extends('layouts.main')

@section('header')

    <span class="pull-left">
    Pisma nieprzypisane
    </span>
@include('injuries.menu-top')

@stop

@include('injuries.nav')

@section('main')

    @include('injuries.menu')
    <div class="pull-right marg-right">
        <form method="post" class="form-inline" id="document-type-filter-form" action="{{ URL::route('injuries.getSearch') }}" >
            {{ Form::token() }}
            <div class="form-group" >
                <label for="document_type_id">Typ dokumentu: </label>
                <select class="form-control input-sm" name="document_type_id">
                    <option @if(! Input::has('document_type_id') ) selected @endif value="0">--- wybierz ---</option>
                    @foreach($uploadedDocumentTypes as $uploadedDocumentType)
                        @if($uploadedDocumentType->subtypes->count() == 0)
                            <option @if(Input::has('document_type_id') && Input::get('document_type_id') == $uploadedDocumentType->id) selected @endif value="{{ $uploadedDocumentType->id }}">{{ $uploadedDocumentType->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    <div id="injuries-container">
        <table class="table table-hover  table-condensed" id="users-table">
            <thead>
                <Th style="width:30px;">lp.</th>
                <th>typ dokumentu</th>
                <th>tytuł pisma</th>
                <th>nr szkody</th>
                <th>nr umowy</th>
                <th>nr rejestracyjny</th>
                <th>wprowadzający</th>
                <th>data wprowadzenia</th>
                <th></Th>
                <th></th>
                <Th></Th>
                <Th></Th>
                <th></th>
            </thead>
            <?php $lp = (($letters->getCurrentPage()-1)*$letters->getPerPage()) + 1;?>
            @foreach ($letters as $letter)
                <tr class="odd gradeX vertical-middle">
                    <td>{{$lp++}}.</td>
                    <td>{{ $letter->uploadedDocumentType->name }}</td>
                    <td>{{ checkIfEmpty($letter->name) }}</td>
                    <td>{{ checkIfEmpty($letter->injury_nr) }}</td>
                    <td>{{ checkIfEmpty($letter->nr_contract) }}</td>
                    <td>{{ checkIfEmpty($letter->registration) }}</td>
                    <td>{{ $letter->user->name }}</td>
                    <td>{{substr($letter->created_at, 0, -3)}}</td>
                    <td>
                        @if( trim($letter->description) != '')
                            <a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="Opis pisma" data-content="{{ $letter->description }}"><i class="fa fa-info-circle"></i> opis</a>
                        @endif
                    </td>
                    <td><a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a> </td>
                    <Td>
                        <button target="{{ URL::route('routes.get', ['injuries', 'letters', 'edit', $letter->id])}}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-pencil"></i> edytuj
                        </button>
                    </Td>
                    <Td>
                        <button target="{{ URL::route('routes.get', ['injuries', 'letters', 'delete', $letter->id])}}" class="btn btn-sm btn-danger modal-open" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-trash-o"></i> usuń
                        </button>
                    </Td>
                </tr>
            @endforeach
        </table>
        <div class="pull-right" style="clear:both;">{{ $letters->appends(Input::query())->links() }}</div>
    </div>


@stop

@section('headerJs')
    @parent
    <script>
        $(document).ready(function() {

            $('#document-type-filter-form').on('change', 'select[name="document_type_id"]', function(){
                $.ajax({
                    type: "POST",
                    url: $('#document-type-filter-form').prop( 'action' ),
                    data: $('#document-type-filter-form').serialize(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        self.location = data;
                    }
                });

                return false;
            });
        });
    </script>
@stop
