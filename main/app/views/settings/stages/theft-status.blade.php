@extends('layouts.main')

@section('header')

    Zarządzanie etapami kradieży

@stop

@section('main')
    @include('settings.stages.theft-nav')
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-auto table-bordered table-hover">
                <thead>
                <th>lp.</th>
                <Th>Status</Th>
                <Th>Warunek</Th>
                <th></th>
                <th></th>
                </thead>

                @foreach ($stages as $k => $stage)
                    @if($stage->step)
                    <tr>
                        <td>{{++$k}}.</td>
                        <td>
                            {{ $stage->step->name }}
                        </td>
                        <td>
                            {{ $stage->condition }}
                        </td>
                        <td>
                            <button class="btn btn-xs btn-primary modal-open-lg"  target="{{ URL::route('routes.get', array('settings', 'stages', 'uploaded-document-types', $stage->id, 3)) }}" data-toggle="modal" data-target="#modal-lg">
                                Dokumenty wgrywane <span class="badge">{{ $stage->uploadedDocumentTypes->count() }}</span>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-xs btn-info modal-open-lg" target="{{ URL::route('routes.get', array('settings', 'stages', 'document-types', $stage->id, 3)) }}" data-toggle="modal" data-target="#modal-lg">
                                Dokumenty generowane <span class="badge">{{ $stage->documentTypes->count() }}</span>
                            </button>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>





@stop


@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {

        });
    </script>
@stop