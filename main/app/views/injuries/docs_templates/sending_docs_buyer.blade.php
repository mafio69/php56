<?php //Pismo dotyczące przesłania dokumentów do nabywcy ?>
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

                @include('injuries.docs_templates.modules.wreck')

				<table style="font-size:10pt; margin-top:60pt; align="center; width="100%;" >

					<tr>
						<td style="text-align:center;">Dotyczy: sprzedaży uszkodzonego pojazdu {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}} nr rej. {{ $injury->vehicle->registration }}</td>
					</tr>

				</table>
			</div>

            <div style ="margin-top:80pt; font-size:10pt; text-align:justify;text-justify:inter-word; line-height: 1.5;">
            				W związku ze sprzedaż w/w pojazdu przesyłamy Państwu następujące dokumenty:<br/>
            				- oryginał karty pojazdu nr {{ $inputs['vehicle_card_number'] }}<br/>
                            - fakturę zakupu uszkodzonego pojazdu nr {{ $inputs['invoice_number'] }}<br/>
                            - fakturę źródłową<br/>
                            - klucze<br/>
                            - książkę serwisową<br/>
                            - instrukcje obsługi<br/>
                            - aktualną polisę OC<br/>
                            - oryginał dowodu rejestracyjnego<br/>
                            - KRS<br/>
                            - wypowiedzenie polisy OC<br/>
                            - protokół przekazania pojazdu między oddziałami <br/>
                            - inne..............................
            </div>

			<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:90pt; ">
                @include('injuries.docs_templates.modules.regards')
			</table>

		</div>
	</div>

</body>
</html>
