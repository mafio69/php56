<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja danych pisma</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="row">
        <div class="@if( in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp'])) col-sm-6 @else col-sm-12 @endif">
            <form action="{{ URL::route('routes.post', ['injuries', 'letters', 'update', $letter->id]) }}" method="post"  id="dialog-form" class="form-horizontal">
                {{Form::token()}}
                <div class="form-group">
                    <label class="col-sm-3 control-label">Typ dokumentu:</label>
                    <div class="col-sm-9">
                        {{ Form::select('category', $uploadedDocumentTypes, $letter->category, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Nr dokumentu</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="nr dokumentu" name="nr_document" value="{{ $letter->nr_document }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3  control-label">Tytuł pisma:</label>
                    <div class="col-sm-9 ">
                        <input type="text" class="form-control" placeholder="tytuł pisma" name="name" value="{{ $letter->name }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3  control-label">Nr szkody:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control upper" placeholder="nr szkody" name="injury_nr" value="{{ $letter->injury_nr }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3  control-label">Nr umowy:</label>
                    <div class="col-sm-9 ">
                        <input type="text" class="form-control" placeholder="nr umowy" name="nr_contract" value="{{ $letter->nr_contract }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3  control-label">Nr rejestracyjny:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control uppercase" placeholder="nr rejestracyjny" name="registration" value="{{ $letter->registration }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3  control-label">Opis pisma:</label>
                    <div class="col-sm-9 ">
                        <textarea class="form-control" placeholder="opis zawartości pisma" name="description" style="min-height: 200px;">{{ str_replace("<br />", "", $letter->description) }}</textarea>
                    </div>
                </div>
            </form>
            @if( in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..."  id="set"><i class="fa fa-floppy-o fa-fw"></i> Zapisz</button>
                </div>
            @endif
        </div>
        @if( in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
            <div class="col-sm-6">
                @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['jpeg', 'jpg', 'png' ,'gif',  'bmp']))
                    <img src="{{ url('injuries/preview-doc', [$letter->id, 'letter']) }}" class="img-rounded" style="max-width: 100%;">
                @elseif(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['tiff', 'tif']))
                    <div class="image-body" style="height: 80vh; overflow: auto;">

                    </div>
                @else
                    <iframe style="width:100%; border: none; height:50vw;" src="{{ url('injuries/preview-doc', [$letter->id, 'letter']) }}"></iframe>
                @endif
            </div>
        @endif
    </div>
</div>
@if( !in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa aktualizowanie..." id="set">Zapisz zmiany</button>
</div>
@endif


@if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$letter->file)), ['tiff', 'tif']))
    <script>
        $(function () {
            Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ url('injuries/preview-doc', [$letter->id, 'letter']) }} ');
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
