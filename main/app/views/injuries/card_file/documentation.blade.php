@if(Auth::user()->can('kartoteka_szkody#dokumentacja'))
    <div class="tab-pane fade in " id="documentation">
        @if(Auth::user()->can('kartoteka_szkody#dokumentacja#dodaj_dokument'))
            <div class="row marg-btm">
                <div class="col-sm-12">
                  {{ Form::open( [ 'url' => URL::route('injuries-post-document', array($injury->id)) , 'class' => 'dropzone fileUploads' , 'id' => 'fileForm', 'files'=>true ] ) }}
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                  {{ Form::close() }}
                </div>
            </div>
        @endif
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
                  <td width="10px">{{ $documents->count() - $k }}.</td>
                  @if($v->type == 4)
                      <td>
                          <a href="{{ URL::route('injuries-downloadDoc', array($v->id)) }}" target="_blank"
                             class="fa fa-floppy-o blue pointer md-ico"></a>
                      </td>
                      <td>
                          <span>Mail wysłany z systemu</span>
                      </td>
                  @elseif($v->type == 2)
                      <td>
                          <a href="{{ URL::route('injuries-downloadDoc', array($v->id)) }}" target="_blank"
                             class="fa fa-floppy-o blue pointer md-ico"></a>
                          @if($v->file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$v->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                              <span class="modal-open-lg marg-left"
                                    target="{{ URL::to('injuries/dialog/preview-doc', array($v->id)) }}"
                                    data-toggle="modal" data-target="#modal-lg">
                                <i class="fa fa-search blue pointer md-ico"></i>
                              </span>
                          @endif
                          @if($v->if_fee_collected == 1)
                              <i class="fa fa-money fa-2x blue"></i>
                          @endif
                      </td>
                      <td>
                          <span>dok. wgrany</span>
                      </td>
                  @else
                      <td>
                          <a href="{{ URL::route('injuries-downloadGenerateDoc', array($v->id)) }}" target="_blank"
                             class="fa fa-floppy-o blue pointer md-ico"></a>
                          <span class="modal-open-lg marg-left"
                                target="{{ URL::to('injuries/dialog/preview-doc', array($v->id, $v->category)) }}"
                                data-toggle="modal" data-target="#modal-lg">
                            <i class="fa fa-search blue pointer md-ico"></i>
                          </span>
                          @if($v->if_fee_collected == 1)
                              <i class="fa fa-money fa-2x blue"></i>
                          @endif
                      </td>
                      <td>
                          <span>dok. wygenerowany</span>
                      </td>
                  @endif
                  <td>
                      @if($v->type == 2)
                          {{ $v->document->name }}<br>
                          <i>
                              @if($v->category == 23 || $v->category == 48)
                              {{ number_format( (float) $v->name , 2, ',', '') }} zł
                          @else
                              {{ $v->name }}
                          @endif
                      </i>
                      @else
                      {{ $v->document->name }}
                        @if($v->name != '')
                          <br>
                          <i>
                            {{ $v->name }}
                          </i>
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
                      @if(Auth::user()->can('kartoteka_szkody#dokumentacja#usun_dokument') )
                        @if($v->type == 2 )
                            @if(Auth::user()->can('kartoteka_szkody#dokumentacja#dodaj_usun_dokument_'.$v->document_id))
                                <button type="button" class="btn btn-danger modal-open-sm" target="{{ URL::route('injuries-getDelDoc', array($v->id)) }}"  data-toggle="modal" data-target="#modal-sm">usuń</button>
                            @endif
                        @else
                              @if(Auth::user()->can('kartoteka_szkody#dokumentacja#usun_dokument_generowany'))
                                <button type="button" class="btn btn-danger modal-open" target="{{ URL::route('injuries-getDelDocConf', array($v->id)) }}"  data-toggle="modal" data-target="#modal">usuń</button>
                              @endif
                        @endif
                      @endif
                  </td>
              </tr>
            @endforeach
              <tr>
                  <td colspan="8">
                      @if(Auth::user()->can('kartoteka_szkody#dokumentacja#wyslij_dokument'))
                          <span class="btn btn-sm btn-primary disabled" id="send-docs" target="{{ URL::route('injuries-docs-send-dialog', array($injury->id)) }}"  data-toggle="modal" data-target="#modal-lg">
                            <i class="fa fa-paper-plane-o"></i> wyślij dokumenty
                          </span>
                      @endif
                      <span class="btn btn-sm btn-primary disabled off-disable" id="download-docs" target="{{ URL::route('injuries-downloadDocs', array($injury->id)) }}" >
                          <i class="fa fa-floppy-o"></i> pobierz dokumenty
                      </span>
                  </td>
              </tr>
          </table>
          </form>
          </div>
        </div>
        @if(!$matchedLetters->isEmpty())
        <div class="row">
            <div class="col-sm-8  col-sm-offset-2 ">
              <div class="panel panel-primary">
                <div class="panel-heading">Dostępne pisma, dopasowane względem szkody</div>
                <div class="panel-body">
                  <div class="table-responsive" style="float: inherit;">
                      <table class="table table-condensed">
                        <thead>
                          <Th style="width:30px;">lp.</th>
                          <Th>typ dokumentu</Th>
                          <th>nazwa pisma</th>
                          <th>nr szkody</th>
                          <th>nr umowy</th>
                          <th>nr rejestracyjny</th>
                          <th>data wprowadzenia</th>
                          <th></Th>
                          <th></th>
                          <Th></Th>
                          <th></th>
                        </thead>
                        <?php $lp = 1;?>
                        @foreach ($matchedLetters as $letter)
                          <tr class="vertical-middle">
                            <td>{{$lp++}}.</td>
                            <td>{{ $letter->uploadedDocumentType->name }}</td>
                            <td>{{ $letter->name }}</td>
                            <td>{{ $letter->injury_nr }}</td>
                            <td>{{ $letter->nr_contract }}</td>
                            <td>{{ $letter->registration }}</td>
                            <td>{{substr($letter->created_at, 0, -3)}}</td>
                            <td>
                              @if( trim($letter->description) != '')
                                <a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="Opis pisma" data-content="{{ $letter->description }}"><i class="fa fa-info-circle"></i> opis</a>
                              @endif
                            </td>
                            <td><a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" class="btn btn-sm btn-info " off-disable><i class="fa fa-download"></i> pobierz</a> </td>
                            <Td>
                              <button target="{{ URL::route('routes.get', ['dialogs', 'injuries', 'appendLetter', $letter->id, $injury->id])}}" class="btn btn-sm btn-success modal-open" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-sign-in"></i> przypisz do szkody
                              </button>
                            </Td>
                          </tr>
                        @endforeach
                      </table>
                  </div>
                </div>
              </div>
            </div>
        </div>
        @endif
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
@endif
