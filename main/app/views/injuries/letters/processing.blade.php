@extends('layouts.main')

@section('header')

    Dodawanie nowego pisma

    <div class="pull-right">
        <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
        <div class="row">
            @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$filename)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                <div class="col-sm-12 col-md-6">
            @else
                <div class="col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2">
            @endif
            {{ Form::open(array('url' => URL::route('routes.post', ['injuries', 'letters', 'store'] ), 'id' => 'page-form', 'class' => 'form-horizontal' )) }}
                <input type="hidden" name="file" value="{{ $filename }}"/>
                <div class="page-header">
                    <h3 class="text-center">Dane pisma:</h3>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3 control-label">Typ dokumentu:</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        {{ Form::select('category', $uploadedDocumentTypes, null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3 control-label">Nr dokumentu</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        <input type="text" class="form-control" placeholder="nr dokumentu" name="nr_document" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3  control-label">Tytuł pisma:</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        <input type="text" class="form-control" placeholder="tytuł pisma" name="name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3  control-label">Nr szkody:</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        <input type="text" class="form-control upper" placeholder="nr szkody" name="injury_nr">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3  control-label">Nr umowy:</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        <input type="text" class="form-control" placeholder="nr umowy" name="nr_contract">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3  control-label">Nr rejestracyjny:</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        <input type="text" class="form-control uppercase" placeholder="nr rejestracyjny" name="registration">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-md-3  control-label">Opis pisma:</label>
                    <div class="col-sm-10 col-md-9 col-lg-6">
                        <textarea class="form-control" placeholder="opis zawartości pisma" name="description" style="min-height: 200px;"></textarea>
                    </div>
                </div>

                <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
                    {{ Form::submit('Wprowadź pismo',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;', 'data-loading-text' => 'Trwa wprowadzanie pisma...'))  }}
                </div>
            {{ Form::close() }}
            </div>
            @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$filename)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'bmp']))
                <div class="col-sm-12 col-md-6">
                    @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$filename)), ['jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                        <img src="{{ url('injuries/preview-doc', [$filename, 'filename']) }}" class="img-rounded" style="max-width: 100%;">
                    @elseif(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$filename)), [ 'tiff', 'tif']))
                        <div class="image-body" style="height: 80vh; overflow: auto;">

                        </div>
                    @else
                        <iframe style="width:100%; border: none; height:50vw;" src="{{ url('injuries/preview-doc', [$filename, 'filename']) }}"></iframe>
                    @endif
                </div>
            @endif
        </div>
@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
        });
    </script>
    @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$filename)), [ 'tiff', 'tif']))
        <script>
            $(function () {
                Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '{{ url('injuries/preview-doc', [$filename, 'filename']) }}');
                xhr.responseType = 'arraybuffer';
                xhr.onload = function (e) {
                    var buffer = xhr.response;
                    var tiff = new Tiff({buffer: buffer});
                    for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                        tiff.setDirectory(i);
                        var canvas = tiff.toCanvas();

                        $('.image-body').append(canvas);
                    }

                    $('.image-body canvas').each(function(){
                        $(this).css('width', '100%');
                    });
                };
                xhr.send();
            });
        </script>
    @endif
@stop


