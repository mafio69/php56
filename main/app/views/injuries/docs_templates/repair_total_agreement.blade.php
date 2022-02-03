<?php //wniosek o naprawę szkody całkowitej ?>
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
						<td style="text-align:center;">Zgoda na naprawę samochodu po szkodzie całkowitej</td>
					</tr>


				</table>

			</div>

			<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:15px; ">
                @include('injuries.docs_templates.modules.regards')
			</table>

		</div>
	</div>

</body>
</html>
