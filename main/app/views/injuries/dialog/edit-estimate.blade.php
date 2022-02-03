<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja kosztorysu</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="row">
        <div class="@if($estimate->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp'])) col-sm-6 @else col-sm-12 @endif">
            <form action="{{ URL::route('injuries-setEstimate', array($estimate->id)) }}" method="post"  id="dialog-form">
                {{Form::token()}}
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label >Kwota netto [zł]:</label>
                            {{ Form::text('net', money_format("%.2n",$estimate->net), array('class' => 'form-control currency_input number',  'placeholder' => 'kwota w zł')) }}
                        </div>
                    </div>
                   <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label >Kwota brutto [zł]:</label>
                            {{ Form::text('gross',money_format("%.2n",$estimate->gross), array('class' => 'form-control currency_input number',  'placeholder' => 'kwota w zł')) }}
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 marg-btm">
                        <div class="checkbox ">
                          <label>
                            <input type="checkbox" name="report" value="1"
                            @if($estimate->report == 1)
                              checked="checked"
                            @endif
                            >
                            Uwzględnij w raporcie
                          </label>
                        </div>
                      </div>
                    </div>
                </div>
            </form>
            @if($estimate->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)),['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..."  id="set"><i class="fa fa-floppy-o fa-fw"></i> Zapisz</button>
                </div>
            @endif
        </div>
        @if($estimate->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)),['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
            <div class="col-sm-6">
                @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)), ['jpeg', 'jpg', 'png' ,'gif',  'bmp']))
                    <img src="{{ url('injuries/preview-doc', [$estimate->injury_file->id]) }}" class="img-rounded" style="max-width: 100%;">
                @elseif(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)), ['tiff', 'tif']))
                    <div class="image-body" style="height: 80vh; overflow: auto;">

                    </div>
                @else
                    <iframe style="width:100%; border: none; height:50vw;" src="{{ url('injuries/preview-doc', [$estimate->injury_file->id]) }}"></iframe>
                @endif
            </div>
        @endif
    </div>
</div>
@if(!$estimate->injury_file || !in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)),['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..."  id="set">Zapisz</button>
</div>
@endif

<script type="text/javascript">
    var gross = {{Config::get('definition.estimate_gross')}}
    gross=(parseFloat(gross)/100)+1;
    $('[name="net"]').change(function(){
      $('[name="gross"]').val((parseFloat($('[name="net"]').val())*gross).toFixed(2));
    })
    $('[name="gross"]').change(function(){
      $('[name="net"]').val((parseFloat($('[name="gross"]').val())/gross).toFixed(2));
    })

</script>

@if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)), ['tiff', 'tif']))
    <script>
        $(function () {
            Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ url('injuries/preview-doc', [$estimate->injury_file->id]) }}');
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
