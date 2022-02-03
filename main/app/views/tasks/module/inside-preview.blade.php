<div class="panel panel-default">
    <div class="panel-heading text-center" style="padding: 10px 15px 20px 15px;">
        <small class="text-left">
            Podgląd dokumentu
        </small>
        <span class="pull-right small btn btn-xs btn-default task-show-details" data-task="{{ $file->task->current_task_instance_id }}" >
           <i class="fa fa-arrow-left fa-fw"></i> powrót
        </span>
    </div>
    <div class="panel-body">
        <div class="row">
            @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                <div class="col-sm-12">
                    @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename)), ['jpeg', 'jpg', 'png' ,'gif',  'bmp']))
                        <img src="{{ url('tasks/preview-doc', [$file->id]) }}" class="img-rounded"
                             style="max-width: 100%;">
                    @elseif(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename)), ['tiff', 'tif']))
                        <div class="image-body" style="height: 80vh; overflow: auto;">

                        </div>
                    @else
                        <iframe style="width:100%; border: none; height:50vw;"
                                src="{{ url('tasks/preview-doc', [$file->id]) }}"></iframe>
                    @endif
                </div>
            @else
                @if($file->mime == 'application/pdf')
                    <div class="col-sm-12">
                        <iframe style="width:100%; border: none; height:50vw;"
                                src="{{ url('tasks/preview-doc', [$file->id]) }}"></iframe>
                    </div>
                @else
                    <h4 class="text-center">nieobsługiwany format pliku</h4>
                @endif
            @endif
        </div>
    </div>
</div>

@if(!isset($loadInSection) || $loadInSection)
    @section('headerJs')
        @parent
        @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename)), ['tiff', 'tif']))
            <script>
                $(function () {
                    Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', '{{ url('tasks/preview-doc', [$file->id]) }}');
                    xhr.responseType = 'arraybuffer';
                    xhr.onload = function (e) {
                        var buffer = xhr.response;
                        var tiff = new Tiff({buffer: buffer});
                        for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                            tiff.setDirectory(i);
                            var canvas = tiff.toCanvas();

                            $('.image-body').append(canvas);
                        }

                        $('.image-body canvas').each(function () {
                            $(this).css('width', '100%');
                        });
                    };
                    xhr.send();
                });
            </script>
        @endif
    @endsection
@else
    @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename)), ['tiff', 'tif']))
        <script>
            $(function () {
                Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '{{ url('tasks/preview-doc', [$file->id]) }}');
                xhr.responseType = 'arraybuffer';
                xhr.onload = function (e) {
                    var buffer = xhr.response;
                    var tiff = new Tiff({buffer: buffer});
                    for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                        tiff.setDirectory(i);
                        var canvas = tiff.toCanvas();

                        $('.image-body').append(canvas);
                    }

                    $('.image-body canvas').each(function () {
                        $(this).css('width', '100%');
                    });
                };
                xhr.send();
            });
        </script>
    @endif
@endif