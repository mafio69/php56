    <div class="btn-group pull-right">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            wprowadzanie umów <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="{{ url('gap/agreements/create') }}" >
                    <span class="glyphicon glyphicon-plus-sign"></span> Wprowadź ręcznie nową umowę
                </a>
            </li>
            <li class="divider"></li>
                <li>
                    <span class="fileinput-button let_disable">
                        <i class="fa fa-upload"></i> Wgraj nowe umowy</a>
                        <form id="fileupload-new" method="POST" class="fileupload" data-url="{{ url('gap/upload/uploadFile/new') }}">
                            {{ Form::token() }}
                            <input type="file" name="file" >
                        </form>
                    </span>
                </li>
        </ul>
    </div>

    @section('headerJs')
        @parent
        <script type="text/javascript">
            $(function () {
                $('.fileupload').fileupload({
                    singleFileUploads: true,
                    url: $(this).data('url'),
                    dataType: 'json',
                    add: function (e, data) {
                        var dialog_href= "{{ url('gap/upload/uploadDialog/') }}";
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
                                    $('#insuranceFileUploadDialog').html(response.msg);
                                    $('#insuranceFileUploadDialogClose').removeAttr('disabled');
                                }else if(response.status == 'success'){
                                    self.location = response.redirect;
                                }
                            }
                        , 200);
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress .progress-bar').css(
                                'width',
                                progress + '%'
                        ).attr('aria-valuenow', progress).html(progress + '%');
                    }
                });


            });
        </script>
    @stop

