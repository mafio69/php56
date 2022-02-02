@extends('layouts.main')

@section('header')

Edycja danych firmy <i>{{ $company->owner->name }}</i> <small><i>( {{ $company->name }} )</i></small>

@stop

@section('main')

@include('modules.flash_notification')

{{ Form::open(array('url' => URL::action('VmanageCompaniesController@postUpdate', [$company->id]))) }}
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
                        {{ Form::text('name', $company->name, array('id'=>'name', 'class' => 'form-control tips required', 'required', 'placeholder' => 'nazwa firmy', 'title' => 'nazwa firmy'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('nip', $company->nip, array('id'=>'nip', 'class' => 'form-control tips required', 'required' , 'placeholder' => 'NIP', 'title' => 'NIP'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('regon', $company->regon, array('id'=>'regon', 'class' => 'form-control tips ', 'placeholder' => 'regon', 'title' => 'regon'))  }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h4 class="inline-header"><span>Dane kontaktowe:</span></h4>
            <div class="row">
                <div class="col-md-4 col-lg-3  marg-btm">
                    <div class="form-group">
                        {{ Form::text('post', $company->post, array('id'=>'post', 'class' => 'form-control tips', 'placeholder' => 'kod pocztowy', 'title' => 'kod pocztowy'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('city', $company->city, array('id'=>'city', 'class' => 'form-control tips ', 'placeholder' => 'miasto', 'title' => 'miasto'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('street', $company->street, array('id'=>'street', 'class' => 'form-control tips ', 'placeholder' => 'ulica', 'title' => 'ulica'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('phone', $company->phone, array('id'=>'phone', 'class' => 'form-control tips ', 'placeholder' => 'telefon', 'title' => 'telefon'))  }}
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 marg-btm">
                    <div class="form-group">
                        {{ Form::text('mail', $company->mail, array('id'=>'mail', 'class' => 'form-control tips email', 'placeholder' => 'adres email', 'title' => 'adres email'))  }}
                    </div>
                </div>
            </div>
        </div>
	</div>
    <div class="row marg-btm">
        <h4 class="inline-header "></h4>
        <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">

            {{ Form::submit('Zapisz zmiany',  array('id'=>'store', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }}
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
                btn = $('#store');
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

