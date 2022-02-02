@extends('layouts.main')


@section('header')
    Import pojazdów poniżej 3.5t
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="progress progress-striped active margin-bottom margin-top-md" id="uploading-progressbar" style="display: none;">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
        </div>
        @if(!$filename)
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <form action="{{ URL::action('VmanageImportController@postUploadTruckFile') }}"  method="post" id="fileForm" enctype="multipart/form-data" >
                    {{ Form::token() }}
                    <div class="uploader btn btn-block btn-primary btn-sm" >
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        wgraj zestawienie
                        <input class="file" name="file" type="file" style="width: 90%;opacity: 0;position: absolute;top: 0;height: 100%;">
                    </div>
                </form>
                <p class="text-center loader" style="display: none">
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                    <br>
                    <strong>
                    trwa przesyłanie pliku
                    </strong>
                </p>
            </div>
        @endif
        @if($filename)
        <div class="col-sm-12 col-md-8 col-md-offset-2" id="uploading-form" >
            <div class="panel panel-primary marg-top">
                <div class="panel-body">
                    <form action="{{ URL::action('VmanageImportController@postProceedFile') }}" method="post">
                        {{ Form::token() }}
                        {{ Form::hidden('filename', $filename) }}
                        {{ Form::hidden('original_filename', $original_filename) }}
                        <div class="form-group marg-top">
                            <label>Rodzaj pliku</label>
                            <select name="file_type" class="form-control" required>
                                <option value="1" selected>TSV/CSV (Idea Getin Leasing SA)</option>
                                <option value="2">formatka GetinCFM</option>
                                <option value="3">formatka GetinMobilny</option>
                                <option value="4">formatka OpelLeasingMobilny</option>
                            </select>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                Rozpocznij przetwarzanie pliku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        <div class="col-sm-12">
            <h4>Historia importów</h4>
            <div class="table-responsive">
                <table class="table table-hover table-condensed">
                    <thead>
                    <th>lp.</th>
                    <th></th>
                    <th>status</th>
                    <th>data wywołania importu</th>
                    <th>rodzaj pliku</th>
                    <th>osoba wgrywająca</th>
                    </thead>
                    @foreach ($imports as $lp => $import)
                        <tr class="vertical-middle">
                            <td width="20px">{{++$lp}}.</td>
                            <td>
                                <a href="{{ URL::action('VmanageImportController@getDownload', [$import->id]) }}" target="_blank" class="btn btn-primary btn-xs">
                                    <i class="fa fa-floppy-o"></i>
                                </a>
                            </td>
                            <Td>
                                @if($import->parsed)
                                    <span class="label label-success" data-import="{{ $import->id }}">Zaimportowane</span>
                                @else
                                    <span class="label label-warning" data-import="{{ $import->id }}">W trakcie importu...</span>
                                @endif
                            </td>
                            <td>{{ $import->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($import->file_type == 1)
                                    TSV/CSV (Idea Getin Leasing SA)
                                @elseif($import->file_type == 2)
                                    formatka GetinCFM
                                @elseif($import->file_type == 3)
                                    formatka GetinMobilny
                                @elseif($import->file_type == 4)
                                    formatka OpelLeasingMobilny
                                @endif
                            </td>
                            <td>{{ $import->user->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop

@section('headerJs')
    @parent
    <script>
        $(document).ready(function() {
            $('.file').change(function(){
                $('#fileForm').hide();
                $('.loader').show();
                $('#fileForm').submit();
            });
        });

        function upload(){
            var data = new FormData();
            data.append("_token", $('meta[name="csrf-token"]').attr('content'));

            $.each( $(this)[0].files , function (idx, file) {
                data.append("file", file);
            });

            $('#uploading-progressbar').show();
            $.ajax({
                data: data,
                type: 'POST',
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) myXhr.upload.addEventListener('progress',progressHandlingFunction, false);
                    return myXhr;
                },
                url: '{{ URL::action('VmanageImportController@postUploadFile') }}',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                    $('input[name="filename"]').val(data.filename);
                    $('#uploading-progressbar').hide();
                    $('.uploader').hide();
                    $('#uploading-form').show();
                }
            });
        }

        // update progress bar
        function progressHandlingFunction(e){
            if(e.lengthComputable){
                $('#uploading-progressbar .progress-bar').css('width', (e.loaded*100)/e.total+'%');
            }
        }

        function checkParseStatus() {
            $.ajax({
                data: { '_token' : $('meta[name="csrf-token"]').attr('content'), 'if_truck' : 1 },
                type: 'POST',
                url: '{{ URL::action('VmanageImportController@postCheckParseStatus') }}',
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $.each( data, function( import_id, parse_date ) {
                        $('span.label[data-import="'+import_id+'"]').removeClass('label-warning').addClass('label-success').html('Zaimportowane');
                    });
                }
            });
            return;
        }

        setInterval(checkParseStatus, 30000);

    </script>

@stop
