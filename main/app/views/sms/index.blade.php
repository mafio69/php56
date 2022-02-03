@extends('layouts.main')

@section('header')

Bramka SMS

@stop

@section('main')



<div class="row">
    <div class="col-xs-12 col-sm-10 col-md-8  col-xs-offset-0 col-sm-offset-1 col-md-offset-2 ">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Tworzenie wiadomości SMS</h3>
            </div>
            <div class="panel-body">
                <form action="{{ URL::route('sms.send') }}" method="post" id="form"   role="form">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="input-group input-group-sm form-sms marg-btm">
                                <span class="input-group-addon ">Numer telefonu odbiorcy:</span>
                                <input type="text" name="phone_number" required class="form-control required number" placeholder="wpisz numer telefonu">
                            </div>

                            <div class="input-group input-group-sm form-sms marg-btm">
                                <span class="input-group-addon ">Szablon wiadomości:</span>
                                <select class="form-control" id="template" >
                                    <option value="">---</option>
                                    @foreach($templates as $template)
                                    <option value="{{ $template->body }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group marg-btm">
                                <textarea class="form-control required" placeholder="treść wiadomości" required autofocuse="" name="bodySMS" id="bodySMS" cols="50" rows="10"></textarea>
                            </div>

                            <div class="form-group marg-btm">
                                <label>Podpis: </label> <span id="podpisSMS">Pozdrawiam {{ Auth::user()->name }}</span>
                            </div>
                            <div class="form-group marg-btm">
                                <label>Ilość znaków: </label> <span id="iloscZnakow">0</span><br>
                                <label>Liczba wiadomości: </label> <span id="iloscWiadomosci">0</span>
                                {{ Form::submit(' wyślij wiadomość ',  array('class' => 'btn btn-primary pull-right', 'id' => 'submit'))  }}
                            </div>
                        </div>
                    </div>
                    {{Form::token()}}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@stop


@section('headerJs')
@parent
<script type="text/javascript">

    function countChar(){

        var $maxsms = 918;
        $ograniczenie = new Array(161,307,460,613,766,918); //wartosc musi byc +1

        var $podpis = $("#podpisSMS").html().length;

        var $iloscznakow = $podpis + $("#bodySMS").val().length + 1;

        for(var i = 0; i<6 ; i++)
            if($iloscznakow<$ograniczenie[i])
                break;

        $("#iloscWiadomosci").html( i+1 );
        $("#iloscZnakow").html($iloscznakow + ' / ' + $maxsms );

        if($iloscznakow > $maxsms){
            alert('Wiadomość nie może zawierać więcej niż '+$maxsms+' znaków');
            $("#bodySMS").attr('value', $("#bodySMS").val().substr(0,($maxsms -$podpis)));
        }
    }

    $(document).ready(function() {
        countChar();

        $("#bodySMS").on("keyup", function() {
            countChar();
        });

        $('#template').on('change',function(){
           $('#bodySMS').html($(this).val());
        });

    });
</script>

@stop