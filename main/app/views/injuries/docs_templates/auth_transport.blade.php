<?php //upoważnienie do odbioru i transportu pojazdu ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="{{ url('templates-src/css/notification.css') }}" rel="stylesheet">
	<link href="{{ url('templates-src/css/notification-'.$documentTemplate->slug.'.css') }}" rel="stylesheet">
	<title></title>
</head>
<body>
<?php $vehicle = $injury->vehicle()->first();?>
	<div id="body">
		<div class="page"  id="content">
			<div style="font-size: 7pt;">
                @include('injuries.docs_templates.modules.place')

                @include('injuries.docs_templates.modules.branch')
			</div>

			<div style ="margin-top:50pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
				<p >
    {{ checkIfEmpty('1', $ideaA) }} z siedzibą we Wrocławiu upoważnia <br>
	@if($branch && $branch->id != NULL && $branch->id > 0)
		{{$branch->company->name}},
	@else
		<span style="color:red;">---</span>
	@endif
	reprezentowaną przez {{ checkIfEmpty('person', $inputs) }},
	nr dowodu osobistego {{ checkIfEmpty('nr_id', $inputs)}}
	do odebrania i transportu pojazdu {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}},
	o nr rej. {{$injury->vehicle->registration}},
	nr VIN {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}
	będącego własnością {{ checkIfEmpty('1', $ideaA) }}
				</p>
				<p >
					Pojazd znajduje się pod adresem :  <br>
					{{ checkIfEmpty('car_location',$inputs)}}
				</p>
			</div>

            <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:35px; ">
			    @include('injuries.docs_templates.modules.regards')
            </table>
		</div>
	</div>

</body>
</html>
