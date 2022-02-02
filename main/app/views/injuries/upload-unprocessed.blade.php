@extends('layouts.main')


@section('header')

    Import szkód nieprzetworzonych
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="progress progress-striped active margin-bottom margin-top-md" id="uploading-progressbar" style="display: none;">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="uploader btn btn-block btn-primary btn-sm" >
                <i class="fa fa-upload" aria-hidden="true"></i>
                wgraj zestawienie w formacie .xlsx
                <input class="file" name="file" type="file" style="width: 90%;opacity: 0;position: absolute;top: 0;height: 100%;">
            </div>
            <div class="loader" style="display: none;">
                <h1 class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                </h1>
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
            $('.uploader').hide();
            $('.loader').show();
            $.ajax({
                data: data,
                type: 'POST',
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) myXhr.upload.addEventListener('progress',progressHandlingFunction, false);
                    return myXhr;
                },
                url: '{{ URL::action('InjuriesController@proceedUnprocessed') }}',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function() {
                    self.location = '{{ url('injuries/unprocessed') }}'
                }
            });
        }

        // update progress bar
        function progressHandlingFunction(e){
            if(e.lengthComputable){
                $('#uploading-progressbar .progress-bar').css('width', (e.loaded*100)/e.total+'%');
            }
        }

    </script>
@stop
