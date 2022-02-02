@extends('injuries.letters.partials.index_template')

@section('page-title')
    Baza pism
@stop

@section('table-content')
    <table class="table table-hover  table-condensed" id="users-table">
        <thead>
            <Th style="width:30px;">lp.</th>
            <th>typ dokumentu:</th>
            <th>nr dokumentu</th>
            <Th>nr sprawy</Th>
            <th>tytuł pisma</th>
            <th>nr szkody</th>
            <th>nr umowy</th>
            <th>nr rejestracyjny</th>
            <th>wprowadzający/przetwarzający</th>
            <th>data wprowadzenia</th>
            <th></Th>
            <th></th>
            <Th></Th>
        </thead>
        <?php $lp = (($letters->getCurrentPage()-1)*$letters->getPerPage()) + 1;?>
        @foreach ($letters as $letter)
            <tr class="odd gradeX vertical-middle">
                <td>{{$lp++}}.</td>
                <td>{{ $letter->uploadedDocumentType->name }}</td>
                <td>{{ $letter->nr_document }}</td>
                <td>
                    @if(Auth::user()->can('kartoteka_szkody#wejscie'))
                        <a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($letter->injury_file->injury->id))}}" >
                            {{$letter->injury_file->injury->case_nr}}
                        </a>
                    @else
                        {{$letter->injury_file->injury->case_nr}}
                    @endif
                </td>
                <td>{{ $letter->name }}</td>
                <td>{{ $letter->injury_nr }}</td>
                <td>{{ $letter->nr_contract }}</td>
                <td>{{ $letter->registration }}</td>
                <td>{{ $letter->user->name }}</td>
                <td>{{substr($letter->created_at, 0, -3)}}</td>
                <td>
                    @if(trim($letter->description) != '')
                        <a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="Opis pisma" data-content="{{ $letter->description }}"><i class="fa fa-info-circle"></i> opis</a>
                    @endif
                </td>
                <td>
                    <a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a>
                    @if($letter->file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                        <span class="modal-open-lg marg-left btn btn-sm btn-info" target="{{ URL::to('injuries/dialog/preview-doc', array($letter->id, 'letter')) }}"  data-toggle="modal" data-target="#modal-lg">
                            <i class="fa fa-search pointer"></i>
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <div class="pull-right" style="clear:both;">{{ $letters->appends(Input::query())->links() }}</div>
@stop



