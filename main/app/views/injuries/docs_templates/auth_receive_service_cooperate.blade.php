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
						<td style="text-align:center;">Upoważnienie do odbioru odszkodowania</td>
					</tr>
                    @if($inputs['description'] != '')
                        <tr>
                            <td style="text-align:center; font-size: 10pt;">{{ $inputs['description'] }}</td>
                        </tr>
                    @endif
				</table>
				
			</div>
			<div style ="margin-top:50pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
				<p >
				Szanowni Państwo, 
				</p>
				<p >
	W związku ze zgłoszoną szkodą {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }} z siedzibą we Wrocławiu przy {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }} zwraca się z prośbą o przekazanie przyznanego odszkodowania z tytułu wymienionej szkody na rachunek warsztatu dokonującego naprawy pojazdu:
				</p>
				@if($branch && $branch->id != NULL && $branch->id > 0)
					<p style="text-align:center; font-weight:bold;">
						
						{{$branch->company->name}}<br>
						{{$branch->street}}, {{$branch->code}} {{$branch->city}} 
						
					</p>
					@if($branch->company->account_nr != '' && $branch->company->account_nr != 0)
					<p style="text-align:center; font-weight:bold;">
						Nr konta : {{$branch->company->account_nr}}
					</p>
					@endif
				@else
					<p style="text-align:center; font-weight:bold;">
						<i>nie przypisano warsztatu do szkody</i>
					</p>
				@endif
				<p>
					Wypłata odszkodowania powinna nastąpić na  podstawie faktur Vat za naprawę pojazdu.
				</p>
				
				<p>Decyzję o wypłacie odszkodowania prosimy o przesłanie na adres:</p>
				<p>
                    {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}<br>
                    {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}<br>
                    {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}<br>
                    {{ (isset($ideaA[4])) ? $ideaA[4] : '---' }}
				</p>

                <p style="font-size: 7pt; font-weight:normal; margin-top:30px;">
                    <i>Jednocześnie informujemy, że w przypadku zakwalifikowania szkody jako całkowitej, uprawnionym do odbioru odszkodowania pozostaje wyłącznie {{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}, {{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}, {{ (isset($ideaA[3])) ? $ideaA[3] : '---' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}, wówczas odszkodowanie prosimy przelać na konto:
						@if($owner->id == 1 && $vehicle->register_as == 0)
							{{ checkIfEmpty('16', $ideaA) }}
						@else
							{{ checkIfEmpty('10', $ideaA) }}
						@endif
					</i>
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
