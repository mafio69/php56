<div class="tab-pane fade in " id="files">
    <div class="row marg-btm">
        <div class="col-sm-12">
            @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                {{ Form::open( [ 'url' => URL::to('insurances/info-dialog/upload-document', [$agreement->id]) , 'class' => 'dropzone fileUploads' , 'id' => 'fileForm', 'files'=>true ] ) }}
                <div class="fallback">
                    <input name="file" type="file" multiple/>
                </div>
                {{ Form::close() }}
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8  col-sm-offset-2 ">
            <table class="table table-hover">
                @foreach($agreement->files as $k => $v)
                    <tr class="vertical-middle">
                        <td width="10px">{{++$k}}.</td>
                        <td>
                            <a href="{{ URL::to('insurances/manage-actions/download-document', [$v->id]) }}"
                               target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                        </td>
                        <td>
                            <span>dok. wgrany</span>
                        </td>
                        <td>
                            {{ Config::get('definition.insurancesFileCategory.'.$v->category) }}<br>
                            <i>{{ $v->name }}</i>
                        </td>
                        <Td>
                            {{ $v->user->name }}
                        </td>
                        <Td>
                            {{substr($v->created_at, 0, -3)}}
                        </td>
                        <Td>
                            @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                <button type="button" class="btn btn-danger modal-open-sm"
                                        target="{{ URL::to('insurances/info-dialog/delete-document', [$v->id]) }}"
                                        data-toggle="modal" data-target="#modal-sm">usuń
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

@section('headerJs')
    @parent

    @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
    <script>
        var filesA = new Array();
        Dropzone.options.fileForm = {
            init: function () {
                this.on("complete", function (file) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        var hrf = "<?php echo URL::to('insurances/info-dialog/assign-uploaded-document');?>";
                        $.post(hrf, {
                            "files": JSON.stringify(filesA),
                            "_token": $('input[name="_token"]').val()
                        }, function (data) {
                            $('#modal .modal-content').html(data);
                            $('#modal').modal({
                                keybord: false,
                                backdrop: false
                            }).modal('show');
                        });
                    }
                });
            },
            success: function (file, response) {
                response = tryParseJSON(response);
                filesA.push(response.id);
            }
        };

        $('#modal').on('click', '#set_docs', function () {
            var btn = $(this);
            if ($("#dialog-set-doc-form").valid()) {
                btn.attr('disabled', 'disabled');
                $.post(
                    $('#dialog-set-doc-form').prop('action'),
                    $('#dialog-set-doc-form').serialize()
                    ,
                    function (data) {
                        if (data != '0') {
                            filesA = new Array();

                            window.location.hash = "#files";

                            $('#modal').modal('hide');

                            window.location.reload();
                        } else {
                            $('#modal .modal-body').html(data);
                        }
                    },
                    'json'
                );
            }
            return false;
        });
    </script>
    @endif
@endsection
