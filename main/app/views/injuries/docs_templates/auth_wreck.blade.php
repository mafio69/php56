<?php //upoważnienie do odbioru wraku ?>
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

            </div>
            <div style ="margin-top:100pt;">
				<table style="font-size:14pt; font-weight:bold; width:100%;" align="center">

					<tr>
						<td style="text-align:center;">UPOWAŻNIENIE DO ODBIORU WRAKU</td>
					</tr>


				</table>

			</div>
			<div style ="margin-top:100pt; font-size:10pt; text-align:justify;text-justify:inter-word; line-height: 1.5">
            {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}, upoważnia pana {{ $inputs['name'] }} legitymującego się dowodem osobistym seria {{ $inputs['id_series'] }} nr {{ $inputs['id_number'] }}, pesel {{ $inputs['pesel'] }} do odbioru pojazdu {{ $injury->vehicle->brand.' '.$injury->vehicle->model }} nr rej. {{ $injury->vehicle->registration }} znajdującego się na terenie warsztatu {{ $inputs['address'] }}.<br/>
            <u>Telefon kontaktowy:</u><br/>
            Leasingobiorca: {{ $injury->vehicle->client()->first()->name }} tel. {{ $injury->vehicle->client()->first()->phone }}
			</div>

			<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:85pt; ">
                @include('injuries.docs_templates.modules.regards')
			</table>

		</div>
	</div>

</body>
</html>
