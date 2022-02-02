<div class="pull-left">
    <ul  class="nav nav-pills nav-injuries btn-sm">
        <li class="<?php if(Request::segment(3) == 'unprocessed') echo 'active'; ?> ">
            <a href="{{ URL::route('routes.get', ['injuries', 'letters', 'unprocessed']) }}">Nieprzetworzone</a>
        </li>
        
        <li class="<?php if(Request::segment(3) == 'non-appended') echo 'active'; ?> ">
            <a href="{{{ URL::route('routes.get', ['injuries', 'letters', 'non-appended'] ) }}}">Nieprzypisane</a>
        </li>

        <li class="<?php if(Request::segment(3) == 'appended') echo 'active'; ?> ">
            <a href="{{{ URL::route('routes.get', ['injuries', 'letters', 'appended'] ) }}}" >Przypisane</a>
        </li>
    </ul>
</div>
<div class="pull-right" >
    {{ Form::open(array('url' => URL::route('routes.get', ['injuries', 'letters', Request::segment(3)] ) , 'method' => 'get', 'id' => 'search-form', 'class' => 'allow-confirm form-inline' )) }}
        <div class="input-group pull-right" style="width: 300px;">
            <input type="text" name="term" value="{{ Input::get('term') }}" id="search_garage" class="form-control" placeholder="nr szkody/nr umowy/rejestracja...">
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit" id="search_btn"><i class="fa fa-search"></i></button>
            </span>
        </div>
        <div class="form-group pull-right" >
            <label for="document_type_id">Typ dokumentu: </label>
            <select class="form-control" name="document_type_id">
                <option @if(! Input::has('document_type_id') ) selected @endif value="0">--- wybierz ---</option>
                @foreach($uploadedDocumentTypes as $uploadedDocumentType)
                    @if($uploadedDocumentType->subtypes->count() == 0)
                        <option @if(Input::has('document_type_id') && Input::get('document_type_id') == $uploadedDocumentType->id) selected @endif value="{{ $uploadedDocumentType->id }}">{{ $uploadedDocumentType->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </form>
</div>


@section('headerJs')
    @parent
    <script>
        $(document).ready(function() {

            $('#search-form').on('change', 'select[name="document_type_id"]', function(){
                $('#search-form').submit();
            });
        });
    </script>
@stop
