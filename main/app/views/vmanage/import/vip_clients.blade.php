@extends('layouts.main')


@section('header')

    Import rejestracji pojazdów klientów VIP
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
                <li role="presentation" class="active"><a href="{{ URL::to('vehicle-manage/import/vip-clients') }}">Historia importów</a></li>
                <li role="presentation"><a href="{{ URL::to('vehicle-manage/import/registrations') }}">Lista rejestracji VIP</a></li>
            </ul>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default marg-top">
                <div class="panel-body">
                    <h4>Historia importów</h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead>
                            <th>lp.</th>
                            <th>data importu</th>
                            <th>osoba wgrywająca</th>
                            <th>wgrane rejestracje</th>
                            </thead>
                            <?php
                            $lp = (($imports->getCurrentPage()-1)*$imports->getPerPage()) + 1;
                            ?>
                            @foreach ($imports as $import)
                                <tr class="vertical-middle">
                                    <td width="20px">{{ $lp++ }}.</td>
                                    <td>{{ $import->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $import->user->name }}</td>
                                    <td>
                                        <a href="{{ URL::to('vehicle-manage/import/vip-client-registrations', [$import->id]) }}" class="label label-info">
                                            rejestracje VIP <span class="badge">{{ $import->vips->count() }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div style="clear:both;">{{ $imports->appends(Input::all())->links() }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('headerJs')
    @parent
    <script>
        $(document).ready(function() {
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
