<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="templates-src/css/notification.css" rel="stylesheet">
    <title></title>
    <style>
        body{
            font-family: DejaVu Sans, sans-serif;
        }
        .page {
            background-color: white;
            padding: 70px 20px 20px 20px;
        }

        @page {
            margin: 0.35in 0.35in 0.35in 0.35in;
        }
    </style>
</head>
<body>

@include('dos.other_injuries.docs_templates.modules.header')

    @include('dos.other_injuries.docs_templates.modules.footer')


	<div id="body">


		<div class="page"  id="content">

			<div style="font-size: 7pt;">

                @include('dos.other_injuries.docs_templates.modules.place')


                @include('dos.other_injuries.docs_templates.modules.client')

				<div style ="font-size:10pt; text-align:justify;text-justify:inter-word;">
					<p>Dotyczy: szkoda nr {{ $injury->injury_nr }}, przedmiot leasingu {{ $injury->object ? $injury->object->description : '' }}, {{ $injury->object->factoryNbr }}</p>
				</div>
				<div style ="margin-top:30pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
					<p>W związku z w/w szkodą prosimy o przekazanie odszkodowania na konto {{ checkIfEmpty('1', $ideaA) }} <br/>{{ checkIfEmpty('10', $ideaA) }}.</p>
				</div>

			</div>

            <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:50pt; ">
                <tbody>
                <tr>
                    <td style="width:50%; "></td>
                    <td style="text-align:center;">Z poważaniem</td>
                </tr>
                <tr>
                    <td style="width:50%; "></td>
                    <td style="text-align:center;">
                        @include('modules.signatures-dompdf')
                    </td>
                </tr>

                </tbody>
            </table>

        </div>
    </div>

</body>
</html>
