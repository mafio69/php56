	<?php //upoważnienie dla serwisu ?>
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

			<div style="font-size: 9pt; margin-left: 20pt;">

				<table style="width: 100%; font-size: 9pt; font-weight:normal;">
					<tr>
					    <td style="text-align: left">
					        <table style="width: 100%; font-size: 9pt; font-weight:normal;">
                                <tr>
                                    <td style="font-weight:bold;">Sprzedawca:</td>
                                <tr>
                                    <td style="font-weight:bold;">{{ (isset($ideaA[1])) ? $ideaA[1] : '---' }}</td>
                                </tr>
                                <tr>
                                    <td >{{ (isset($ideaA[2])) ? $ideaA[2] : '---' }}</td>
                                </tr>
                                <tr>
                                    <td >{{ (isset($ideaA[3])) ? $ideaA[3] : '' }} {{ (isset($ideaA[13])) ? $ideaA[13] : '---' }}</td>
                                </tr>
                                <tr>
                                    <td>NIP: {{ (isset($ideaA[8])) ? $ideaA[8] : '---' }}</td>
                                </tr>

                            </table>
					    </td>
						<td style="text-align: right; vertical-align: top;">Data wystawienia: {{date('d-m-Y')}}</td>
					</tr>

				</table>


				<table style="font-size:9pt; margin-top:10pt; font-weight:bold;" align="center">

					<tr>
						<td style="text-align:center;">FAKTUR PROFORMA</td>
					</tr>
					<tr>
                        <td style="text-align:center; padding-bottom: 5pt;">ORYGINAŁ / KOPIA</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">nr: {{ (isset($inputs['nr_invoice'])) ? $inputs['nr_invoice'] : '---' }}</td>
                    </tr>

				</table>

			</div>
			<div style ="margin-top:50pt; font-size:9pt; margin-left: 20pt; ">
				<table style="width: 100%; font-size: 9pt; font-weight:normal;">
                    <tr>
                        <td style="font-weight:bold; padding-bottom: 10pt;">Nabywca:</td>
                    <tr>
                        <td >@if($injury->wreck){{ $injury->wreck->buyer_name }}@endif</td>
                    </tr>
                    <tr>
                        <td >@if($injury->wreck){{ $injury->wreck->buyer_address_street }}@endif<</td>
                    </tr>
                    <tr>
                        <td >@if($injury->wreck){{ $injury->wreck->buyer_address_code }} {{ $injury->wreck->buyer_address_city }}@endif<</td>
                    </tr>
										@if($injury->wreck)
                    @if($injury->wreck->nip != '')
                    <tr>
                        <td>NIP: {{ $injury->wreck->nip }}<</td>
                    </tr>
                    @endif
										@endif
                </table>
            </div>
            <div style ="margin-top:20pt; font-size:9pt;">
                <div style =" margin-left: 20pt; margin-bottom: 0pt; font-weight: bold;">
                        umowa numer: {{$injury->vehicle->nr_contract}}
                </div>
                <table style="width: 100%; font-size: 7pt; font-weight: normal; margin-top: 0pt; border: thin solid #000000;" cellspacing="0" cellpadding="5">
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">L.p.</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">Nazwa towaru lub usługi</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">SWW</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">Ilość</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">JL</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">Cena jednostkowa bez podatku VAT</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">Kwota VAT</td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;">Wartość z podatkiem VAT</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">1</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">
                            {{ $injury->vehicle->brand }} {{ $injury->vehicle->model }}<br/>
                            Nr rejestracyjny: {{ $injury->vehicle->registration }}<br/>
                            Rok produkcji: {{ $injury->vehicle->year_production }}
                            </td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">1</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">@if($injury->wreck) {{ number_format($injury->wreck->repurchase_price, 2, ',',' ') }} @endif</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;">@if($injury->wreck) {{ number_format($injury->wreck->repurchase_price*0.23, 2, ',',' ') }} @endif</td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;">@if($injury->wreck) {{ number_format($injury->wreck->repurchase_price*1.23, 2, ',',' ') }} @endif</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px; font-weight: bold;">Razem:</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px; font-weight: bold;">@if($injury->wreck) {{ number_format($injury->wreck->repurchase_price*0.23, 2, ',',' ') }} @endif</td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px; font-weight: bold;">@if($injury->wreck) {{ number_format($injury->wreck->repurchase_price*1.23, 2, ',',' ') }} @endif</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 1px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 0px 1px 0px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 0px 0px;"></td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 0px 0px; text-align: right; font-weight: bold;">Do zapłaty złotych:</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 0px 0px; text-align: right; font-weight: bold;" colspan="3">@if($injury->wreck) {{ number_format($injury->wreck->repurchase_price*1.23, 2, ',',' ') }} @endif</td>
                            <td style="border: 1px solid #000000; border-width: 0px 1px 0px 0px; font-weight: bold;" colspan="3">@if($injury->wreck) {{ Idea\AmountTranslator\AmountTranslator::getInstance()->slownie($injury->wreck->repurchase_price*1.23, null, false, false) }} @endif</td>
                        </tr>
                    </tbody>
                </table>
			</div>

            <div style ="margin-top:20pt; font-size:7pt; ">
                <p style="font-weight: bold">
                    Termin zapłaty: 7 dni od daty wystawienia
                </p>
                <p>
                Należność prosimy przelać na rachunek bankowy:<br/>
                @if($owner->id == 1 && $vehicle->register_as == 0)
                    {{ checkIfEmpty('16', $ideaA) }}
                @else
                    {{ checkIfEmpty('10', $ideaA) }}
                @endif
                </p>
            </div>
            <div style="margin-top:150pt; ">
                <table style="width: 100%; font-size: 9pt; font-weight:normal; ">
                    <tbody>

                        <tr >
                            <td style="width:50%; text-align:center;">.................................................</td>
                            <td style="text-align:center;" >
                                @include('modules.signatures')
                            </td>
                        </tr>
                        <tr >
                            <td style="width:50%; text-align:center;">Podpis osoby upoważnionej<br/>do odbioru faktury</td>
                            <td style="text-align:center;">Podpis i pieczęć osoby<br/>upoważnionej do wystawienia faktury</td>
                        </tr>
                    </tbody>
                </table>
			</div>



		</div>
	</div>

</body>
</html>
