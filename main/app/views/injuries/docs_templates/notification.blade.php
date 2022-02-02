<?php //druk zgłoszenia szkody do serwisu?>
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

				<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:5px;">
					<tr>
						<td width="200pt">{{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}</td>
						<td style="text-align: right">Zleceniobiorca: {{ ($branch) ? $branch->company->name : '<i>brak danych</i>'}}</td>
					</tr>
					<tr>
						<td width="200pt">{{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}</td>
						<td style="text-align: right">Adres: {{ ($branch) ? $branch->street : '<i>brak danych</i>' }}</td>
					</tr>
					<tr>
						<td width="200pt">{{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}</td>
						<Td style="text-align: right">{{ ($branch) ? $branch->code.',' : ''}} {{ ($branch) ? $branch->city : ''}}</td>
					</tr>
					<tr>
						<td width="200pt"><a href="mailto:{{ (isset($ideaA[4])) ? $ideaA[4] : '' }}">{{ (isset($ideaA[4])) ? $ideaA[4] : '---' }}</a></td>
						<Td style="text-align: right">Tel. / fax.: {{ ($branch) ? $branch->phone : '<i>brak danych</i>'}}</td>
					</tr>
				</table>

				<table style="font-size:14pt; margin-top:20pt; font-weight:bold;" align="center">

					<tr>
						<td style="text-align:center;">Zgłoszenie szkody z dnia {{substr($injury->created_at, 0, 10)}}</td>
					</tr>
					<tr>
						<td style="text-align:center;">Nr sprawy {{$injury->case_nr}}</td>
					</tr>

				</table>
				<?php $vehicle = $injury->vehicle()->first();?>
				<?php $client = $injury->client()->first();?>
				<?php $driver = $injury->driver()->first();?>
				<table style="width: 100%; font-size: 8pt; font-weight:normal; border:thin solid black;">
					<tbody>
						<tr class="padding-top">
							<td style="width:25%;">Nr rejestracyjny:</td>
							<td style="width:25%;">{{ $vehicle->registration }}</td>
							<td style="width:25%;">Właściciel pojazdu:</td>
							<td style="width:25%;">{{ $vehicle->owner->name }}</td>
						</tr>
						<tr >
							<td style="width:25%;">Marka pojazdu:</td>
							<td style="width:25%;">{{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}}</td>
							<td style="width:25%;">Klient:</td>
							<td style="width:25%;">{{ ($client) ? $client->name : '---' }}</td>
						</tr>
						<tr >
							<td style="width:25%;">Numer VIN:</td>
							<td style="width:25%;">{{ ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin }}</td>
							<td style="width:25%;">Użytkownik pojazdu:</td>
							<td style="width:25%;">
								@if($injury->contact_person == 1 && !is_null($driver))
									{{ $driver->surname }} {{ $driver->name }}
								@else
									{{ $injury->notifier_surname}} {{ $injury->notifier_name }}
								@endif
							</td>
						</tr>
						<tr >
							<td style="width:25%;">Rok produkcji:</td>
							<td style="width:25%;">{{ $vehicle->year_production }}</td>
							<td style="width:25%;">Tel. Kontaktowy:</td>
							<td style="width:25%;">
								@if($injury->contact_person == 1 && $injury->driver_id !='' && !is_null($driver))
									{{ $driver->phone }}
								@else
									{{ $injury->notifier_phone }}
								@endif
							</td>
						</tr>
						<tr class="padding-btm">
							<td style="width:25%;">Przebieg (km):</td>
							<td style="width:25%;">{{ $vehicle->mileage }}</td>
							<td style="width:25%;">Adres e-mail:</td>
							<td style="width:25%;">
								@if($injury->contact_person == 1 && $injury->driver_id != '' && !is_null($driver))
									{{ $driver->email }}
								@else
									{{ $injury->notifier_email }}
								@endif
							</td>
						</tr>
					</tbody>
				</table>
				<table style="width: 100%; font-size: 8pt; font-weight:normal; border-left:thin solid black; border-right:thin solid black;">
					<tbody>
						<tr class="padding-top">
							<td style="width:25%;">Nr polisy AC pojazdu:</td>
							<td style="width:25%;">{{$vehicle->nr_policy}}</td>
							<td style="width:25%;">Sposób likwidacji szkody:</td>
							<td style="width:25%;">{{ $injury->injuries_type()->first()->name }}
							@if($injury->injuries_typ_id == 2)
							 - {{$injury->offender()->first()->zu}}
							@endif
							</td>
						</tr>
						<tr >
							<td style="width:25%;">Zakład ubezpieczeń:</td>
							<td style="width:25%;">{{ ($injury->injuryPolicy && $injury->injuryPolicy->insuranceCompany) ? $injury->injuryPolicy->insuranceCompany->name : '' }}</td>
							<td style="width:25%;">Nr szkody:</td>
							<td style="width:25%;">{{$injury->injury_nr}}</td>
						</tr>
						<tr class="padding-btm">
							<td style="width:25%;">Pakiet assistance:</td>
							<td style="width:25%;">{{$vehicle->assistance_name}}</td>
							<td style="width:25%;">Ubezpieczenie:</td>
							<td style="width:25%;">
								{{ Config::get('definition.compensationsNetGross')[$vehicle->netto_brutto] }}
							</td>
						</tr>

					</tbody>
				</table>

				<table style="width: 100%; font-size: 8pt; font-weight:normal; border-left:thin solid black; border-right:thin solid black;">
					<tbody>
						<tr class="padding-top">
							<td >Pojazd wymaga holowania:</td>
							<td >
								@if( $injury->if_towing == 1 )
		                        	tak
		                      	@else
		                        	nie
		                      	@endif
							</td>
							<td >Usługa door-to-door:</td>
							<td >
								@if( $injury->if_door2door == 1 )
									tak
								@else
									nie
								@endif
							</td>
						</tr>
						<tr class="padding-btm">
							<td >Gwarantowane auto zastępcze na czas naprawy:</td>
							<td >
								@if( $injury->if_courtesy_car == 1)
									tak
								@else
									nie
								@endif
							</td>
							<td >Zatrzymany dowód rej.:</td>
							<td >
								@if( $injury->if_registration_book == 1 )
		                        	tak
			                    @else
			                        nie
			                    @endif
							</td>
						</tr >


					</tbody>
				</table>

				<table style="width: 100%; font-size: 8pt; font-weight:normal; border:thin solid black; background-color: rgba(245, 250, 255, 1);">
					<tbody>
						<tr class="padding-top" >
							<td >Odbiorca faktury:</td>
							<td >
								@if( $injury->invoicereceives_id == 0 || $injury->invoicereceives_id == NULL)
									<i>nieokreślono odbiorcy faktury</i>
								@else
									@if($injury->invoicereceives_id == 1)
                                        {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}
									@elseif($vehicle->client)
										{{$vehicle->client->name}}
									@else
										---
									@endif
								@endif
							</td>
							<td >NIP:</td>
							<td >
								@if( $injury->invoicereceives_id == 0 || $injury->invoicereceives_id == NULL)
									<i>nieokreślono odbiorcy faktury</i>
								@else
									@if($injury->invoicereceives_id == 1)
                                        {{ (isset($ideaA[8])) ? $ideaA[8] : '---' }}
									@elseif($vehicle->client)
										{{$vehicle->client->NIP}}
									@else
										---
									@endif
								@endif
							</td>
						</tr>
						<tr class="padding-btm">
							<td >Adres:</td>
							<td colspan="3">
								@if( $injury->invoicereceives_id == 0 || $injury->invoicereceives_id == NULL)
									<i>nieokreślono odbiorcy faktury</i>
								@else
									@if($injury->invoicereceives_id == 1)
                                        {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }}
                                        {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }},
                                        {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}
									@elseif($vehicle->client)
										{{$vehicle->client->correspond_post}} {{$vehicle->client->correspond_city}}, {{$vehicle->client->correspond_street}}
									@else
										---
									@endif
								@endif
							</td>
						</tr >
					</tbody>
				</table>

				@if($vehicle->cfm != 1)
				<table style="width: 100%; font-size: 7pt; font-weight:normal; margin-top:0px;">
					<tbody>
					<tr>
						<td style="text-align:left;">
							UWAGA: Wszelkie koszty niepokryte przez Ubezpieczyciela, wynikające z potrąceń oraz amortyzacji, pokrywa klient! W sprawie płatności prosimy o kontakt przed wystawieniem FV.
						</td>
					</tr>
					</tbody>
				</table>
				@endif

				<table style="width: 100%; font-size: 8pt; font-weight:normal; margin-top:10px;">
					<tbody>
						<tr >
							<td style="text-align:center;">Uszkodzenia</td>

						</tr>
						<tr >
							<td style="border: thin solid black; padding: 5px;">
							@foreach($damageSet as $k => $v)
							{{ $damage->find($v->damage_id)->name}}
								@if($v->param != 0)
									@if($v->param == 1)
										lewe/y
									@else
										prawe/y
									@endif
								@endif
								;
							@endforeach
							</td>
						</tr>
					</tbody>
				</table>

				<table style="width: 100%; font-size: 8pt; font-weight:normal; margin-top:10px;">
					<tbody>
						<tr >
							<td style="text-align:center;">Uwagi</td>
						</tr>
						<tr >
							<td style="border: thin solid black; padding: 5px;">
								@if($injury->remarks_damage != 0)
								{{ $remarks->content }}
								@endif
							</td>
						</tr>
					</tbody>
				</table>

				<table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:10px; ">
					<tbody>
						<tr >
							<td style="width:33%; text-align:center;">.................................</td>
							<td style="width:33%;  text-align:center;">.................................</td>
							<td style="text-align:center;" rowspan="2">
								@include('modules.signatures')
							</td>
						</tr>
						<tr >
							<td style="width:33%;  text-align:center;">Podpis Użytkownika</td>
							<td style="width:33%;  text-align:center;">Podpis zleceniobiorcy</td>

						</tr>
						<tr >
							<td style="width:33%;  text-align:center;">(czytelny)</td>
							<td style="width:33%;  text-align:center;">(czytelny)</td>
							<td style="text-align:center;">Zlecający</td>
						</tr>

					</tbody>
				</table>
			</div>
			@if($vehicle->cfm == 1)
				<div style ="margin-top:20px;">
					Przed wystawieniem faktury prosimy o przesłanie do Działu Serwisu i Likwidacji Szkód {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} kompletu dokumentów niezbędnych do wystawienia upoważnienia do odbioru odszkodowania.<br/>
					{{ (isset($ideaA[1])) ? $ideaA[1] : '' }} prosi, aby po zakończeniu naprawy niezwłocznie wystawić fakturę(y) i wysłać pocztą.<br/>
					Faktury za naprawę proszę wystawić zgodnie z informacją w powyższym zgłoszeniu. Oryginał faktury proszę przesłać/przekazać odbiorcy faktury, kopię do Zakładu Ubezpieczeń.<br/>
					Użytkownik pojazdu nie jest osobą upoważnioną do podpisywania faktur VAT wystawionych na {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}<br/>
					Jeżeli zakres czynności wykonanych będzie wykraczał poza powyższe (bez uzgodnienia z {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}) lub faktura będzie miała błędy merytoryczne, {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} zastrzega sobie prawo wstrzymania wypłaty należności do momentu wyjaśnienia niezgodności
				</div>
			@else
				<div style ="margin-top:10px; font-size: 6pt;">
					Przed wystawieniem faktury prosimy o przesłanie do Działu Serwisu i Likwidacji Szkód {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} kompletu dokumentów niezbędnych do wystawienia upoważnienia do odbioru odszkodowania.<br/>
					{{ (isset($ideaA[1])) ? $ideaA[1] : '' }} prosi, aby po zakończeniu naprawy niezwłocznie wystawić fakturę(y) i wysłać pocztą.<br/>
					Faktury za naprawę proszę wystawić zgodnie z informacją w powyższym zgłoszeniu. Oryginał faktury proszę przesłać/przekazać: Europejski Dom Brokerski ul. Gwiaździsta 66 53-413 Wrocław., kopię do Zakładu Ubezpieczeń.<br/>
					Użytkownik pojazdu nie jest osobą upoważnioną do podpisywania faktur VAT wystawionych na {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}<br/>
					Jeżeli zakres czynności wykonanych będzie wykraczał poza powyższe (bez uzgodnienia z {{ (isset($ideaA[1])) ? $ideaA[1] : '' }}) lub faktura będzie miała błędy merytoryczne, {{ (isset($ideaA[1])) ? $ideaA[1] : '' }} zastrzega sobie prawo wstrzymania wypłaty należności do momentu wyjaśnienia niezgodności
				</div>
			@endif

			</div>

		</div>
	</div>

</body>
</html>
