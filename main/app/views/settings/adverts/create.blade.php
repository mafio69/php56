@extends('layouts.main')

@section('header')

<span class="pull-left">
Dodawanie reklam {{ $resolution_type->x_ax }} x {{ $resolution_type->y_ax }}
</span>

<div class="pull-right">
    <a href="{{{ URL::route('settings.adverts') }}}" class="btn btn-small btn-primary ">Gotowe</a>
</div>
@stop

@section('main')
<div class="row marg-btm">
    <div class="col-xs-12 col-sm-10 col-md-8  col-xs-offset-0 col-sm-offset-1 col-md-offset-2 ">
        {{ Form::open( [ 'url' => URL::route('settings.adverts.store', array($resolution_type->id)) , 'class' => 'dropzone advertsUploads' , 'id' => 'imgUpload', 'files'=>true ] ) }}

            <div class="fallback" >
                <input name="file" type="file" multiple />
            </div>

        {{ Form::close() }}
    </div>
</div>
<div class="row marg-top" id="adverts_to_cut_container">
    <div class="col-xs-12" >
        {{ Form::open(array('route' => 'settings.adverts.cut', 'id'=> 'cutForm')) }}
        <input type="hidden" name="resolution_x" value="{{  $resolution_type->x_ax }}"/>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Zdjęcia wymagające przycięcia</h3>
            </div>
            <div class="panel-body" id="adverts_to_cut">
            </div>
            <div class="panel-footer overflow">
                <button type="submit"  class="btn btn-primary btn-sm pull-right">Zapisz poprawione zdjęcia</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>


@stop


@section('headerJs')
@parent
<script type="text/javascript">
    $(document).ready(function() {
        $('.modal-open').on('click',  function(){
            hrf=$(this).attr('target');
            $.get( hrf, function( data ) {
                $('#modal .modal-content').html(data);
            });
        });

        $('#modal').on('click', '#set', function(){
            $.ajax({
                type: "POST",
                url: $('#dialog-form').prop( 'action' ),
                data: $('#dialog-form').serialize(),
                assync:false,
                cache:false,
                success: function( data ) {
                    if(data.code == '0') location.reload();
                    else if(data.code == '1') self.location = data.url;
                    else{
                        $('#modal .modal-body').html( data.error);
                        $('#set').attr('disabled',"disabled");
                    }
                },
                dataType: 'json'
            });
            return false;
        });

        $(document).on('click', '.remove-panel', function(){
           $(this).parent().parent().remove();
        });

        var $croppers = new Array();
        Dropzone.options.imgUpload = {
            acceptedFiles: "image/*",
            dictInvalidFileType: "Przesłany plik nie jest zdjęciem",
            error: function(file, message) {
                var node, _i, _len, _ref, _results;
                if (file.previewElement) {
                    file.previewElement.classList.add("dz-error");
                    if (typeof message !== "String" && message.error) {
                        if(message.status == 1){
                            $('#adverts_to_cut_container').show();
                            $( "#adverts_to_cut" ).append( message.content );
                            $croppers = new Array();

                            $('#adverts_to_cut_container .bootstrap-modal-cropper').each(function(){
                                var $img = $(this).find('img');
                                $croppers[$img.attr('idImg')] = $img.cropper({
                                    aspectRatio: message.ratio
                                });
                            });
                        }
                        message = message.error;
                    }
                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        node = _ref[_i];
                        _results.push(node.textContent = message);
                    }
                    return _results;
                }

            }
        };
        $("#cutForm").submit(function(e) {
            var self = this;
            e.preventDefault();

            for (key in $croppers) {
                if (arrayHasOwnIndex($croppers, key)) {
                    var crop_data =  $croppers[key].cropper("getData") ;
                    $('#dataX_'+key).val(crop_data.x);
                    $('#dataY_'+key).val(crop_data.y);
                    $('#dataHeight_'+key).val(crop_data.height);
                    $('#dataWidth_'+key).val(crop_data.width);
                }
            }

            self.submit();

            return false; //is superfluous, but I put it here as a fallback
        });

        //originalData = $image.cropper("getData");
        //$image.cropper("destroy");
    });
</script>

@stop

