<?php //odmowa wydania upoważnienia ?>
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

                @include('injuries.docs_templates.modules.client')
				
			</div>
			<div style ="margin-top:50pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
				<p style="text-indent:50px;">
				W odpowiedzi na Państwa wniosek z dnia {{$inputs['date_submit']}} o wydanie upoważnienia do odbioru odszkodowania związanego ze szkodą komunikacyjną na pojeździe o nr rejestracyjnym {{$injury->vehicle->registration}}, będącym przedmiotem leasingu w umowie nr {{$injury->vehicle->nr_contract}}, informujemy, że w związku z zaległością na państwa rachunku, przedmiotowe upoważnienie nie może zostać wydane. 
				</p>
				<p style="text-indent:50px;">
	W celu wyjaśnienia szczegółów dotyczących zaległej wobec {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} kwoty, prosimy o kontakt z Działem Monitoringu Płatności: (22) 101 57 04.
				</p>

				
			</div>

			<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:40px; ">
                @include('injuries.docs_templates.modules.regards')
			</table>

		</div>
	</div>

</body>
</html>
