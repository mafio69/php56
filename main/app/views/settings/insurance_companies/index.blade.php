@extends('layouts.main')


@section('header')
	
Lista ubezpieczalni

<div class="pull-right">
	<button target="{{{ URL::route('insurance_companies-create') }}}" class="btn btn-small btn-primary modal-open" data-toggle="modal" data-target="#modal"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj ubezpieczalnię</button>
</div>
	
@stop

@section('main')

	<div class="table-responsive">
		
		<table class="table table-condensed table-hover" id="users-table">
			<thead>
				<th>lp.</th>
				<th>nazwa</th>
				<th>adres</th>
				<th>osoba kontaktow</th>
				<th>email</th>
				<th>telefon</th>
				<th>zaokrąglaj kwoty</th>
				<th>składki do pełnych lat</th>
				<Th>nadrzędne TU</Th>
				<Th></Th>
				<th ></th>
				<th ></th>
			</thead>

			<?php $lp = (($insurance_companies->getCurrentPage()-1)*$insurance_companies->getPerPage()) + 1;?>
			@foreach ($insurance_companies as $company)
				<tr >
					<td width="20px">{{$lp++}}.</td>
					<Td>{{$company->name}}</td>
					<td>{{$company->post}} {{$company->city}}, {{$company->street}}</td>
					<td>{{$company->contact_person}}</td>
					<td>{{$company->email}}</td>
					<Td>{{$company->phone}}</td>
					<td>
						@if($company->if_rounding == 1)
							<i class="fa fa-check"></i>
						@else
							<i class="fa fa-minus"></i>
						@endif
					</td>
					<td>
						@if($company->if_full_year == 1)
							<i class="fa fa-check"></i>
						@else
							<i class="fa fa-minus"></i>
						@endif
					</td>
					<td>
						@if($company->parent)
							<span class="label label-primary system-warning" 
							data-toggle="tooltip" data-placement="top"
							title="{{$company->parent->name."\n".$company->parent->post."\t".$company->parent->city."\n".
							$company->parent->street}}
							">
								<i class="fa fa-info"></i>
							</span>
						@endif
					</td>
					<td>
						<button target="{{ URL::route('insurance_companies-edit', array($company->id)) }}" class="btn btn-sm btn-warning modal-open" data-toggle="modal" data-target="#modal">edytuj</button>
					</td>
					<td>
						<button target="{{ URL::route('insurance_companies-delete', array($company->id)) }}" class="btn btn-sm btn-danger modal-open" data-toggle="modal" data-target="#modal">usuń</button>
					</td>

					<td>
						<button target="{{ URL::route('insurance_companies-set-parent', array($company->id)) }}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal">sparuj</button>
					</td>
				</tr>
				<?php $lp2 = 1?>
					@foreach($company->children as $company)
						<tr style="background-color: #ecf4fc; font-size: 11px">
							<td style="padding-left: 20px" width="20px">{{$lp2++}}.</td>
							<Td>{{$company->name}}</td>
							<td>{{$company->post}} {{$company->city}}, {{$company->street}}</td>
							<td>{{$company->contact_person}}</td>
							<td>{{$company->email}}</td>
							<Td>{{$company->phone}}</td>
							<td>
								@if($company->if_rounding == 1)
									<i class="fa fa-check"></i>
								@else
									<i class="fa fa-minus"></i>
								@endif
							</td>
							<td>
								@if($company->if_full_year == 1)
									<i class="fa fa-check"></i>
								@else
									<i class="fa fa-minus"></i>
								@endif
							</td>
							<td>
								@if($company->parent)
									<span class="label label-primary" 
									data-toggle="tooltip" data-placement="top"
									title="{{$company->parent->name."\n".$company->parent->post."\t".$company->parent->city."\n".
									$company->parent->street}}
									">
										<i class="fa fa-info"></i>
									</span>
								@endif
							</td>
							<td>
								<button target="{{ URL::route('insurance_companies-edit', array($company->id)) }}" class="btn btn-xs btn-warning modal-open" data-toggle="modal" data-target="#modal">edytuj</button>
							</td>
							<td>
								<button target="{{ URL::route('insurance_companies-delete', array($company->id)) }}" class="btn btn-xs btn-danger modal-open" data-toggle="modal" data-target="#modal">usuń</button>
							</td>

							<td>
								<button target="{{ URL::route('insurance_companies-set-parent', array($company->id)) }}" class="btn btn-xs btn-primary modal-open" data-toggle="modal" data-target="#modal">sparuj</button>
							</td>
						</tr>
						@endforeach
			@endforeach
		</table>
		<div class="pull-right" style="clear:both;">{{ $insurance_companies->links() }}</div>
	</div>



@stop

@section('headerJs')
	@parent
	<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
	</script>
@stop
