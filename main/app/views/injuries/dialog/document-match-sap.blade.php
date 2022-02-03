<div id="match-sap-container">
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Parowanie z SAP</h4>
    </div>
    <div class="modal-body" style="overflow:hidden;">
        <div class="row">
            <div class="@if($compensation->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp'])) col-sm-6 @else col-sm-12 @endif">
                <h3 class="text-danger text-center">UWAGA! W SAP istnieje wprowadzona kwota odszkodowania</h3>
                <h5 class="text-center">Wskaż odpowiednią kwotę do sparowania z DECYZJĄ:</h5>
                <hr>
                @if( $compensation->mode == 1)
                    <form action="{{ URL::to('injuries/document/save-match-sap', [$compensation->id]) }}" method="post" class="match-form">
                        {{Form::token()}}
                        <input type="hidden" name="mode" value="1">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <p class="bg-info text-center"><strong>Wypłata bez dopłat z SAP</strong></p>
                                <br>
                                <button type="submit" class="btn btn-primary">SPARUJ {{ $compensation->injury->sap->kwotaOdsz }} PLN</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                @endif

                @if(count($premiums) > 0)
                    <form action="{{ URL::to('injuries/document/save-match-sap', [$compensation->id]) }}" method="post" class="match-form">
                        {{Form::token()}}
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="bg-info text-center"><strong>Dopłata w SAP</strong></p>
                                </div>
                                @foreach($premiums as $premium)
                                    <div class="form-group col-sm-12">
                                        <label class="radio-inline">
                                            <input type="radio" name="injury_sap_premium_id" value="{{ $premium->id }}" required>
                                            <strong>
                                                nr raty:
                                                {{ $premium->nrRaty }}
                                            </strong>

                                            <em>
                                                <label>data dopłaty:</label> {{ $premium->dataDpl }}
                                            </em>

                                            <em>
                                                <label>kwota dopłaty:</label> {{ $premium->kwDpl }}
                                            </em>

                                            <em>
                                                <label>rejestrujący:</label> {{ $premium->unameRej }}
                                            </em>

                                            <em>
                                                <label>data wpisu:</label> {{ $premium->dataRej }}
                                            </em>
                                        </label>
                                    </div>
                                @endforeach
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary" >SPARUJ</button>
                                </div>
                            </div>
                    </form>
                    <hr>
                @endif

                <div class="row">
                    <div class="col-sm-6 text-center">
                        <form action="{{ URL::to('injuries/document/save-match-sap', [$compensation->id]) }}" method="post" class="match-form">
                            {{Form::token()}}
                            <input type="hidden" name="new_premium" value="1">
                            <button type="submit" class="btn btn-primary">Procesuj jako dopłatę</button>
                        </form>
                    </div>
                    <div class="col-sm-6 text-center">
                        @if(! $compensation->injury->compensations()->where('mode', 1)->first())
                            <form action="{{ URL::to('injuries/document/save-match-sap', [$compensation->id]) }}" method="post" class="match-form">
                                {{Form::token()}}
                                <input type="hidden" name="new" value="1">
                                <button type="submit" class="btn btn-info">Procesuj jako wypł. bez dopłat</button>
                            </form>
                        @endif
                    </div>
                </div>
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
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    </div>
</div>
<script>
    $('form.match-form').submit(function(e){
        $.ajax({
            type: "POST",
            url: '{{ URL::to('injuries/document/save-match-sap', [$compensation->id]) }}',
            data: $(this).serialize(),
            assync: false,
            cache: false,
            success: function (data) {
                $('#match-sap-container').html(data);
            },
            dataType: 'text'
        });

        e.preventDefault();
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