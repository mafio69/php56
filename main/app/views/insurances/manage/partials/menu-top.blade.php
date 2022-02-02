@if(Auth::user()->can('wykaz_polis#wprowadzenie_umowy'))
    <div class="btn-group pull-right">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            wprowadzanie umów <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="{{ URL::to('insurances/create/index') }}" >
                    <span class="glyphicon glyphicon-plus-sign"></span> Wprowadź ręcznie nową umowę majątku
                </a>
            </li>
            <li>
                <a href="{{ URL::to('insurances/create-yacht/index') }}" >
                    <span class="glyphicon glyphicon-plus-sign"></span> Wprowadź ręcznie nową umowę jachtu
                </a>
            </li>
            <li class="divider"></li>
                <li>
                    <span class="fileinput-button let_disable">
                        <i class="fa fa-upload"></i> Wgraj nowe umowy majątku
                        <form id="fileupload-new" method="POST" class="fileupload" data-url="{{ URL::to('insurances/upload/upload-file', ['new'] ) }}">
                            {{ Form::token() }}
                            <input type="file" name="file" >
                        </form>
                    </span>
                </li>
                <li>
                    <span class="fileinput-button let_disable">
                        <i class="fa fa-upload"></i> Wgraj wznowienia umów majątku
                        <form id="fileupload-resume" method="POST" class="fileupload" data-url="{{ URL::to('insurances/upload/upload-file', ['resume'] ) }}">
                            {{ Form::token() }}
                            <input type="file" name="file" >
                        </form>
                    </span>
                </li>
            <li class="divider"></li>
                <li>
                    <span class="fileinput-button let_disable">
                        <i class="fa fa-upload"></i> Wgraj umowy jachtów
                        <form id="fileupload-yachts" method="POST" class="fileupload" data-url="{{ URL::to('insurances/upload/upload-file', ['yachts'] ) }}">
                            {{ Form::token() }}
                            <input type="file" name="file" >
                        </form>
                    </span>
                </li>
            <li class="divider"></li>
            <li>
                <span class="fileinput-button let_disable">
                    <i class="fa fa-upload"></i> Wgraj raport ubezpieczyciela
                    <form id="fileupload-report" method="POST" class="fileupload" data-url="{{ URL::to('insurances/upload/upload-file', ['report'] ) }}">
                        {{ Form::token() }}
                        <input type="file" name="file" >
                    </form>
                </span>
            </li>
            <li class="divider"></li>
            <li>
                <span class="fileinput-button let_disable">
                    <i class="fa fa-upload"></i> Wgraj numery polis
                    <form id="fileupload-policies-number" method="POST" class="fileupload" data-url="{{ URL::to('insurances/upload/import-policies-number' ) }}">
                        {{ Form::token() }}
                        <input type="file" name="file" >
                    </form>
                </span>
            </li>
        </ul>
    </div>
    {{--<div class="btn btn-primary pull-right marg-right">--}}
        {{--<span class="fileinput-button let_disable">--}}
            {{--<i class="fa fa-upload"></i> import polis--}}
            {{--<form id="fileupload-resume" method="POST" class="fileupload" data-url="{{ URL::route('insurances.post', ['upload', 'uploadFile', 'resume'] ) }}">--}}
                {{--{{ Form::token() }}--}}
                {{--<input type="file" name="file" >--}}
            {{--</form>--}}
        {{--</span>--}}
        {{--<i class="fa fa-upload"></i>--}}
    {{--</div>--}}

    @section('headerJs')
        @parent
        <script type="text/javascript">
            $(function () {
                $('.fileupload').fileupload({
                    singleFileUploads: true,
                    url: $(this).data('url'),
                    dataType: 'json',
                    add: function (e, data) {
                        var dialog_href= "{{ URL::to('insurances/upload/upload-dialog' ) }}";
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

@endif