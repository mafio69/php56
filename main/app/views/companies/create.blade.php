@extends('layouts.main')


@section('header')
Dodawanie nowej firmy
@stop

@section('main')
<form action="{{ URL::to('companies/store') }}" method="post" role="form">
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
			{{ Form::submit('Dodaj firmę',  array('id' => 'btnSubmit', 'class' => 'btn btn-primary'))  }}
		</div>
	</div>
	@if($errors->any())
		<div class="row">
			<div class="alert alert-danger">
				<a href="#" class="close" data-dismiss="alert">&times;</a>
				<li class="error">Firma o podanej nazwie istnieje już w systemie.</li>
			</div>
		</div>
	@endif
	<div class="row">
		<div class="form-group">
		    <label for="name">Nazwa firmy:</label>
		    {{ Form::text('name', '', array('class' => 'form-control required', 'placeholder' => 'nazwa firmy', 'autofocuse' => ''))  }}
	  	</div>
	  	<div class="form-group">
		    <label >Adres:</label>
		    <div class="row">
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('city', '', array('class' => 'form-control  required', 'placeholder' => 'miasto'))  }}
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('code', '', array('class' => 'form-control  required', 'placeholder' => 'kod pocztowy'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('street', '', array('class' => 'form-control  required', 'placeholder' => 'ulica'))  }}
			    </div>
			</div>
	  	</div>
	  	<div class="form-group">
		    <label >Dane firmy:</label>
		    <div class="row">
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('nip', '', array('class' => 'form-control tips', 'placeholder' => 'nip', 'title' => 'nip'))  }}
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('krs', '', array('class' => 'form-control  tips', 'placeholder' => 'krs', 'title' => 'krs'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('regon', '', array('class' => 'form-control tips', 'placeholder' => 'regon', 'title' => 'regon'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('account_nr', '', array('class' => 'form-control  tips', 'placeholder' => 'nr konta', 'title' => 'nr konta'))  }}
			    </div>
			</div>

	  	</div>

	  	<div class="form-group">
		    <label >Dane kontaktowe firmy:</label>

	  		<div class="row">
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('www', '', array('class' => 'form-control  ', 'placeholder' => 'www'))  }}
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('email', '', array('class' => 'form-control   ', 'placeholder' => 'email'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('phone', '', array('class' => 'form-control  ', 'placeholder' => 'telefon'))  }}
			    </div>
			</div>
		</div>

		<div class="form-group">
		    <label >Adnotacje:</label>
		    {{ Form::textarea('remarks', '', array('class' => 'form-control  ', 'placeholder' => 'adnotacje'))  }}
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12 col-lg-8 col-lg-offset-2">
					<label>Grupa kontrahenta:</label>
					{{ Form::select('contractor_group_id', $contractorGroups, null, ['class' => 'form-control']) }}
				</div>
			</div>
		</div>
		@if(Auth::user()->can('serwisy#dodaj_firme#przypisanie_grupy'))
			<div class="panel panel-primary">
				<div class="panel-heading">
					Przypisane grupy
				</div>
				<div class="panel-body">
					@foreach($groups as $group_id => $group_name)
						<div class="col-sm-6 col-md-4">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="groups[]" value="{{ $group_id }}">
									{{ $group_name }}
								</label>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		@endif
		 {{Form::token()}}
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

			     var btn = $('#btnSubmit');
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

