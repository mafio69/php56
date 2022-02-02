@extends('layouts.main')


@section('header')
	
Lista marek samochodów

<div class="pull-right">
	<button target="{{{ URL::route('brands-create') }}}" class="btn btn-small btn-primary create" data-toggle="modal" data-target="#modal"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj markę</button>
</div>
	
@stop

@section('leftNav')
	@parent
	<div class="l-menu-content left-nav-element">
		<table id="l-menu-show" style="width: 26px;">
			<tr>
				<td>
					<?php $exist_filter = 0;?>

					@if($exist_filter == 0)
						<i class="fa fa-angle-right"></i>
					@endif
				</td>
			</tr>
		</table>
	</div>
@stop

@section('leftNavContent')
	@parent
	<form method="post" id="search-form" action="{{ URL::route('session.setSearch') }}" >
		{{Form::token()}}
		<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="l-menu">
			<div class="cbp-search-para">
				<label>Liczba wierszy na stronie:</label>
				<select class="form-control input-sm  search-el" id="s-pagin" name="pagin">
					<option value="10"
							@if(Session::get('search.pagin', '10') == 10)
							selected
							@endif
					>10</option>
					<option value="15"
							@if(Session::get('search.pagin', '10') == 15)
							selected
							@endif
					>15</option>
					<option value="20"
							@if(Session::get('search.pagin', '10') == 20)
							selected
							@endif
					>20</option>
					<option value="25"
							@if(Session::get('search.pagin', '10') == 25)
							selected
							@endif
					>25</option>
					<option value="30"
							@if(Session::get('search.pagin', '10') == 30)
							selected
							@endif
					>30</option>
					<option value="40"
							@if(Session::get('search.pagin', '10') == 40)
							selected
							@endif
					>40</option>
					<option value="50"
							@if(Session::get('search.pagin', '10') == 50)
							selected
							@endif
					>50</option>
				</select>
			</div>
		</nav>
	</form>
@stop

@section('main')

	<div class="row">
		<?php $queryString = http_build_query(Request::except('page'));?>
		<div class="col-sm-12">
			<nav class="navbar navbar-default navbar-sm marg-top-min" style="margin-bottom: 0px;">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed off-disable" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
						<form class="navbar-form navbar-left allow-confirm flex" role="search">
							<div class="form-group form-group-sm text-center" style="border-right: 1px solid #000; padding-right: 10px; margin-right:10px; width: 130px;">
								<label>Filtrowanie </label><br/>
								<button type="submit" class="btn btn-xs btn-primary">
									<i class="fa fa-search fa-fw"></i> filtruj <span class="badge">{{ $brands->getTotal() }}</span>
								</button><br />
								<a class="btn btn-xs btn-danger marg-top-min" href="{{ Request::url() }}">
									<i class="fa fa-remove fa-fw"></i> usuń filtry
								</a>
							</div>
							<div class="form-group form-group-sm">
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group form-group-sm ">
											<label>Typ:</label><br>
											<div class="input-group">
												<select class="form-control" name="filter_types">
													<option value="0" selected="selected">--- wszystkie ---</option>
													<option value="1" @if(Input::has('filter_types') && Input::get('filter_types') == 1) selected @endif>osobowe</option>
													<option value="2" @if(Input::has('filter_types') && Input::get('filter_types') == 2) selected @endif>ciężarowe</option>
												</select>
											</div>
										</div>
										<div class="form-group form-group-sm ">
											<label>Nazwa:</label><br>
											<input class="form-control" autocomplete="off" name="filter_name" type="text" value="{{ Input::has('filter_name')?Input::get('filter_name'):'' }}">
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</nav>
		</div>

		<div class="col-sm-12">
			<hr/>
		</div>
	</div>


	<div class="table-responsive">
		
		<table class="table  table-hover table-condensed" id="users-table" style="caption-top">

			<caption><h4>Marki</h4></caption>
			<thead>
					<th>lp.</th>
					<th >marka</th>
					<th>typ</th>
					<Th></Th>
					<th ></th>
					<Th></Th>
			</thead>
			<?php $lp = (($brands->getCurrentPage()-1)*$brands->getPerPage()) + 1;?>
			@foreach ($brands as $k => $brand)
				<tr >
					<td width="20px">{{$lp++}}.</td>
					<Td>{{$brand->name}}</td>
					<td><?php if($brand->typ == 1) echo 'osobowe'; else echo 'ciężarowe'; ?></td>
					<td>
						<a href="{{ URL::route('settings.brand.models', array($brand->id)) }}" style="width: 130px;" class="btn btn-primary btn-xs"><span class="pull-left"><i class="fa fa-car"></i> modele</span><span class="badge pull-right">{{ $brand->models->count() }}</span></a>
					</td>
					<td>
						<button target="{{ URL::route('brands-edit', array($brand->id)) }}" class="btn btn-warning btn-xs edit-brand" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil fa-fw"></i> edytuj</button>
					</td>
					<td>
						<button target="{{ URL::route('brands-delete', array($brand->id)) }}" class="btn btn-danger btn-xs delete-brand" data-toggle="modal" data-target="#modal"><i class="fa fa-trash-o fa-fw"></i> usuń</button>
						<a {{$brand->if_multibrand?'disabled':''}} data-brand={{$brand->id}} class="btn btn-success btn-xs multibrand">
							<i class="fa fa-trash-o fa-arrow-right"></i>
						</a>	
					</td>
				</tr>
			@endforeach
		</table>
		<div class="pull-right" style="clear:both;"><?php 
		Paginator::setPageName('brands');
		echo $brands->appends(Input::all())->links() ?> </div>
	</div>
	
	<div class="table-responsive"  style="margin-left: 20px">
		
		<table class="table  table-hover table-condensed" id="users-table" style="caption-top">
			<caption><h4>Wielomarkowe</h4></caption>
			<thead>
					<th>lp.</th>
					<th >marka</th>
					<th>typ</th>
					<Th></Th>
					<th ></th>
					<Th></Th>
			</thead>
			<?php $lp = (($brands_multibrands->getCurrentPage()-1)*$brands_multibrands->getPerPage()) + 1;?>
			@foreach ($brands_multibrands as $k => $brand)
				@if($brand->if_multibrand)
					<tr >
						<td width="20px">{{$lp++}}.</td>
						<Td>{{$brand->name}}</td>
						<td><?php if($brand->typ == 1) echo 'osobowe'; else echo 'ciężarowe'; ?></td>
						
						<td>
							<a data-brand={{$brand->id}} class="btn btn-danger btn-xs multibrand">
								<i class="fa fa-trash-o fa-arrow-left"></i>
							</a>	
						</td>
					</tr>
				@endif
			@endforeach
		</table>
		<div class="pull-right" style="clear:both;">
			<?php 
				Paginator::setPageName('brands_multibrands');
				echo $brands_multibrands->appends(Input::all())->links() ?> 
			</div>
		</div>

	@include('modules.modals')

@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {	       
	      	$('.table').on('click', '.edit-brand', function(){
		       	var hrf=$(this).attr('target');

				$.get( hrf, function( data ) {
				  $('#modal .modal-content').html(data);
				});
	       	});

	       	$('.table').on('click', '.delete-brand', function(){
		       	var hrf=$(this).attr('target');

				$.get( hrf, function( data ) {
				  $('#modal .modal-content').html(data);
				});
	       	});

			$('.create').click( function(){
		       	var hrf=$(this).attr('target');

				$.get( hrf, function( data ) {
				  $('#modal .modal-content').html(data);
				});
	       	});

	       	$('#modal').on('click', '#save-brand', function(){
	       		$('#edit-brand-form').validate();

                var btn = $(this);
                btn.attr('disabled', 'disabled');

	       		if($('#edit-brand-form').valid() ){	       			
					$.post(
			            $('#edit-brand-form').prop( 'action' ),
			            
			            $('#edit-brand-form').serialize()
			            ,
			            function( data ) {
			                if(data == '0') location.reload();
			                else{
			                	$('<label for="name" class="error">'+data+'</label>').insertAfter( "#modal .modal-body input[name='name']" );
			                	btn.removeAttr('disabled');
			                } 
			            },
			            'json'
			        );
					return false;
	       		}else{
	       		    btn.removeAttr('disabled');
	       		}

	       });

			$('.search-clear').on('click', function(){
				$('#search-adv-form input:checkbox, #search-adv-form input[name="search_term"]').each(function(){
					$(this).val('');
					$(this).removeAttr('checked');
				});
				$('#search-adv-form').submit();
			});
		});
		
		$('.multibrand').on('click', function (e) {
			e.preventDefault();
			brand = $(this).data('brand');
				$.ajax({
				url: "/brands/set-multibrand",
				data: {
					id: brand,
					_token: $('input[name="_token"]').val()
				},
				dataType: "json",
				type: "POST",
				success: function (data) {
					$.get( "/settings/brands", function( data ) {
						location.reload();
					});
				}
			});
		});
	    
    </script>
  
@stop

