@extends('layouts.main')

@section('header')

    Edycja danych leasingobiorcy {{ $client->name }}

    <div class="pull-right">
        <a href="{{{ URL::previous() }}}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">

        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/info-client/update', [$agreement_id]), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                    <div class="row marg-btm">
                        <div class="col-md-12 ">
                            <label>Nazwa:</label>
                            {{ Form::text('name', $client->name, array('class' => 'form-control required', 'placeholder' => 'nazwa'))  }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 ">
                            <label>NIP:</label>
                            {{ Form::text('NIP', $client->NIP, array('class' => 'form-control required', 'placeholder' => 'NIP', 'id'=>'NIP_to_check'))  }}
                        </div>
                        <div class="col-md-6 ">
                            <label>REGON:</label>
                            {{ Form::text('REGON', $client->REGON, array('class' => 'form-control ', 'placeholder' => 'REGON'))  }}
                        </div>
                        <div class="col-md-6 ">
                            <label>Kod klienta:</label>
                            {{ Form::text('firmID', $client->firmID, array('class' => 'form-control ', 'placeholder' => 'kod klienta'))  }}
                        </div>
                    </div>
                    <h4 class="inline-header"><span>Adres rejestrowy:</span></h4>
                    <div class="row marg-btm">
                        <div class="col-md-6 ">
                            <label>Kod pocztowy:</label>
                            {{ Form::text('registry_post', $client->registry_post, array('class' => 'form-control', 'placeholder' => 'Kod pocztowy'))  }}
                        </div>
                        <div class="col-md-6 ">
                            <label>Miasto:</label>
                            {{ Form::text('registry_city', $client->registry_city, array('class' => 'form-control', 'placeholder' => 'Miasto'))  }}
                        </div>
                    </div>
                    <div class="row marg-btm">
                        <div class="col-md-6 ">
                            <label>Ulica:</label>
                            {{ Form::text('registry_street', $client->registry_street, array('class' => 'form-control', 'placeholder' => 'Ulica'))  }}
                        </div>
                    </div>
                    <h4 class="inline-header"><span>Adres kontaktowy:</span></h4>
                    <div class="row marg-btm">
                        <div class="col-md-6 ">
                            <label>Kod pocztowy:</label>
                            {{ Form::text('correspond_post', $client->correspond_post, array('class' => 'form-control', 'placeholder' => 'Kod pocztowy'))  }}
                        </div>
                        <div class="col-md-6 ">
                            <label>Miasto:</label>
                            {{ Form::text('correspond_city', $client->correspond_city, array('class' => 'form-control', 'placeholder' => 'Miasto'))  }}
                        </div>
                    </div>
                    <div class="row marg-btm">
                        <div class="col-md-12 ">
                            <label>Ulica:</label>
                            {{ Form::text('correspond_street', $client->correspond_street, array('class' => 'form-control', 'placeholder' => 'Ulica'))  }}
                        </div>
                    </div>
                    <div class="row marg-btm">
                        <div class="col-md-6 ">
                            <label>Telefon:</label>
                            {{ Form::text('phone', $client->phone, array('class' => 'form-control', 'placeholder' => 'Telefon'))  }}
                        </div>
                        <div class="col-md-6 ">
                            <label>Email:</label>
                            {{ Form::text('email', $client->email, array('class' => 'form-control email', 'placeholder' => 'Email'))  }}
                        </div>
                    </div>
                    <h4 class="inline-header "></h4>
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Zapisz zmiany',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa zapisywanie zmian...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $( "form#page-form" ).submit(function(e) {
                var $btn = $('#form_submit').button('loading');
                if(! $('#page-form').valid()) {
                    e.preventDefault();
                    $btn.button('reset');
                    return false;
                }
                return true;
            });
        });
    </script>

@stop