@extends('layouts.main')


@section('header')

Edycja dnaych oddziału <i>{{ $office->name }}</i>

@stop

@section('main')
{{ Form::open(array('url' => URL::route('idea.offices.update', array($office->id)), 'method' => 'put')) }}
<div class="row marg-btm">
    <div class="pull-right">
        <a href="{{{ URL::previous() }}}" class="btn btn-default">Anuluj</a>
        {{ Form::submit('Zapisz zmiany',  array('id' => 'btnSubmit', 'class' => 'btn btn-primary'))  }}
    </div>
</div>
<div class="row">
    <div class="col-lg-8 col-lg-offset-2">


            @if($errors->any())
            <div class="row">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ implode('', $errors->all('<li class="error">:message</li>'))}}
                </div>
            </div>
            @endif

            <div class="row">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 marg-btm">
                            <label for="name">Nazwa oddziału:</label>
                            {{ Form::text('name', $office->name , array('class' => 'form-control required', 'placeholder' => 'nazwa oddziału'))  }}
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <label >Adres:</label>
                    <div class="row">
                        <div class="col-md-4 col-lg-4 marg-btm">
                        {{ Form::text('city', $office->city , array('class' => 'form-control  required', 'placeholder' => 'miasto'))  }}
                        </div>
                        <div class="col-md-4 col-lg-4 marg-btm">
                        {{ Form::text('post', $office->post , array('class' => 'form-control  required', 'placeholder' => 'kod pocztowy'))  }}
                        </div>
                        <div class="col-md-4 col-lg-4 marg-btm">
                        {{ Form::text('street', $office->street , array('class' => 'form-control  required', 'placeholder' => 'ulica'))  }}
                        </div>
                    </div>
                    <label for="name">Telefon:</label>
                    <div class="row">
                        <div class="col-sm-12 col-md-4 marg-btm">
                            {{ Form::text('phone', $office->phone , array('class' => 'form-control', 'placeholder' => 'telefon kontaktowy'))  }}
                        </div>
                    </div>
                </div>
            </div>
</div>
</div>
</form>


@stop

@section('headerJs')
	@parent

	<script type="text/javascript" >
		$(document).ready(function(){
			$("form").submit(function(e) {
			     var self = this;
			     e.preventDefault();

			     btn = $('#btnSubmit');
                 btn.attr('disabled', 'disabled');

			     if($("form").valid()){
			     	self.submit();
			     }else
                    btn.removeAttr('disabled');

			     return false; //is superfluous, but I put it here as a fallback
			});
		});
	</script>
@stop

