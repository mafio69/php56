@extends('injuries.letters.partials.index_template')

@section('page-title')
    Baza pism
@stop

@section('table-content')
    <table class="table table-hover  table-condensed" id="users-table">
        <thead>
            <Th style="width:30px;">lp.</th>
            <th>typ dokumentu</th>
            <th>nr dokumentu</th>
            <th>tytuł pisma</th>
            <th>nr szkody</th>
            <th>nr umowy</th>
            <th>nr rejestracyjny</th>
            <th>wprowadzający/przetwarzający</th>
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
                <td>{{ $letter->nr_document }}</td>
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
                <td>
                    <a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a>
                    @if($letter->file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
                        <span class="modal-open-lg marg-left btn btn-sm btn-info" target="{{ URL::to('injuries/dialog/preview-doc', array($letter->id, 'letter')) }}"  data-toggle="modal" data-target="#modal-lg">
                            <i class="fa fa-search pointer"></i>
                          </span>
                    @endif
                </td>
                <Td>
                    @if($letter->file && in_array( mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
                        <button target="{{ URL::route('routes.get', ['injuries', 'letters', 'edit', $letter->id])}}" class="btn btn-sm btn-primary modal-open-xl" data-toggle="modal" data-target="#modal-xl">
                            <i class="fa fa-pencil"></i> edytuj
                        </button>
                    @else
                        <button target="{{ URL::route('routes.get', ['injuries', 'letters', 'edit', $letter->id])}}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-pencil"></i> edytuj
                        </button>
                    @endif
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
@stop



