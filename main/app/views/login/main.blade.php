@extends('layouts.base')

@section('content')
    @include('modules.flash_notification')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel panel-default">
                    @if (Session::has('status'))
                        <div class="alert alert-{{Session::get('status')['type']}}">
                            {{ Session::get('status')['text'] }}
                        </div>
                    @endif
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-8 col-lg-offset-2 text-center marg-btm">
                                {{ HTML::image('images/cas.jpg', 'Logo', ['style' => 'max-width:100%;']) }}
                            </div>
                            <div class="col-sm-12 marg-btm">
                            <form action="{{ URL::route('login-post') }}" method="post">

                                <fieldset>
                                    <div class="form-group">
                                        {{ Form::text('login', '', array('class' => 'form-control', 'placeholder' => 'login', 'autofocuse' => ''))  }}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::password('password',  array('class' => 'form-control', 'placeholder' => 'hasło'))  }}
                                    </div>
                                    {{ Form::submit('Zaloguj się',  array('class' => 'btn btn-lg btn-primary btn-block'))  }}
                                    {{ Form::token() }}
                                </fieldset>
                            </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <div class="jumbotron">
                    <div class="media">
                        <div class="media-left">
                            <img class="media-object" alt="64x64" src="/images/icons8-email-100-1.png" style="width: 64px; height: 64px;">
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Aplikacja {{ Config::get('webconfig.APP_NAME') }}</h4>
                            W razie problemów z logowaniem <br>
                            prosimy o kontakt na <a href="mailto:pomoc-it@cas-auto.pl" style="text-decoration: none; font-weight: bold;">pomoc-it@cas-auto.pl</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('headerJs')
    @parent
    <script type="text/javascript">
        $(function() {
            window.localStorage.clear();
            $('form').each(function() {
                $(this).find('input').keypress(function(e) {
                    if(e.which == 10 || e.which == 13) {
                        this.form.submit();
                    }
                });
            });
        });
    </script>
@endsection
