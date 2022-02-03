<?php //prośba o wyrejestrowanie pojazdu ?>
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

                @include('injuries.docs_templates.modules.insurance_company')
			</div>

			<div style ="margin-top:80pt; font-size:10pt; ">
				<p>
				    <table >
                        <tbody>
                            <tr >
                                <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;">Dotyczy: kradzieży pojazdu {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand)}} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)}} nr rej. {{ $injury->vehicle->registration }} szkoda nr {{ $injury->injury_nr }} z dnia {{ $injury->date_event }}</td>
                            </tr>
                        </tbody>
                    </table>
				</p>


			</div>
			<div style ="margin-top:20pt; font-size:10pt; text-align:justify;text-justify:inter-word;">
			        <p  >
                    W związku z kradzieżą w/w pojazdu przesyłamy dokumenty konieczne do zakończenia procesu likwidacji szkody.
                    </p>
			</div>

            <div>
                <table style="width: 100%; font-size: 9pt; font-weight:normal; margin-top:35px; ">
                    @include('injuries.docs_templates.modules.regards')
                </table>
            </div>

            @if(isset($inputs['attachments']))
			<div style ="margin-top:70pt; font-size:10pt; text-align:justify;text-justify:inter-word; ">
			    <p>
			        Załączniki:<br/>
                    <ol>
                        @foreach($inputs['attachments'] as $k => $v)
                            <li style="margin-bottom: 5pt; text-align: left;">{{ $v }}
                            @if($k == 0)
                                numer decyzji {{ $inputs['description'][$k] }}
                            @elseif($k == 3 || $k == 4)
                                seria i numer {{ $inputs['description'][$k] }}
                            @elseif($k == 6)
                                ilość sztuk {{ $inputs['description'][$k] }}
                            @elseif($k == 9)
                                - {{ $inputs['description'][$k] }}
                            @endif
                            </li>
                        @endforeach
                    </ol>
			    </p>
			</div>
            @endif

		</div>
	</div>

</body>
</html>
