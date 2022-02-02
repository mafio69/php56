<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja odszkodowania</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="row">
        <div class="@if($compensation->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp'])) col-sm-6 @else col-sm-12 @endif">
            <form action="{{ URL::route('injuries-setCompensation', array($compensation->id)) }}" method="post"  id="dialog-form">
                {{Form::token()}}
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label>Data decyzji:</label>
                            {{ Form::text('date_decision', $compensation->date_decision, ['class' => 'form-control date', 'placeholder' => 'data decyzji']) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label>Rodzaj decyzji:</label>
                            {{ Form::select('injury_compensation_decision_type_id', $decisionTypes, $compensation->injury_compensation_decision_type_id, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label>Odbiorca odszkodowania:</label>
                            {{ Form::select('receive_id', $receives, $compensation->receive_id, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label >Kwota [zł]:</label>
                            {{ Form::text('compensation', money_format("%.2n",$compensation->compensation), array('class' => 'form-control currency_input number',  'placeholder' => 'kwota w zł')) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <select class="form-control required error" required="required" name="net_gross"
                                    aria-required="true">
                                @foreach(Config::get('definition.compensationsNetGross') as $key => $value)
                                <option value="{{ $key == 0 ? '' : $key }}" @if($key == $compensation->net_gross) selected="selected" @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label >Uwagi:</label>
                            {{ Form::textarea('remarks', $compensation->remarks, array('class' => 'form-control',  'placeholder' => 'uwagi')) }}
                        </div>
                    </div>
                </div>
            </form>
            @if($compensation->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..."  id="set"><i class="fa fa-floppy-o fa-fw"></i> Zapisz</button>
                </div>
            @endif
        </div>
        @if($compensation->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']) )
            <div class="col-sm-6">
                @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['jpeg', 'jpg', 'png' ,'gif', 'bmp']))
                    <img src="{{ url('injuries/preview-doc', [$compensation->injury_file->id]) }}" class="img-rounded" style="max-width: 100%;">
                @elseif(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['tiff', 'tif']))
                    <div class="image-body" style="height: 80vh; overflow: auto;">

                    </div>
                @else
                    <iframe style="width:100%; border: none; height:50vw;" src="{{ url('injuries/preview-doc', [$compensation->injury_file->id]) }}"></iframe>
                @endif
            </div>
        @endif
    </div>
</div>
@if(!$compensation->injury_file || !in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..."  id="set">Zapisz</button>
</div>
@endif

<script type="text/javascript">
    $(document).ready(function(){
        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            'changeMonth':true,
            'changeYear':true,
            'maxDate': '0'
        });
    });

</script>

@if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['tiff', 'tif']))
    <script>
        $(function () {
            Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ url('injuries/preview-doc', [$compensation->injury_file->id]) }}');
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
