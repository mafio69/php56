@extends('layouts.main')

@section('header')

    Edycja danych nabywcy {{ $buyer->name }}

@stop

@section('main')

    {{ Form::open(array('url' => URL::to('injuries/buyers/update', [$buyer->id]))) }}
    <div class="row marg-btm">
        <div class="pull-right">
            <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <h4 class="inline-header"><span>Dane rejestrowe:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('name', $buyer->name, array('id'=>'name', 'class' => 'form-control tips required', 'required', 'placeholder' => 'nazwa firmy', 'title' => 'nazwa firmy'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('nip', $buyer->nip, array('id'=>'nip', 'class' => 'form-control tips', 'placeholder' => 'NIP', 'title' => 'NIP'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('regon', $buyer->regon, array('id'=>'regon', 'class' => 'form-control tips ', 'placeholder' => 'regon', 'title' => 'regon'))  }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Dane adresowe i kontaktowe:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('address_code', $buyer->address_code, array('id'=>'address_code', 'class' => 'form-control tips', 'placeholder' => 'kod pocztowy', 'title' => 'kod pocztowy'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('address_city', $buyer->address_city, array('id'=>'address_city', 'class' => 'form-control tips ', 'placeholder' => 'miasto', 'title' => 'miasto'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('address_street', $buyer->address_street, array('id'=>'address_street', 'class' => 'form-control tips ', 'placeholder' => 'ulica', 'title' => 'ulica'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('phone', $buyer->phone, array('id'=>'phone', 'class' => 'form-control tips ', 'placeholder' => 'telefon', 'title' => 'telefon'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('email', $buyer->email, array('id'=>'email', 'class' => 'form-control tips email', 'placeholder' => 'adres email', 'title' => 'adres email'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('contact_person', $buyer->contact_person, array('id'=>'contact_person', 'class' => 'form-control tips ', 'placeholder' => 'osoba kontaktowa', 'title' => 'osoba kontaktowa'))  }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row marg-btm">
        <h4 class="inline-header "></h4>
        <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">

            {{ Form::submit('Zapisz',  array('id'=>'store', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }}
        </div>
    </div>
    {{ Form::close() }}

@stop

@section('headerJs')
    @parent
    <script type="text/javascript" >
        $(document).ready(function(){
            $("form").submit(function(e) {
                var self = this;
                e.preventDefault();
                var btn = $('#store');
                btn.attr('disabled', 'disabled');

                if($("form").valid()){
                    self.submit();
                }else{
                    btn.removeAttr('disabled');
                }
                return false; //is superfluous, but I put it here as a fallback
            });
        });
    </script>

@stop

