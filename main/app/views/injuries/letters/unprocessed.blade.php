@extends('injuries.letters.partials.index_template')

@section('page-title')
    Baza pism
@stop

@section('table-content')
    <table class="table table-hover  table-condensed" id="users-table">
        <thead>
            <Th style="width:30px;">lp.</th>
            <th>data wprowadzenia</th>
            <th></th>
            <Th></Th>
            <Th></Th>
            <th></th>
        </thead>
        <?php $lp = (($letters->getCurrentPage()-1)*$letters->getPerPage()) + 1;?>
        @foreach ($letters as $letter)
            <tr class="odd gradeX vertical-middle">
                <td>{{$lp++}}.</td>
                <td>{{substr($letter->created_at, 0, -3)}}</td>
                <td>
                    <a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a>
                    @if($letter->file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)),['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
                        <span class="modal-open-lg marg-left btn btn-sm btn-info" target="{{ URL::to('injuries/dialog/preview-doc', array($letter->id, 'letter')) }}"  data-toggle="modal" data-target="#modal-lg">
                            <i class="fa fa-search pointer"></i>
                          </span>
                    @endif
                </td>
                <Td>
                    @if($letter->file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
                        <button target="{{ URL::route('routes.get', ['injuries', 'letters', 'assign', $letter->id])}}" class="btn btn-sm btn-primary modal-open-xl" data-toggle="modal" data-target="#modal-xl">
                            <i class="fa fa-cogs"></i> przetwórz
                        </button>
                    @else
                        <button target="{{ URL::route('routes.get', ['injuries', 'letters', 'assign', $letter->id])}}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal">
                            <i class="fa fa-cogs"></i> przetwórz
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



