<div class="tab-pane fade in " id="photos">
        <div class="row">
          <div class="col-sm-12">
            <div class="panel panel-default">
              <div class="row">
              @if(Auth::user()->can('zlecenia#zarzadzaj'))
                    <div class="col-sm-12">
                    {{ Form::open( [ 'url' => URL::route('dos.other.injuries.post-image', array($injury->id, 1)) , 'class' => 'dropzone imageUploads' , 'id' => 'imgBefore', 'files'=>true ] ) }}
                      <div class="fallback">
                          <input name="file" type="file" multiple />
                      </div>
                    {{ Form::close() }}
                    </div>
              @endif
              </div>
              <div class="row marg-top-min">
                <div class="col-sm-12">
                @foreach ($imagesBefore as $k => $v)
                  <div class="col-sm-4 col-md-2" id="image-{{$v->id}}">
                      <div class="thumbnail">
                          <div class="image-container">
                              <a href="/file/uploads/images/{{$v->file}}/full" data-lightbox="image-before" >
                                {{ HTML::image('/file/uploads/images/'.$v->file.'/thumb') }}
                              </a>
                          </div>
                          <div class="caption">
                              @if(Auth::user()->can('zlecenia#zarzadzaj'))
                                <button type="button" class="btn btn-danger btn-xs modal-open-sm" target="{{ URL::route('dos.other.injuries.getDelImage', array($v->id)) }}" data-toggle="modal" data-target="#modal-sm">usuń</button>
                              @endif
                              <span>{{ $v->user->name }}</span>
                              <span>{{substr($v->created_at, 0, -3)}}</span>
                          </div>
                      </div>
                  </div>
                @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
