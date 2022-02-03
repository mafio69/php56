<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Podpis użytkownika {{$user_db->name}}</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="progress progress-striped active margin-bottom margin-top-md" id="uploading-progressbar" style="display: none;">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="uploader btn btn-default btn-block off-disable pointer">
                    <i class="fa fa-plus fa-fw"></i>
                    <span class="desc">wgraj skan</span>
                    <input id="change_logo" name="file" type="file" style="opacity: 0;position: absolute;top: 0;height: 100%;width: 100%;">
                </div>
            </div>
            <div class="col-sm-6 col-sm-offset-3">
                <br>
                <img class="img img-responsive" src="{{($user_db->signature) ? url('settings/users/show-signature/'.$user_db->signature) : ''}}">
            </div>
            <form action="{{ url('settings/users/signature/'.$user_db->id) }}" method="post" id="dialog-form">
                {{ Form::hidden('filename') }}
                {{ Form::token() }}
            </form>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa zapisywanie">Zapisz</button>
</div>

<script>
    $(document).ready(function() {
        $('#change_logo').change(uploadImage);
    });

    function uploadImage(){
        var data = new FormData();

        var file = $('#change_logo')[0].files[0];

        data.append("file", file);
        data.append("_token", $('input[name="_token"]').val());

        $('#uploading-progressbar').show();
        $('.uploader').button('loading');
        $.ajax({
            data: data,
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) myXhr.upload.addEventListener('progress',progressHandlingFunction, false);
                return myXhr;
            },
            url: '{{ URL::to('settings/users/upload-signature/') }}',
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(file) {
                $('[name="filename"]').val(file.filename);

                $('.modal-body .img').attr('src','/settings/users/show-signature/'+file.filename);

                $('#uploading-progressbar').hide();
                $('#uploading-progressbar .progress-bar').css('width','0%');
                $('.uploader').button('reset');

                setTimeout(function(){
                    $('.uploader').remove();
                    //$("<input name='file' type='file' id='change_logo'/>").change(uploadImage).appendTo($('.uploader'));
                },500);
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
