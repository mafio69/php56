<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja oznaczenia na mapie dla grupy warsztatów</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('companies/update-group', [$group->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <div class="btn-group" data-toggle="buttons">
            @foreach($markers as $marker)
              <label class="btn btn-default @if($marker==$group->marker) active @endif">
                <input type="radio" autocomplete="off" name="marker" value="{{$marker}}" @if($marker==$group->marker) checked @endif>   <img src="/images/markers/{{$marker}}.png">
              </label>
            @endforeach
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Zapisz</button>
</div>
