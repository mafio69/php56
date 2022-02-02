<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie właściciela do użytkownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/append-contractors', [$user->id]) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-3">
                            <div class="input-group marg-top-min">
                              <span class="input-group-addon">
                                <input type="checkbox" value="1" name="without_restrictions"
                                       @if($user->without_restrictions == 1) checked="checked" @endif
                                >
                              </span>
                                <span class="form-control">
                                    Bez ograniczeń
                                </span>
                            </div>
                        </div>
                    </div>


                    <hr>

                    <div class="row">
                        @foreach($owners as $owner)
                            <div class="col-lg-6">
                                <div class="input-group marg-top-min @if($owner->active != 0) has-error @endif" style="overflow: hidden;" data-toggle="tooltip" data-placement="top" title="{{ $owner->post }} {{ $owner->city }}, {{ $owner->street }}">
                                      <label class="input-group-addon">
                                        <input type="checkbox" value="{{ $owner->id }}" name="owners[]"
                                           @if($user->owners->contains($owner->id)) checked @endif
                                        >
                                      </label>
                                    <span class="form-control" >
                                        {{ $owner->name }}
                                        @if($owner->old_name)
                                            ({{ $owner->old_name }})
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{ Form::token() }}
            </fieldset>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" data-loading-text="Zapisywanie..."  id="set">Zapisz zmiany</button>
</div>

<script>
    $('input[name="without_restrictions"]').on('change', function(){
        if($(this).prop('checked'))
        {
            $('input[name="owners[]"]').each(function(){
                $(this).prop('disabled', 'disabled');
                $(this).removeAttr('checked');
            });
        }else{
            $('input[name="owners[]"]').each(function(){
                $(this).removeAttr('disabled');
            });
        }
    }).change();

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
