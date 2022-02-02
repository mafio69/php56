<?php //Oświadczenie w związku z wynajmem pojazdu zastępczego. ?>
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

                @include('injuries.docs_templates.modules.injury_info')

                @include('injuries.docs_templates.modules.insurance_company')

				<table style="font-size:14pt; margin-top:10pt; font-weight:bold;" align="center">

					<tr>
						<td style="text-align:center;">Oświadczenie w związku z wynajmem pojazdu zastępczego.</td>
					</tr>
					

				</table>
				
			</div>
			<div style ="margin-top:30pt; font-size: 9pt; text-align:justify;text-justify:inter-word;">
				<p >
					{{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we Wrocławiu ({{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}) przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }} jako właściciel  pojazdu informuje, że w/w pojazd, który uległ uszkodzeniu jest własnością {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}, a użytkowany jest przez Korzystającego na podstawie umowy leasingu. Pojazd jest wykorzystywany przez Korzystającego w sposób ciągły dla potrzeb wykonywanej działalności gospodarczej i w związku z powyższą szkodą komunikacyjną niezbędne było wynajęcie pojazdu zastępczego. Z uwagi na fakt zaistniałej szkody z ubezpieczenia OC sprawcy w/w samochód został wyeliminowany z ruchu, a kierowca pozbawiony możliwości wykonywania swoich obowiązków służbowych. Charakter pracy użytkownika zmusza do codziennych licznych kontaktów na rozległym terenie. Jednocześnie informujemy, że nasza Firma nie dysponuje pojazdami, które mogłyby być wykorzystywane jako zastępcze na czas naprawy. Firma nasza jest płatnikiem podatku VAT, a uszkodzony pojazd figuruje w środkach trwałych.
				</p>
				<p style="font-size: 9pt; font-weight:normal; margin-top:30px;">
				W związku z powyższym prosimy o zwrot poniesionych kosztów wynajmu pojazdu zastępczego i upoważniamy firmę :
				</p>
				@if($branch && $branch->id != NULL && $branch->id > 0)
					<p style=" font-size: 9pt; margin-top:20px; margin-left:30px; font-weight:bold;">

						{{$branch->company->name}}<br>
						{{$branch->street}}, {{$branch->code}} {{$branch->city}}

					</p>
				@else
					<p style="font-size: 9pt; margin-top:20px; margin-left:30px; font-weight:bold;">
						..............................<br>
						..............................
					</p>
				@endif
				<p style="font-size: 9pt; font-weight:normal; margin-top:20px; text-align:justify;text-justify:inter-word;">
				do odbioru należnego odszkodowania oraz podejmowania czynności w zakresie ustalania rozmiaru szkody i wysokości odszkodowania, do wglądu w dokumentację szkody celem jej uzupełnienia.
				</p>
			</div>

			<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
                @include('injuries.docs_templates.modules.regards')
			</table>

            @include('injuries.docs_templates.modules.note')

		</div>
	</div>

</body>
</html>
