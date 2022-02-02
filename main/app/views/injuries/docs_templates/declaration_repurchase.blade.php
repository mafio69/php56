<?php //deklaracja odkupu wraku przez leasingobiorcę ?>
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

	<div id="header_basic" >
	  <table>
	    <tr>
	      <td style="text-align: left; border: solid 1pt black; color:#000000; padding: 5pt;">
	      	ZAŁĄCZNIK NR 6 DO PROCEDURY OBSŁUGI SZKÓD
	      </td>
	    </tr>
	  </table>
	</div>

	<div id="footer_basic" style="color:black;" >
		*niepotrzebne skreślić
	</div>


	<div id="body">


		<div class="page"  id="content">

			<div style="font-size: 7pt;">

				<table style="width: 100%; font-size: 10pt; font-weight:normal; ">
					<tr>
						<td style="text-align: left">{{$injury->vehicle->client->name}}</td>
					</tr>
                    <tr>
                        <td style="text-align: left">{{$injury->vehicle->client->correspond_post}} {{$injury->vehicle->client->correspond_city}}, {{$injury->vehicle->client->correspond_street}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left">NIP: {{$injury->vehicle->client->NIP}}</td>
                    </tr>
				</table>

				<table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
				    <tr>
                        <td style="text-align: left">Numer umowy leasingu: {{$injury->vehicle->nr_contract}}</td>
                    </tr>
				</table>

				<table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
                    <tr>
                        <td style="text-align: left">Oświadczam, że w związku ze szkodą całkowitą nr {{$injury->injury_nr}} z dnia {{$injury->date_event}} dotyczącą pojazdu {{$injury->vehicle->brand}}, {{$injury->vehicle->model}} nr rej {{$injury->vehicle->registration}}</td>
                    </tr>
                </table>

				<table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
                    <tr>
                        <td style="text-align: left">Chcę skorzystać z prawa pierwokupu / rezygnuję z przysługującego mi prawa pierwokupu pojazdu* użytkowanego w ramach umowy leasingu.</td>
                    </tr>
                </table>


				<table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
                    <tr>
                        <td style="text-align: left"><strong>W PRZYPADKU REZYGNACJI Z WYKUPU</strong>, zagospodarowania pozostałości pozostawiam właścicielowi pojazdu {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}</td>
                    </tr>
                </table>

                <table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
                    <tr>
                        <td style="text-align: left">Pojazd znajduje się pod adresem:</td>
                    </tr>
                    <tr>
                        <td style="line-height: 2;">
                        .................................................................................................................................................<br/>
                        .................................................................................................................................................<br/>
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
                    <tr>
                        <td style="text-align: left;">Wraz z pojazdem dla nowonabywcy zostaną przekazane następujące dokumenty</td>
                    </tr>
                </table>

                <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:20pt; border:thin solid black;"  cellspacing="0">
                    <tbody>
                        <tr >
                            <td style="width:35%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;">Nazwa dokumentu</td>
                            <td style="width:15%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;">Posiadam</td>
                            <td style="width:50%; border-bottom: thin solid #000000; text-align: center; padding: 15pt;">Nie posiadam, dokument znajduje się.....</td>
                        </tr>
                        <tr >
                            <td style="width:35%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;"><i>Dowód rejestracyjny lub pokwitowanie o zabraniu dowodu z policji</i></td>
                            <td style="width:15%; border-right:thin solid black; border-bottom: thin solid #000000;"></td>
                            <td style="width:50%; border-bottom: thin solid #000000;"></td>
                        </tr>
                        <tr >
                            <td style="width:35%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;"><i>Polisa OC</i></td>
                            <td style="width:15%; border-right:thin solid black; border-bottom: thin solid #000000;"></td>
                            <td style="width:50%; border-bottom: thin solid #000000;"></td>
                        </tr>
                        <tr >
                            <td style="width:35%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;"><i>Wszystkie komplety kluczyków</i></td>
                            <td style="width:15%; border-right:thin solid black; border-bottom: thin solid #000000;"></td>
                            <td style="width:50%; border-bottom: thin solid #000000;"></td>
                        </tr>
                        <tr >
                            <td style="width:35%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;"><i>Książki serwisowe pojazdu</i></td>
                            <td style="width:15%; border-right:thin solid black; border-bottom: thin solid #000000;"></td>
                            <td style="width:50%; border-bottom: thin solid #000000;"></td>
                        </tr>
                        <tr >
                            <td style="width:35%; border-right:thin solid black; border-bottom: thin solid #000000; text-align: center; padding: 15pt;"><i>Inne</i></td>
                            <td style="width:15%; border-right:thin solid black; border-bottom: thin solid #000000;"></td>
                            <td style="width:50%; border-bottom: thin solid #000000;"></td>
                        </tr>
                    </tbody>
                </table>

                <table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:10pt; ">
                    <tr>
                        <td style="text-align: left;"><strong>UWAGA:</strong> Jeśli z parkowaniem pojazdu związane są koszta, faktura z tego tytułu może być pokryta przez {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}, jednak ostatecznie koszta te będą brane pod uwagę podczas rozliczenia Państwa umowy leasingu.</td>
                    </tr>
                </table>

                <table style="width: 100%; font-size: 10pt; font-weight:normal; margin-top:20pt; ">
                    <tr>
                        <td style="width: 60%"></td>
                        <td style="text-align: center;">........................................</td>
                    </tr>
                    <tr>
                        <td style="width: 60%"></td>
                        <td style="text-align: center;">(data i podpis)</td>
                    </tr>
                </table>

			</div>

		</div>
	</div>

</body>
</html>
