@extends('layouts.main')


@section('header')

    Lista rejestracji VIP
@stop

@section('main')
    <div class="row">

        <div class="col-sm-12">
            <div class="progress progress-striped active margin-bottom margin-top-md" id="uploading-progressbar" style="display: none;">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="uploader btn btn-block btn-primary btn-sm" >
                <i class="fa fa-upload" aria-hidden="true"></i>
                wgraj zestawienie
                <input class="file" name="file" type="file" style="width: 90%;opacity: 0;position: absolute;top: 0;height: 100%;">
            </div>
        </div>
        <div class="col-sm-12 marg-top-min">
            <ul class="nav nav-pills">
                <li role="presentation"><a href="{{ URL::to('vehicle-manage/import/vip-clients') }}">Historia importów</a></li>
                <li role="presentation" class="active"><a href="{{ URL::to('vehicle-manage/import/registrations') }}">Lista rejestracji VIP</a></li>
            </ul>
        </div>
        <div class="col-sm-12">
            <nav class="navbar navbar-default marg-top-min">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <form id="search-form" class="navbar-form navbar-left" action="{{ URL::to('vehicle-manage/import/registrations') }}" method="get">
                            <div class="form-group">
                                <input name="registration" type="text" class="form-control input-sm" placeholder="nr rejestracyjny" value="{{ Request::get('registration') }}">
                            </div>
                            <button type="submit" class="btn btn-default btn-sm">
                                <i class="fa fa-search fa-fw"></i>
                                wyszukaj
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default ">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead>
                            <th>lp.</th>
                            <th>rejestracja</th>
                            <th>data importu</th>
                            <th>osoba wgrywająca</th>
                            <td></td>
                            </thead>
                            <?php
                            $lp = (($registrations->getCurrentPage()-1)*$registrations->getPerPage()) + 1;
                            ?>
                            @foreach ($registrations as $registration)
                                <tr class="vertical-middle">
                                    <td width="20px">{{ $lp++ }}.</td>
                                    <td>{{ $registration->registration }}</td>
                                    <td>{{ ($registration->import) ? $registration->import->created_at->format('Y-m-d H:i') : ''}}</td>
                                    <td>{{ ($registration->import) ? $registration->import->user->name : ''}}</td>
                                    <td>
                                        <span class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('vehicle-manage/import/detach-registration', [$registration->id]) }}">
                                            <i class="fa fa-trash fa-fw"></i>
                                            usuń z bazy
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div style="clear:both;">{{ $registrations->appends(Input::all())->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[name="registration"]').on('keyup', function(e){
                if (event.which == 13 || event.keyCode == 13) {
                    $('#search-form').submit();
                }
            });
            $('.file').change(upload);
        });

        function upload(){
            var data = new FormData();
            data.append("_token", $('meta[name="csrf-token"]').attr('content'));

            $.each( $(this)[0].files , function (idx, file) {
                data.append("file", file);
            });

            $('#uploading-progressbar').show();
            $.ajax({
                data: data,
                type: 'POST',
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) myXhr.upload.addEventListener('progress',progressHandlingFunction, false);
                    return myXhr;
                },
                url: '{{ URL::action('VmanageImportController@postUploadVipClients') }}',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function() {
                    location.reload();
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

@stop
