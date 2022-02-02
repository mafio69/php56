<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Generowanie nowego hasła</h4>
</div>
<div class="modal-body" style="overflow:hidden;">

    <form action="{{ url('settings/users/generate', [ $user->id ])}}" method="post"  id="dialog-form">
        {{ Form::token() }}
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 marg-btm">
                  @if($user->email)
                    Czy chesz wygenerować nowe hasło dla {{$user->name}} i wysłać je na adres {{$user->email}}?
                  @else
                    Brak adresu email użytkownika, nie można wygenerować hasła.
                  @endif
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    @if($user->email)
      <button type="button" class="btn btn-primary" id="set" data-loading-text="trwa generowanie">Generuj</button>
    @endif
</div>
