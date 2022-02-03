<div class="tab-pane fade in " id="documentation">
        <div class="row marg-btm">
          <div class="col-sm-12">
              @if(Auth::user()->can('zlecenia#zarzadzaj'))
                  {{ Form::open( [ 'url' => URL::route('dos.other.injuries.post-document', array($injury->id)) , 'class' => 'dropzone fileUploads' , 'id' => 'fileForm', 'files'=>true ] ) }}
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                  {{ Form::close() }}
              @endif
          </div>
        </div>
        <div class="row">
          <div class="col-sm-8  col-sm-offset-2 ">

              <form id="doc-list-form">
                  <table class="table table-hover" >
                    @foreach($documents as $k => $v)
                      <tr class="vertical-middle">
                          <td>
                              <div class="checkbox">
                                  <label>
                                      <input type="checkbox" class="docs_to_send" name="docs_to_send[]" value="{{ $v->id }}">
                                  </label>
                              </div>
                          </td>
                        <td width="10px">{{++$k}}.</td>
                        <td width="180px" class="download_doc_td">
                            @if($v->type == 4)
                                <a href="{{ URL::route('dos.other.injuries.downloadDoc', array($v->id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                                <i>Mail wysłany z systemu</i>
                            @elseif($v->type == 2)
                                <a href="{{ URL::route('dos.other.injuries.downloadDoc', array($v->id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                                <i>dok. wgrany</i>
                              @else
                                <a href="{{ URL::route('dos.other.injuries.downloadGenerateDoc', array($v->id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                                <i>dok. wygenerowany</i>
                            @endif
                        </td>
                        <td>
                          @if($v->type == 2 || $v->type == 4)
                          {{ Config::get('definition.fileCategory.'.$v->category)}}<br>
                          <i>{{ $v->name }}</i>
                          @else
                          {{ $v->document_type()->first()->name }}
                            @if($v->name != '')
                              <br>
                              <i>{{ $v->name }}</i>
                            @endif
                          @endif
                        </td>
                        <Td>
                          {{ $v->user->name }}
                        </td>
                        <Td>
                          {{substr($v->created_at, 0, -3)}}
                        </td>
                        <Td>
                        @if(Auth::user()->can('zlecenia#zarzadzaj'))
                            @if($v->type == 2 || $v->type == 4)
                              <button type="button" class="btn btn-danger modal-open-sm" target="{{ URL::route('dos.other.injuries.getDelDoc', array($v->id)) }}"  data-toggle="modal" data-target="#modal-sm">usuń</button>
                            @else
                              <button type="button" class="btn btn-danger modal-open" target="{{ URL::route('dos.other.injuries.getDelDocConf', array($v->id)) }}"  data-toggle="modal" data-target="#modal">usuń</button>
                            @endif
                          @endif
                        </td>
                      </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                              <span class="btn btn-sm btn-primary disabled" id="send-docs" target="{{ URL::route('dos.other.injuries.get', array('docsSendDialog',$injury->id)) }}"  data-toggle="modal" data-target="#modal-lg">
                                  <i class="fa fa-paper-plane-o"></i> wyślij dokumenty
                              </span>
                            <span class="btn btn-sm btn-primary disabled off-disable" id="download-docs" target="{{ URL::route('dos.other.injuries.get', array('downloadDocs', $injury->id)) }}" >
                          <i class="fa fa-floppy-o"></i> pobierz dokumenty
                      </span>
                        </td>
                    </tr>
                  </table>
              </form>
          </div>
        </div>
  </div>


@section('headerJs')
    @parent

    <script>
        function checkFiles() {
            var checked = false;

            $('.docs_to_send').each(function(){
                if( $(this).prop('checked') )
                {
                    checked = true;
                }
            });

            if(! checked )
            {
                $('#send-docs').addClass('disabled');
                $('#download-docs').addClass('disabled');
            }else{
                $('#send-docs').removeClass('disabled');
                $('#download-docs').removeClass('disabled');
            }
        }
        $('.docs_to_send').on('change', function(){
            checkFiles();
        });

        $('#send-docs').on('click', function(){
            var docs = $('#doc-list-form').serialize();
            var hrf=$(this).attr('target')+'?'+docs;
            $.get( hrf, function( data ) {
                $('#modal-lg .modal-content').html(data);
            });
        });

        $('#download-docs').on('click', function(){
            var docs = $('#doc-list-form').serialize();
            var hrf=$(this).attr('target')+'?'+docs;
            window.location.href = hrf;
            checkFiles();
        });
    </script>
@endsection
