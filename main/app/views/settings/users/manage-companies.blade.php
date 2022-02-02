<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Przypisywanie firm do użytkownika</h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form action="{{ URL::to('settings/users/append-companies', [$user->id]) }}" method="post" id="dialog-form">
            <fieldset>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-3">
                            <div class="input-group marg-top-min">
                              <span class="input-group-addon">
                                <input type="checkbox" value="1" name="without_restrictions_vmanage"
                                       @if($user->without_restrictions_vmanage == 1) checked="checked" @endif
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
                        @foreach($companies as $company)
                            <div class="col-lg-6">
                                <div class="input-group marg-top-min" style="overflow: hidden;" data-toggle="tooltip" data-placement="top" title="{{ $company->post }} {{ $company->city }}, {{ $company->street }}">
                                      <label class="input-group-addon">
                                        <input type="checkbox" value="{{ $company->id }}" name="companies[]"
                                           @if($user->vmanage_companies->contains($company->id)) checked @endif
                                        >
                                      </label>
                                    <span class="form-control" >
                                        {{ $company->name }}
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
            $('input[name="companies[]"]').each(function(){
                $(this).prop('disabled', 'disabled');
                $(this).removeAttr('checked');
            });
        }else{
            $('input[name="companies[]"]').each(function(){
                $(this).removeAttr('disabled');
            });
        }
    }).change();

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
