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
        @if($ideaOffice)
				<table style=" width: 100%; font-size:10pt; margin-top:30pt; font-weight:bold;">
					<tr>
						<td style="text-align: right;">{{ $ideaOffice->name }}</td>
					</tr>
					<tr>
						<td style="text-align: right;">{{ $ideaOffice->street }}</td>
					</tr>
					<tr>
                        <td style="text-align: right;">{{ $ideaOffice->post.' '.$ideaOffice->city }}</td>
                    </tr>
				</table>
        @endif
			</div>

			<div style ="margin-top:80pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
				<p style="font-weight: bold;">
				    <table >
                        <tbody>
                            <tr >
                                <td style="font-weight: bold;border: 1px solid #000000; border-width: 0px 0px 1px 0px;">Dotyczy: wyrejestrowania pojazdu po kradzieży:</td>
                            </tr>
                        </tbody>
                    </table>
            @if($injury->vehicle)
				    <span style="font-weight: bold">{{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}} nr rej. {{ $injury->vehicle->registration }}</span><br/>
				    <span style="font-weight: bold">Umowa nr {{ $injury->vehicle->nr_contract }}</span>
            @endif
				</p>

				<p >
				W załączeniu przesyłam komplet dokumentów potrzebnych do wyrejestrowania skradzionego pojazdu.<br/>
	Decyzję o wyrejestrowaniu proszę odesłać na moje nazwisko, na adres {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}, {{ (isset($ideaA[2])) ? $ideaA[2] : '' }}, {{ (isset($ideaA[3])) ? $ideaA[3] : '' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '' }}.
				</p>
			</div>

            <div>
                <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:35px; ">
                    @include('injuries.docs_templates.modules.regards')
                </table>
            </div>

			<div style ="margin-top:150pt; font-size:10pt; text-align:justify;text-justify:inter-word; ">
			    <p>
			        <table style="margin-bottom: 10pt;">
			            <tbody>
                            <tr >
                                <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;">Załączniki:</td>
                            </tr>
                        </tbody>
                    </table>
                    <ol>
                        <li style="margin-bottom: 5pt;">Oryginał dowodu rejestracyjnego seria {{ $inputs['registration_document_series'] }} numer {{ $inputs['registration_document_number'] }}</li>
                        <li style="margin-bottom: 5pt;">Oryginał karty pojazdu seria {{ $inputs['vehicle_card_series'] }} numer {{ $inputs['vehicle_card_number'] }}</li>
                        <li style="margin-bottom: 5pt;">Oryginał postanowienia o umorzeniu dochodzenia</li>
                        <li style="margin-bottom: 5pt;">Polisa OC</li>
                    </ol>
			    </p>
			</div>

		</div>
	</div>

</body>
</html>
