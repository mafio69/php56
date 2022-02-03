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

	<div id="body">

		<div class="page"  id="content">
		  
			<div style="font-size: 7pt;">

                @include('injuries.docs_templates.modules.place')
                @include('injuries.docs_templates.modules.insurance_company')

			</div>
			<?php $vehicle = $injury->vehicle()->first();?>
			<div style ="margin-top:60pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
					<p style="font-weight:bold">
						Dotyczy: szkody częściowej nr {{$injury->injury_nr}} z dnia {{$injury->date_event}} 
					</p>
				<p style="margin-top:60pt; line-height:20pt;">
                {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} upoważnia warsztat naprawczy {{$branch->company->name}} do odbioru oceny technicznej z oględzin pojazdu marki {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}} nr rej. {{$injury->vehicle->registration}}.
				</p>
			</div>

			<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:25px; ">
                @include('injuries.docs_templates.modules.regards')
			</table>

		</div>
	</div>

</body>
</html>
