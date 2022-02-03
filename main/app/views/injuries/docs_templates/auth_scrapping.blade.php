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
			<div>
			    <table style="font-size:14pt; margin-top:80pt; font-weight:bold; width: 100%" align="center">
                    <tr>
                        <td style="text-align:center;">UPOWAŻNIENIE DO ZEZŁOMOWANIA POJAZDU</td>
                    </tr>
                </table>
			</div>

			<div style ="margin-top:80pt; font-size:10pt; text-align:justify;text-justify:inter-word; line-height: 1.5;">
				<p >
				{{ (isset($ideaA[1])) ? $ideaA[1] : '' }} z siedzibą we {{ (isset($ideaA[13])) ? $ideaA[13] : '' }} przy {{ (isset($ideaA[2])) ? $ideaA[2] : '' }} upoważnia firmę {{ $inputs['company'] }} do zezłomowania na terenie {{ $inputs['address_pl'] }} pojazd {{ $injury->vehicle->brand.' '.$injury->vehicle->model }} o numerze rejestracyjnym {{ $injury->vehicle->registration }} i numerze VIN {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }} będącej naszą własnością.
				</p>
			</div>

            <div>
                <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:55px; ">
                    @include('injuries.docs_templates.modules.regards')
                </table>
            </div>

            @if($inputs['address_en'] != '')
                <div style="font-size: 7pt; margin-top: 30pt;">

                    @include('injuries.docs_templates.modules.place')

                </div>
                <div>
                    <table style="font-size:14pt; margin-top:80pt; font-weight:bold; width: 100%" align="center">
                        <tr>
                            <td style="text-align:center;">AUTHORIZATION TO SCRAP VEHICLE</td>
                        </tr>
                    </table>
                </div>

                <div style ="margin-top:80pt; font-size:10pt; text-align:justify;text-justify:inter-word; line-height: 1.5;">
                    <p >
                    {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} with its registered office at {{ (isset($ideaA[13])) ? $ideaA[13] : '' }}, {{ (isset($ideaA[2])) ? $ideaA[2] : '' }} authorizes the company {{ $inputs['company'] }} scrap of the {{ $inputs['address_pl'] }} vehicle {{ $injury->vehicle->brand.' '.$injury->vehicle->model }} registration number {{ $injury->vehicle->registration }} and VIN  {{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }} being our property.
                    </p>
                </div>

                <div>
                    <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:55px; ">
                        <tbody>
                            <tr >
                                <td style="width:50%; "></td>
                                <td style="text-align:center;">Sincerely,</td>
                            </tr>
                            <tr >
                                <td style="width:50%; "></td>
                                <td style="text-align:center;" >
                                    @include('modules.signatures')
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

		</div>
	</div>

</body>
</html>
