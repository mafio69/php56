<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>

    <h4 class="modal-title" id="myModalLabel">
        Podgląd dokumentu
        @if(
            in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$file->filename)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp'])
            ||
            $file->mime == 'application/pdf'
        )
            <span class="btn btn-xs btn-primary marg-right task-toggle-file-preview" data-file="{{ $file->id }}">
                <i class="fa fa-toggle-left fa-fw"></i>
                przełącz widok
            </span>
        @endif
    </h4>

</div>
<div class="modal-body" style="overflow:hidden;">
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
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
</div>
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