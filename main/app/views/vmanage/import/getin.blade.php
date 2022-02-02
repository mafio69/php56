@extends('layouts.main')


@section('header')

    Import pojazdów GetIn powyżej 3.5t
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="progress progress-striped active margin-bottom margin-top-md" id="uploading-progressbar" style="display: none;">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="alert alert-notification alert-info alert-important marg-top-min marg-btm" >
                Wszystkie pojazdy firm Idea Getin Leasing S.A. i Idea Getin Leasing Spółka Akcyjna Automotive S.K.A. nie znajdujące się w importowanym pliku zostaną usuniąte.
            </div>

            <div class="uploader btn btn-block btn-primary btn-sm" >
                <i class="fa fa-upload" aria-hidden="true"></i>
                wgraj zestawienie w formacie .tsv
                <input class="file" name="file" type="file" style="width: 90%;opacity: 0;position: absolute;top: 0;height: 100%;">
            </div>
        </div>
        <div class="col-sm-12">
            <h4>Historia importów</h4>
            <div class="table-responsive">
                <table class="table table-hover table-condensed">
                    <thead>
                    <th>lp.</th>
                    <th></th>
                    <th>status</th>
                    <th>data wywołania importu</th>
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
            $('.file').change(upload);
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
                url: '{{ URL::action('VmanageImportController@postUploadGetin') }}',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function() {
                    location.reload();
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
                data: { '_token' : $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '{{ URL::action('VmanageImportController@postCheckParseStatus') }}',
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $.each( data, function( import_id, parse_date ) {
                        console.log(import_id);
                        $('span.label[data-import="'+import_id+'"]').removeClass('label-warning').addClass('label-success').html('Zaimportowane');
                    });
                }
            });
            return;
        }

        setInterval(checkParseStatus, 30000);

    </script>

@stop
