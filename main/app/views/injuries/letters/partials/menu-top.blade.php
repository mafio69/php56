<div class="pull-right">
    <div class="btn-group">
        <a href="{{ URL::route('routes.post', ['injuries', 'letters', 'reportNonAppended'] ) }}" class="btn btn-info">
            <i class="fa fa-file-excel-o fa-fw"></i> Zestawienie nieprzypisanych
        </a>
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="{{ URL::route('routes.post', ['injuries', 'letters', 'reportNonAppended'] ) }}">Zestawienie nieprzypisanych</a></li>
            <li><a href="{{ URL::route('routes.post', ['injuries', 'letters', 'reportAppended'] ) }}">Zestawienie przypisanych</a></li>
        </ul>
    </div>

    <span class="btn btn-primary fileinput-button let_disable">
        <i class="fa fa-upload"></i> Wgraj pismo</a>
        <form id="fileupload-letter" method="POST">
            {{ Form::token() }}
            <input type="file" name="file" >
        </form>
    </span>
</div>

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(function () {
            $('#fileupload-letter').fileupload({
                singleFileUploads: true,
                url: "{{ URL::route('routes.post', ['injuries', 'letters', 'uploadLetter'] ) }}",
                dataType: 'json',
                add: function (e, data) {
                    var dialog_href= "{{ URL::route('routes.get', ['injuries', 'letters', 'uploadDialog'] ) }}";
                    $.get( dialog_href, function( data ) {
                        $('#modal .modal-content').html(data);
                    });
                    $('#modal').modal('show');

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
                    setTimeout(
                            function(){
                                if(response.status == 'error'){
                                    $('#fileUploadDialog').html(response.msg);
                                    $('#fileUploadDialogClose').removeAttr('disabled');
                                }else if(response.status == 'success'){
                                    self.location = response.redirect;
                                }
                            }
                            , 200);
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress-bar').css(
                            'width',
                            progress + '%'
                    ).attr('aria-valuenow', progress).html(progress + '%');
                }
            });
        });
    </script>
@stop

