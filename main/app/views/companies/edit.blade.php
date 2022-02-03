@extends('layouts.main')


@section('header')

Edycja siedziby - {{$company['name']}}

@stop

@section('main')
<form action="{{ URL::to('companies/update', array($company->id) ) }}" method="post" role="form">
	<div class="row marg-btm">
		<div class="pull-right">
			<a href="{{{ URL::previous() }}}" class="btn btn-default">Anuluj</a>
			{{ Form::submit('Zapisz zmiany',  array('class' => 'btn btn-primary'))  }}
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
		    {{ Form::text('name',  $company->name, array('class' => 'form-control required', 'placeholder' => 'nazwa firmy', 'autofocuse' => ''))  }}

	  	</div>
	  	<div class="form-group">
		    <label >Adres:</label>
		    <div class="row">
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('city', $company->city, array('class' => 'form-control required ', 'placeholder' => 'miasto'))  }}
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('code', $company->code, array('class' => 'form-control required ', 'placeholder' => 'kod pocztowy'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('street', $company->street, array('class' => 'form-control required ', 'placeholder' => 'ulica'))  }}
			    </div>
			</div>
	  	</div>
	  	<div class="form-group">
		    <label >Dane firmy:</label>
		    <div class="row">
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('nip', $company->nip, array('class' => 'form-control  tips', 'placeholder' => 'nip' , 'title' => 'nip'))  }}
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('krs', $company->krs, array('class' => 'form-control  tips', 'placeholder' => 'krs', 'title' => 'krs'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('regon', $company->regon, array('class' => 'form-control tips', 'placeholder' => 'regon', 'title' => 'regon'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('account_nr', (array_intersect([1, 5], $company->groups()->lists('company_group_id'))) ? null : $company->account_nr, array('class' => 'form-control  tips', 'placeholder' => 'nr konta', 'title' => 'nr konta'))  }}
			    </div>
			</div>

	  	</div>

	  	<div class="form-group">
		    <label >Dane kontaktowe firmy:</label>

	  		<div class="row">
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('www', $company->www, array('class' => 'form-control  ', 'placeholder' => 'www'))  }}
				</div>
				<div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('email', $company->email, array('class' => 'form-control   ', 'placeholder' => 'email'))  }}
			    </div>
			    <div class="col-md-4 col-lg-4 marg-btm">
			    {{ Form::text('phone', $company->phone, array('class' => 'form-control  ', 'placeholder' => 'telefon'))  }}
			    </div>
			</div>
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-md-6 col-lg-6">
                    <label >Adnotacje:</label>
                    {{ Form::textarea('remarks', $company->remarks, array('class' => 'form-control  ', 'placeholder' => 'adnotacje'))  }}
				</div>
                <div class="col-md-6 col-lg-6">
                    <label>Dane rejestrowe serwisu do CESJI:</label>
                    {{ Form::textarea('service_cession_data', $company->service_cession_data, array('class' => 'form-control  ', 'placeholder' => 'Dane rejestrowe serwisu do CESJI'))  }}
                </div>
            </div>
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-sm-12 col-lg-8 col-lg-offset-2">
					<label>Grupa kontrahenta:</label>
					{{ Form::select('contractor_group_id', $contractorGroups, $company->contractor_group_id, ['class' => 'form-control']) }}
				</div>
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				Przypisane grupy
			</div>
			<div class="panel-body">
				@foreach($groups as $group_id => $group_name)
					<div class="col-sm-6 col-md-4">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="groups[]" value="{{ $group_id }}" @if($company->groups->contains($group_id)) checked @endif>
								{{ $group_name }}
							</label>
						</div>
					</div>
				@endforeach
			</div>
		</div>
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
			     if($("form").valid()){
			     	self.submit();
			     }
			     return false; //is superfluous, but I put it here as a fallback
			});
		});
	</script>

@stop

