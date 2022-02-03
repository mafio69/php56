@extends('layouts.main')


@section('header')
Parowanie warsztatu  {{ $company->name }}
<div class="pull-right">
	<a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
</div>
@stop

@section('main')
<form action="{{ URL::to('companies/pair', [$company->id]) }}" method="post" role="form">
	<div class="row  col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group row">
					<div class="col-sm-12">
					<label for="name">Wyszukaj i wskaż warszat pod który ma być podpięty {{ $company->name }}:</label>
					</div>
					<div class="col-sm-12 ">
					{{ Form::text('search_company', '', array('class' => 'form-control required', 'id'=>'search_company', 'placeholder' => 'nazwa firmy', 'autofocus' => ''))  }}
					{{ Form::hidden('company_id') }}
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12  alert alert-danger" role="alert" id="search_alert" style="display: none;">Nie znaleziono pasującego opiekuna w bazie</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<hr>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 control-label text-right">Nazwa:</label>
					<div class="col-sm-9 col-md-8 col-lg-6">
						{{ Form::input('text', 'searched_name', null, ['id' => 'searched_name', 'class' => 'form-control ', 'readonly'])}}
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-md-2 col-lg-3 control-label text-right">Adres:</label>
					<div class="col-sm-9 col-md-8 col-lg-6">
						{{ Form::input('text', 'searched_address', null, ['id' => 'searched_address', 'class' => 'form-control ', 'readonly'])}}
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<hr>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-10 col-lg-8 col-lg-offset-2">
						<button type="submit" id="btnSubmit" class="btn btn-primary btn-sm btn-block">
							<i class="fa fa-floppy-o fw"></i> Zapisz
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>


	{{Form::token()}}
</form>



@stop

@section('headerJs')
	@parent

	<script type="text/javascript" >
		$(document).ready(function(){

			$('#search_company').autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: "{{ url('companies/search-company') }}",
						data: {
							term: request.term,
							company_id: "{{ $company->id }}",
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						dataType: "json",
						type: "POST",
						beforeSend: function(data){
							$('#search_alert').hide();
						},
						success: function( data ) {
							if (jQuery.isEmptyObject(data))
								$('#search_alert').show();

							response( $.map( data, function( item ) {
								return item;
							}));
						}
					});
				},
				minLength: 2,
				open: function(event, ui) {
					$(".ui-autocomplete").css("z-index", 1000);
				},
				select: function(event, ui) {
					if(ui.item.id != $('input[name="company_id"]').val())
					{
						$('input[name="company_id"]').val(ui.item.id);
						$('#searched_address').val(ui.item.address);
						$('#searched_name').val(ui.item.name);
					}
				}
			}).bind("keypress", function(e) {
				if(e.which == 13){
					setTimeout(function(){
						$('#search_company').focusout();
					},500);
				}else{
					$('input[name="company_id"]').val('');
					$('#searched_name').val('');
					$('#searched_address').val('');
					$('#search_alert').hide();
				}
			});

			$("form").submit(function(e) {
			     var self = this;
			     e.preventDefault();

			     var btn = $('#btnSubmit');
                 btn.attr('disabled', 'disabled');

			     if($("form").valid() && $('input[name="company_id"]').val()){
			     	self.submit();
			     }else
                    btn.removeAttr('disabled');

			     return false; //is superfluous, but I put it here as a fallback
			});
		});
	</script>
@stop

