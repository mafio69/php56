@extends('layouts.main')


@section('header')

Baza firm

<div class="pull-right"><a href="{{ URL::action('VmanageCompaniesController@getCreate') }}" class="btn btn-small btn-primary">
		<i class="fa fa-plus fa-fw"></i> Dodaj firmę</a>
</div>
@stop

@section('main')
	<div class="table-responsive">
		<table class="table table-hover table-condensed">
			<thead>
					<th>lp.</th>
					<th>nazwa firmy</th>
					<th>nip</th>
					<th>regon</th>
					<th>adres</th>
					<Th>pojazdy < 3.5t</Th>
					<Th>pojazdy > 3.5t</Th>
					<th>opiekunowie floty</th>
					<th ></th>
			</thead>
			<?php
				$lp = (($companies->getCurrentPage()-1)*$companies->getPerPage()) + 1;
				foreach ($companies as $company)
				{ ?>
				<tr class="vertical-middle">
					<td width="20px">{{$lp++}}.</td>
					<Td>
						{{ $company->owner->name }} <br>
						<i><small><strong>{{ $company->name}}</strong></small></i>
					</td>
					<td>
						{{ (isset($ownersInfo[$company->owner_id]) && isset($ownersInfo[$company->owner_id][8])) ? $ownersInfo[$company->owner_id][8] : '' }} <br>
						<i><small><strong>{{ $company->nip }}</strong></small></i>
					</td>
					<td>
						{{ (isset($ownersInfo[$company->owner_id]) && isset($ownersInfo[$company->owner_id][15])) ? $ownersInfo[$company->owner_id][15] : '' }} <br>
						<i><small><strong>{{ valueIfNotNull($company->regon) }}</strong></small></i>
					</td>
					<td>
						{{ $company->owner->post }} {{ $company->owner->city }}, {{ $company->owner->street }} <br>
						<i><small><strong>{{ $company->post }} {{ $company->city }}, {{ $company->street }}</strong></small></i>
					</td>
					<td>
						<a href="{{ URL::action('VmanageVehiclesController@getIndex', array($company->id, 0)) }}"  class="btn btn-primary btn-sm">
							<span class="marg-right">
								<i class="fa fa-car fa-fw"></i> pojazdy < 3.5t
							</span>
							<span class="vehicles_badge badge pull-right" data-company="{{ $company->id }}"></span>
						</a>
					</td>
					<td>
						<a href="{{ URL::action('VmanageVehiclesController@getIndex', array($company->id, 1)) }}" class="btn btn-primary btn-sm">
							<span class="marg-right">
								<i class="fa fa-truck fa-fw"></i> pojazdy > 3.5
							</span>
							<span class="trucks_badge badge pull-right" data-company="{{ $company->id }}"></span>
						</a>
					</td>
                    <td>
						<a href="{{ URL::action('VmanageCompanyGuardiansController@getIndex', [$company->id]) }}" class="btn btn-primary btn-sm">
							<i class="fa fa-users"></i> opiekunowie <span class="badge marg-left">{{ $company->guardians->count() }}</span>
						</a>
                    </td>
					<Td>
						<a href="{{ URL::action('VmanageCompaniesController@getCsm', [$company->id]) }}" class="btn btn-sm btn-info">
							<i class="fa fa-info-circle"></i> Info Flota
						</a>
					</Td>
					<td>
					    <a href="{{ URL::action('VmanageCompaniesController@getEdit', [$company->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> edytuj</a>
					    <button target="{{ URL::action('VmanageCompaniesController@getDelete', [$company->id]) }}" class="btn btn-sm btn-danger modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-trash-o"></i> usuń</button>
					</td>
					<td>
						<div class="btn-group">
					  <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					   exportuj pojazdy <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu">
					    <li>
							<a href="{{ URL::action('VmanageCompaniesController@getExportVehicles', [$company->id, 0]) }}">
								<i class="fa fa-file-excel-o fa-fw"></i> < 3.5t xls
							</a>
						</li>
						<li>
							 <a href="{{ URL::action('VmanageCompaniesController@getExportVehiclesCsv', [$company->id, 0]) }}">
								 <i class="fa fa-file-o fa-fw"></i> < 3.5t csv
							 </a>
						 </li>
						  <li>
							  <a href="{{ URL::action('VmanageCompaniesController@getExportVehicles', [$company->id, 1]) }}">
								  <i class="fa fa-file-excel-o fa-fw"></i> > 3.5t xls
							  </a>
						  </li>
						  <li>
							  <a href="{{ URL::action('VmanageCompaniesController@getExportVehiclesCsv', [$company->id, 1]) }}">
								  <i class="fa fa-file-o fa-fw"></i> > 3.5t csv
							  </a>
						  </li>
					  </ul>
					</div>
					</td>
				</tr>
				<?php }
			 ?>


		</table>
		<div class="pull-right" style="clear:both;">{{ $companies->links() }}</div>
	</div>


	{{ Form::token() }}
@stop

@section('headerJs')
	@parent
	<script>
        function loadCounter(section) {
            $.ajax({
                url: "/vehicle-manage/companies/load-counter",
                data: {section: section, _token : $('input[name=_token]').val()},
                dataType: "json",
                type: "POST",
                success: function (data) {
                    $.each( data, function( company_id, value ) {
                        $('.' + section + '_badge[data-company="'+company_id+'"]').text(value);
                    });
                }
            });
        }
		$(document).ready(function(){
			loadCounter('vehicles');
            loadCounter('trucks');
		});
	</script>
@endsection
