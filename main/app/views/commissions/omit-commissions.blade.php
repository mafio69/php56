<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Potwierdź nieliczenie do prowizji</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form id="omission-form">
        <div class="form-group">
            <label>Przyczyna nieliczenia do prowizji</label>
            {{ Form::textarea('omission_reason', null, ['class' => 'form-control']) }}
        </div>
        {{ Form::hidden('omission_attachment', null) }}
    </form>
    <div id="progress" class="progress marg-top" style="display: none;">
        <div id="progress-bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="form-group text-center fileupload-container" >
        <span class="btn btn-primary fileinput-button let_disable">
            <i class="fa fa-upload"></i> Wgraj załącznik</a>
            <form id="fileupload-attachment" method="POST">
            {{ Form::token() }}
                <input type="file" name="file" >
            </form>
        </span>
    </div>

    <h3 class="text-center text-success file-uploaded" style="display: none;">
        <i class="fa fa-check"></i>
    </h3>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="trwa wykonywanie..." id="omit">Potwierdź</button>
</div>

<script>
    $('#fileupload-attachment').fileupload({
        singleFileUploads: true,
        url: "{{ url('commissions/upload-attachment') }}",
        dataType: 'json',
        add: function (e, data) {
            $('#progress').show();

            if (e.isDefaultPrevented()) {
                return false;
            }
            if (data.autoUpload || (data.autoUpload !== false &&
                $(this).fileupload('option', 'autoUpload'))) {
                data.process().done(function () {
                    data.submit();
                });
            }

        },
        done: function (e, data) {
            var response = data.result;
            $('input[name="omission_attachment"]').val(response.filename);

            $('#progress, .fileupload-container').hide();
            $('.file-uploaded').show();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress-bar').css(
                'width',
                progress + '%'
            ).attr('aria-valuenow', progress).html(progress + '%');
        }
    });
</script>