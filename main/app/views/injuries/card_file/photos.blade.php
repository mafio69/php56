@if(Auth::user()->can('kartoteka_szkody#zdjecia'))
    <div class="tab-pane fade in " id="photos">
        <div class="row">
            <div class="col-sm-12 text-right marg-btm">
                <span class="btn btn-sm btn-info" id="download-photos">
                    <i class="fa fa-floppy-o fa-fw"></i>
                    pobierz wybrane zdjęcia
                </span>
            </div>
        </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading "><label>Przyjęcie:</label></div>
                  <div class="row">
                      @if(Auth::user()->can('kartoteka_szkody#zdjecia#dodaj_zdjecia'))
                        <div class="col-sm-12">
                        {{ Form::open( [ 'url' => URL::route('injuries-post-image', array($injury->id, 1)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgBefore', 'files'=>true ] ) }}
                          <div class="fallback">
                              <input name="file" type="file" multiple />
                          </div>
                        {{ Form::close() }}
                        </div>
                      @endif
                  </div>
                  <div class="row marg-top-min">
                    <div class="col-sm-12">
                    <?php foreach ($imagesBefore as $k => $v) {?>
                      <div class="col-sm-4 col-md-2" id="image-{{$v->id}}">
                          <div class="thumbnail">
                              <div class="checker pull-right">
                                  <label>
                                      <input type="checkbox" name="downloads[]" value="{{ $v->id }}">
                                  </label>
                              </div>
                              <div class="image-container">
                                  <a href="/file/uploads/images/{{$v->file}}/full" data-lightbox="image-before" >
                                    {{ HTML::image('/file/uploads/images/'.$v->file.'/thumb') }}
                                  </a>
                              </div>
                              <div class="caption">
                                  @if(Auth::user()->can('kartoteka_szkody#zdjecia#usun_zdjecie'))
                                    <button type="button" class="btn btn-danger btn-xs modal-open-sm" target="{{ URL::route('injuries-getDelImage', array($v->id)) }}" data-toggle="modal" data-target="#modal-sm">usuń</button>
                                  @endif
                                  <a href="/file/uploads/images/{{$v->file}}/full" target="_blank">
                                      <button class="btn btn-info btn-xs ">
                                        <i class="fa fa-floppy-o fa-fw"></i>
                                      </button>
                                  </a>
                                  <span>{{ $v->user->name }}</span>
                                  <span>{{substr($v->created_at, 0, -3)}}</span>
                              </div>
                          </div>
                      </div>
                    <?php }?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading "><label>W trakcie:</label></div>
                  <div class="row">
                      @if(Auth::user()->can('kartoteka_szkody#zdjecia#dodaj_zdjecia'))
                        <div class="col-sm-12">
                        {{ Form::open( [ 'url' => URL::route('injuries-post-image', array($injury->id, 2)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgInprogress',  'files'=>true ] ) }}
                          <div class="fallback">
                              <input name="file" type="file" multiple />
                          </div>
                        {{ Form::close() }}
                        </div>
                      @endif
                  </div>
                  <div class="row marg-top-min">
                    <div class="col-sm-12">
                    <?php foreach ($imagesInprogress as $k => $v) {?>
                      <div class="col-sm-4 col-md-2"  id="image-{{$v->id}}">
                          <div class="thumbnail">
                              <div class="checker pull-right">
                                  <label>
                                      <input type="checkbox" name="downloads[]" value="{{ $v->id }}">
                                  </label>
                              </div>
                              <div class="image-container">
                                <a href="/file/uploads/images/{{$v->file}}/full" data-lightbox="image-inprogress" >
                                  {{ HTML::image('/file/uploads/images/'.$v->file.'/thumb') }}
                                </a>
                              </div>
                              <div class="caption">
                                  @if(Auth::user()->can('kartoteka_szkody#zdjecia#usun_zdjecie'))
                                      <button type="button" class="btn btn-danger btn-xs modal-open-sm" target="{{ URL::route('injuries-getDelImage', array($v->id)) }}"  data-toggle="modal" data-target="#modal-sm">usuń</button>
                                  @endif
                                  <a href="/file/uploads/images/{{$v->file}}/full" target="_blank">
                                      <button class="btn btn-info btn-xs ">
                                          <i class="fa fa-floppy-o fa-fw"></i>
                                      </button>
                                  </a>
                                  <span>{{ $v->user->name }}</span>
                                  <span>{{substr($v->created_at, 0, -3)}}</span>
                              </div>
                          </div>
                      </div>
                    <?php }?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading "><label>Po naprawie:</label></div>
                  <div class="row">
                      @if(Auth::user()->can('kartoteka_szkody#zdjecia#dodaj_zdjecia'))
                        <div class="col-sm-12">
                        {{ Form::open( [ 'url' => URL::route('injuries-post-image', array($injury->id, 3)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgAfter',  'files'=>true ] ) }}
                          <div class="fallback">
                              <input name="file" type="file" multiple />
                          </div>
                        {{ Form::close() }}
                        </div>
                      @endif
                  </div>
                  <div class="row marg-top-min">
                    <div class="col-sm-12">
                    <?php foreach ($imagesAfter as $k => $v) {?>
                      <div class="col-sm-4 col-md-2"  id="image-{{$v->id}}">
                          <div class="thumbnail">
                              <div class="checker pull-right">
                                  <label>
                                      <input type="checkbox" name="downloads[]" value="{{ $v->id }}">
                                  </label>
                              </div>
                              <div class="image-container">
                                <a href="/file/uploads/images/{{$v->file}}/full" data-lightbox="image-after" >
                                  {{ HTML::image('/file/uploads/images/'.$v->file.'/thumb') }}

                                </a>
                              </div>
                              <div class="caption">
                                  @if(Auth::user()->can('kartoteka_szkody#zdjecia#usun_zdjecie'))
                                      <button type="button" class="btn btn-danger btn-xs modal-open-sm" target="{{ URL::route('injuries-getDelImage', array($v->id)) }}" data-toggle="modal" data-target="#modal-sm">usuń</button>
                                  @endif
                                  <a href="/file/uploads/images/{{$v->file}}/full" target="_blank">
                                      <button class="btn btn-info btn-xs ">
                                          <i class="fa fa-floppy-o fa-fw"></i>
                                      </button>
                                  </a>
                                  <span>{{ $v->user->name }}</span>
                                  <span>{{substr($v->created_at, 0, -3)}}</span>
                              </div>
                          </div>
                      </div>
                    <?php }?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

    @section('headerJs')
        @parent
        <script>
            $('#download-photos').on('click', function(){
                var $photos = $('input[name="downloads[]"]:checked');
                if( $photos.length > 0)
                {
                    var $ajaxData = { };
                    $photos.each(function(){
                        $ajaxData[$(this).val()]= $(this).val();
                    });

                    var str = jQuery.param( $ajaxData );

                    window.location.href = "/files/download-photos/{{ $injury->id }}?" + str;
                }else{
                    alert('Nie wybrano zdjęć do pobrania.');
                }
            });
        </script>
    @endsection
@endif
