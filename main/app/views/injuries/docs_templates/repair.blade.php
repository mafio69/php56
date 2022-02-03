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
	<div id="body" >

        <div id="footer_basic" >
            <table style="width: 100%; ">
               
                <tr >
                    <td style="text-align: right;"><div class="page-number"></div></td>
                </tr>
    
            </table>
    
        </div>
        
		<div class="page"  id="content">

			<div style="font-size: 7pt;">

                @include('injuries.docs_templates.modules.place')

				<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px;">
					<tr>
						<td >Wnioskodowca: {{ $injury->vehicle->client()->first()->name }}</td>
					<tr>
						<td style="padding-top: 10pt;">NUMER UMOWY LEASINGOWEJ: {{$injury->vehicle->nr_contract}}</td>
					</tr>
					<tr>
						<td >NUMER REJ.POJAZDU: {{$injury->vehicle->registration}}</td>
					</tr>
					<tr>
						<td >NR SZKODY: {{$injury->injury_nr}}</td>
					</tr>
					<tr>
						<td>DATA SZKODY: {{$injury->date_event}}</td>
					</tr>
                    <tr>
                        <td>RYZYKO:
                            @if( !is_null( $injury->type_incident()->first() ))
                            {{ $injury->type_incident()->first()->name}}
                            @endif
                        </td>
                    </tr>

				</table>

				<table style=" width: 100%; font-size:10pt; margin-top:30pt; font-weight:bold;">

					<tr>
						<td style="text-align: right">{{ (isset($ideaA[1])) ? $ideaA[1] : '' }}</td>
					</tr>
					<tr>
						<td style="text-align: right">{{ (isset($ideaA[2])) ? $ideaA[2] : '' }}</td>
					</tr>
					<tr>
						<td style="text-align: right">{{ (isset($ideaA[3])) ? $ideaA[3] : '' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '' }}</td>
					</tr>

				</table>

				<table style="font-size:14pt; margin-top:10pt; font-weight:bold;" align="center">

					<tr>
						<td style="text-align:center;">WNIOSEK O WYDANIE ZGODY NA NAPRAWĘ POJAZDU PO SZKODZIE CAŁKOWITEJ</td>
					</tr>


				</table>

			</div>
			<div  style ="margin-top:30pt; font-size:10pt; text-align:justify; text-justify:inter-word; style=" overflow: hidden;  ">
			    <p style="line-height: 1.5; ">Zwracam się z prośbą o wyrażenie zgody na dokonanie naprawy w/w pojazdu którego uszkodzenia zostały zakwalifikowane przez Ubezpieczyciela jako naprawa ekonomicznie nieuzasadniona.</p>
			    <p style="line-height: 1.5; ">Uzasadnienie wniosku</p>
			    <p style="line-height: 2; ">
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    .................................................................................................................................................<br/>
			    </p>
			</div>
            <hr>
			<div style ="font-size:10pt; text-align:justify; text-justify:inter-word; line-height: 1.5; padding-top: 70px; ">
                <p style="font-weight: bold">Uwaga: do wniosku należy bezwzględnie załączyć ocenę techniczną przygotowaną przez Ubezpieczyciela.</p>
                <p style="margin-top: 10pt;">Jednocześnie oświadczam ,że zgadzam się na następujące warunki dokonania tego typu naprawy:</p>
                <p>
                <ol>
                <li>Koszt naprawy pokrywany jest przez Leasingobiorcę</li>
                <li>Odszkodowanie przekazywane jest na konto {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} i zostaje rozliczone z
                Leasingobiorcą po zakończeniu naprawy, przedstawieniu faktur oraz potrąceniu wszystkich opłat należnych Leasingodawcy oraz podpisaniu porozumienia na mocy którego wznowiona jest umowa leasingowa</li>
                <li>Leasingobiorca pokrywa różnicę między wartością odszkodowania a kosztem naprawy</li>
                <li>Dokonanie opłaty w wysokości 400,00 PLN netto za likwidację szkody całkowitej</li>
                <li>Dokonanie opłaty w wysokości 800,00 PLN netto za wydanie zgody na naprawę</li>
                <li>Brak zaległości na umowie leasingowej</li>
                <li>Naprawa w ASO lub warsztacie posiadającym specjalizację</li>
                <li>Przedstawienie zaświadczenia o przeprowadzonych badaniach technicznych</li>
                <li>Przedstawienie pisemnego oświadczenia o numerze konta na które Idea Leasing
                zwróci ewentualna nadpłatę</li>
                <li>W przypadku szkody całkowitej z AC przedstawienie aktualnej wyceny wartości
                pojazdu po naprawie przygotowanej przez niezależnego rzeczoznawcę na podstawie której nastąpi ubezpieczenie pojazdu wraz z wnioskiem ubezpieczeniowym i zdjęciami poglądowymi auta</li>
                <li>Pokrycie kosztów ponownego ubezpieczenia przedmiotu leasingu na warunkach umów generalnych ubezpieczycieli współpracujących z Idea Leasing</li>
                </ol>
                </p>
                <p style="margin-top: 20pt;">
                Wyrażenie zgody na naprawę pojazdu po szkodzie całkowitej nie jest równoznaczne z przyjęciem odpowiedzialności za stan techniczny pojazdu przez Idea Leasing S.A.
                </p>

                <p style="margin-top: 20pt;">
                Oświadczam, że zapoznałem się z przedstawionymi warunkami i w pełni je akceptuję.
                </p>
			</div>

			<div style="margin-top:30pt;">
			<table style="width: 100%; font-size: 10pt; font-weight:normal;  ">
                <tbody>
                    <tr >
                        <td style="width:50%; ">Data ....................</td>
                        <td style="text-align: right">Podpis......................................</td>
                    </tr>
                    <tr >
                        <td style="width:50%; "></td>
                        <td style="text-align:right;" >
                            ( imię i nazwisko )
                        </td>
                    </tr>
                </tbody>
            </table>
			</div>

		</div>
	</div>

</body>
</html>
