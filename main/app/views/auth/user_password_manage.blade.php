@extends('layouts.main')

@section('header')
  Zmiana hasła
@endsection
@section('main')
    <div class="row marg-top">
      <form action="{{ url('password')}}" method="post">
          <div class="form-group col-sm-6 col-sm-offset-3">
              <div class="row">
                  @if (Session::has('status'))
                      <div class="alert alert-{{Session::get('status')['type']}}">
                          {{ Session::get('status')['text'] }}
                      </div>
                  @endif
                  <div class="col-sm-12 marg-btm">
                    <div class="well">
                      @if(\Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)>=0)
                        Twoje hasło musi być zmienione za {{\Carbon\Carbon::now()->diffInDays($user->password_expired_at)}} dni
                      @else
                        Twoje hasło wygasło {{\Carbon\Carbon::now()->diffInDays($user->password_expired_at)}} dni temu, konieczna jest zmiana hasła
                      @endif
                    </div>
                  </div>
                  <div class="col-sm-12 marg-btm">
                    <p class="small">
                      Wprowadź hasło składające się małych i wielkich liter, liczb i znaków specjalnych.
                    </p>
                  </div>
                  <div class="col-sm-12 marg-btm">
                    <div class="progress">
                      <div class="progress-bar progress-bar-success" role="progressbar"  style="width:0%" id="progress">
                        <span class="text"></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12 marg-btm">
                      <label >Nowe hasło:</label>
                      {{ Form::password('password', array('class' => 'form-control required', 'placeholder' => 'nowe hasło', 'required')) }}
                  </div>
                  <div class="col-sm-12 marg-btm">
                      <label >Powtórz hasło:</label>
                      {{ Form::password('password_confirmation',  array('class' => 'form-control required',  'placeholder' => 'powtórz hasło', 'required'))}}
                  </div>
                  {{Form::token()}}
              </div>
              <button class="btn btn-primary">Zapisz</button>
          </div>
      </form>
    </div>
@endsection
@section('headerJs')
    @parent
    <script>
$("[name='password']").keyup(function(){
  var val = $(this).val();
  if(val.length<8){
    $('#progress').removeClass().addClass('progress-bar progress-bar-danger');
    $('#progress').css('width','30%');
    $('#progress .text').text('Hasło słabe, zbyt mało znaków');
  }
  else{
    if(!(new RegExp('[a-z]')).test(val)){
      $('#progress').removeClass().addClass('progress-bar progress-bar-warning');
      $('#progress').css('width','60%');
      $('#progress .text').text('Hasło średnie, brak małych liter');
      return false;
    }
    if(!(new RegExp('[A-Z]')).test(val)){
      $('#progress').removeClass().addClass('progress-bar progress-bar-warning');
      $('#progress').css('width','60%');
      $('#progress .text').text('Hasło średnie, brak wielkich liter');
      return false;
    }
    if(!(new RegExp('[0-9]')).test(val)){
      $('#progress').removeClass().addClass('progress-bar progress-bar-warning');
      $('#progress').css('width','60%');
      $('#progress .text').text('Hasło średnie, brak liczb');
      return false;
    }

    $('#progress').removeClass().addClass('progress-bar progress-bar-success');
    $('#progress').css('width','100%');
    $('#progress .text').text('Hasło silne');
  }
});
</script>
@endsection
