@extends('layouts.main')

@section('header')

	Dodawanie nowej szkody

@stop

@section('main')
	<form action="{{ url('injuries/make/store' ) }}" method="post" role="form" id="create-form">
		<div class="row marg-btm">
			<h3 class="text-center">
				<span class="text-danger" id="vmanage_company_name" style="display:none;"></span>
					<span class="text-warning marg-top-min" id="vip_client" style="display:none;">
					<i class="fa fa-star"></i> klient VIP
				</span>
				<div class="pull-left">
					<span class="label label-info">{{ $source }}</span>
				</div>
				@if(count( $injuries ) > 0)
					<div class="col-md-4 col-lg-3 " >
						<button type="button" class="btn btn-warning btn-sm btn-block marg-btm" data-toggle="collapse" data-target="#collapseInjuries" aria-expanded="false" aria-controls="collapseInjuries">
							W systemie istnieją już zgłoszenia dla tego pojazdu
							<span class="badge">{{ count( $injuries ) }}</span>
						</button>
					</div>
				@endif



				@if(count(  $letters ) > 0)
					<div class="col-md-4 col-lg-3 " >
						<button type="button" class="btn btn-info btn-sm btn-block marg-btm" data-toggle="collapse" data-target="#collapseLetters" aria-expanded="false" aria-controls="collapseLetters">
							W systemie istnieją pisma dopasowane do pojazdu
							<span class="badge">{{ count(  $letters ) }}</span>
						</button>
					</div>
				@endif

				@if($liquidation_card)
					<div class="col-md-4 col-lg-3">
						<span class="btn btn-info btn-sm  btn-block  marg-btm" disabled="disabled">
							pojazd posiada kartę o nr <strong>{{ $liquidation_card->number }} </strong> ważną do dnia
							<?php
								$expiration_date = new DateTime($liquidation_card->expiration_date);
								$now = new DateTime(date('Y-m-d'));
							?>
							<strong @if($expiration_date < $now) style="color:red;" @endif>
								{{ $liquidation_card->expiration_date }}
							</strong>
						</span>
					</div>
				@endif

				<div class="pull-right">
					<a href="{{ URL::route('injuries-new' ) }}" class="btn btn-default">Anuluj</a>
				</div>
			</h3>

			@if(count( $injuries ) > 0)
				<div class="col-sm-12 col-lg-8 col-lg-offset-2 collapse" id="collapseInjuries">
				<div class="panel panel-warning marg-top-min">
					<table class="table table-hover table-condensed">
						<thead class="bg-warning">
						<th>data zgłoszenia</th>
						<th>osoba zgłaszająca</th>
						<th>miejsce zdarzenia</th>
						<th>nr sprawy</th>
						<th>nr szkodu (ZU)</th>
						<th>opis zdarzenia</th>
						<th>status</th>
						<th></th>
						</thead>
						@foreach($injuries as $injury)
							<tr class="vertical-middle">
								<td>
									{{ substr($injury->created_at, 0, -3) }}
								</td>
								<td>
									{{ $injury->notifier_surname . ' ' . $injury->notifier_name }}
									tel:{{ $injury->notifier_phone . ' email:' . $injury->notifier_email }}
								</td>
								<td>
									{{ $injury->event_city . ' ' . $injury->event_street }}
									<br>
									{{ $injury->date_event }}
								</td>
								<td>
									@if(Auth::user()->can('kartoteka_szkody#wejscie'))
										<a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($injury->id))}}" >
											{{$injury->case_nr}}
										</a>
									@else
										{{$injury->case_nr}}
									@endif
								</td>
								<td @if($injury->dsp_notification) class="bg-danger tips" title="zgłoszenie DSP" data-container="body" @endif>
									{{ (($injury->injury_nr == '') ? '---' : $injury->injury_nr) }}
								</td>
								<td>
									{{ (($injury->info != 0 && !is_null($injury->info) ) ? $injury->getInfo->content : '---') }}
								</td>
								<td>
									{{ $injury->status->name }}
								</td>
								<td>
									@if($eaInjury)
										<a href="{{ url('injuries/make/attach', [$eaInjury->id, $injury->id]) }}" class="btn btn-primary btn-xs">
											<i class="fa fa-dot-circle-o fa-fw"></i> połącz
										</a>
									@endif
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			</div>
			@endif
			@if(count(  $letters ) > 0)
				<div class="col-sm-12 col-lg-8 col-lg-offset-2 collapse" id="collapseLetters">
				<div class="panel panel-info marg-top-min">
					<table class="table table-hover table-condensed">
						<thead class="bg-info">
						<Th></Th>
						<Th></Th>
						<Th>typ dokumentu</Th>
						<th>nazwa pisma</th>
						<th>nr szkody</th>
						<th>nr umowy</th>
						<th>nr rejestracyjny</th>
						<th></th>
						</thead>
						@foreach($letters as $letter)
							<tr class="vertical-middle">
								<td><a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" target="_blank" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a> </td>
								<td>
									@if( trim($letter->description) != '')
										<a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="Opis pisma" data-content="{{ $letter->description }}">
											<i class="fa fa-info-circle"></i> opis
										</a>
									@endif
								</td>
								<td>{{  $letter->uploadedDocumentType->name }}</td>
								<td>{{  $letter->name }}</td>
								<td>{{  $letter->injury_nr }}</td>
								<td>{{  $letter->nr_contract }}</td>
								<td>{{  $letter->registration }}</td>
								<td class="text-center">
									<label>
										przypisz do zgłoszenia <input type="checkbox" name="matchedLetters[]" value="{{ $letter->id }}">
									</label>
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			</div>
			@endif
		</div>
		@if($errors->any())
			<div class="row">
				<div class="alert alert-danger">
					<a href="#" class="close" data-dismiss="alert">&times;</a>
					{{ implode('', $errors->all('<li class="error">:message</li>'))}}
				</div>
			</div>
		@endif
		<div class="row">

		</div>
		<div class="row">
			<div class="form-group">
				<h4 class="inline-header"><span>Dane identyfikacyjne pojazdu:</span></h4>
				<div class="row">
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="VIN" >
							<span class="input-group-addon">VIN</span>
							@if( ($vehicle_type == 'Vehicles' && $vehicle->VIN == '') || ($vehicle_type != 'Vehicles' && $vehicle->vin == '') )
								@if($vehicle_type == 'Vehicles')
									{{ Form::text('VIN', '', array('id'=>'vin', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'VIN'))  }}
								@else
									{{ Form::text('vin', '', array('id'=>'vin', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'VIN'))  }}
								@endif
							@else
								<p class="form-control input-sm" disabled=""> {{ $vehicle_type == 'Vehicles' ? $vehicle->VIN : $vehicle->vin }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Rejestracja" >
							<span class="input-group-addon">Rejestracja</span>
							@if($vehicle->registration == '')
								{{ Form::text('registration', $mobileInjury ? $mobileInjury->registration : '', array('id'=>'registration', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Rejestracja'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ $vehicle->registration }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Marka" >
							<span class="input-group-addon">Marka</span>
							@if(! is_object($vehicle->brand) && $vehicle->brand == '')
								{{ Form::text('brand', $mobileInjury ? $mobileInjury->marka : '', array('id'=>'brand', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Marka'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand) }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Model" >
							<span class="input-group-addon">Model</span>
							@if(! is_object($vehicle->model) && $vehicle->model == '')
								{{ Form::text('model', $mobileInjury ? $mobileInjury->model : '', array('id'=>'model', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'MOdel'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model) }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Silnik" >
							<span class="input-group-addon">Silnik</span>
							@if(property_exists($vehicle, 'engine_capacity') && $vehicle->engine_capacity == '')
								{{ Form::text('engine_capacity', '', array('id'=>'engine_capacity', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Pojemność silnika'))  }}
							@elseif(property_exists($vehicle, 'engine') && $vehicle->engine == '')
								{{ Form::text('engine', '', array('id'=>'engine', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Pojemność silnika'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ $vehicle->engine_capacity }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Rok produkcji" >
							<span class="input-group-addon">Rok produkcji</span>
							@if($vehicle->year_production == '')
								{{ Form::text('year_production', '', array('id'=>'year_production', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Rok produkcji'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ $vehicle->year_production }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Pierwsza rejestracja" >
							<span class="input-group-addon">Pierwsza rej.</span>
							@if($vehicle->first_registration == '')
								{{ Form::text('first_registration', '', array('id'=>'first_registration', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Pierwsza rejestracja'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ $vehicle->first_registration }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Przebieg" >
							<span class="input-group-addon">Przebieg</span>
							@if(property_exists($vehicle, 'mileage') && $vehicle->mileage == '')
								{{ Form::text('mileage', '', array('id'=>'mileage', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Przebieg'))  }}
							@elseif(property_exists($vehicle, 'actual_mileage') && $vehicle->actual_mileage == '')
								{{ Form::text('actual_mileage', '', array('id'=>'actual_mileage', 'class' => 'form-control input-sm tips upper',  'placeholder' => 'Przebieg'))  }}
							@else
								<p class="form-control input-sm" disabled=""> {{ property_exists($vehicle, 'mileage') ? $vehicle->mileage : $vehicle->actual_mileage }}</p>
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="cfm" id="cfm" value="1" > CFM
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Dane właściciela i klienta:</span></h4>
				<div class="row">
					<div class="col-md-6 col-lg-6 editable marg-btm">
						<div class="input-group">
							<p class="form-control tips" disabled="" title="Właściel" > {{ $contract ? $contract->owner->contractor_name : $vehicle->owner->name }}</p>
							<span class="input-group-btn">
								@if($contract)
									<button class="btn btn-default modal-open" type="button" off-disable target="{{ URL::to('injuries/make/show-syjon-owner', array($contract->id)) }}" data-toggle="modal" data-target="#modal">
										<i class="fa fa-search"></i>
									</button>
								@else
									<button class="btn btn-default modal-open" type="button" off-disable target="{{ URL::to('injuries/make/show-dls-owner', array($vehicle->owner->id)) }}" data-toggle="modal" data-target="#modal">
										<i class="fa fa-search"></i>
									</button>
								@endif
							</span>
						</div>
					</div>
					<div class="col-md-6 col-lg-6 editable marg-btm">
						<div class="input-group">
							<p class="form-control tips" title="Klient" disabled=""> {{ $contract ? $contract->object_user->contractor_name : ($vehicle->client ? $vehicle->client->name : '') }}</p>
							<span class="input-group-btn">
								@if($contract)
									<button class="btn btn-default modal-open" type="button" off-disable target="{{ URL::to('injuries/make/show-syjon-object-user', array($contract->id)) }}" data-toggle="modal" data-target="#modal">
										<i class="fa fa-search"></i>
									</button>
								@elseif($vehicle->client)
									<button class="btn btn-default modal-open" type="button" off-disable target="{{ URL::to('injuries/make/show-dls-object-user', array($vehicle->client->id)) }}" data-toggle="modal" data-target="#modal">
										<i class="fa fa-search"></i>
									</button>
								@endif
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Status umowy:</span></h4>
				<div class="row">
					<div class="col-md-4 col-lg-3  marg-btm">
						<div class="input-group  tips" title="Nr umowy" >
							<span class="input-group-addon">Nr umowy</span>
							<p class="form-control" disabled=""> {{ $contract ? $contract->contract_number : $vehicle->nr_contract }}</p>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Data końca leasingu" >
							<span class="input-group-addon">Data końca leasingu</span>
							<p class="form-control" disabled=""> {{ $contract ? $contract->contract_planned_ending_date : $vehicle->end_leasing }}</p>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Status umowy" >
							<span class="input-group-addon">Status umowy</span>
							@if(! $contract && $vehicle->register_as == 0 && !$vehicle->contract_status)
								{{ Form::text('contract_status', '', array('id'=>'contract_status', 'class' => 'form-control tips upper',  'placeholder' => 'Status umowy', 'title' => 'Status umowy'))  }}
							@else
								<p class="form-control @if(! $contract_status->active ) btn-danger @endif" disabled="">
									{{ $contract_status->name }}
								</p>
							@endif
						</div>

					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="form-group">
							<div class="checkbox ">
								<label>
									<input type="checkbox" name="if_vip" id="if_vip" value="1" > Klient VIP
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Polisa ubezpieczeniowa:</span></h4>
				<div class="row">
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Nazwa zakładu ubezpieczeń" >
							<span class="input-group-addon">ZU</span>
						@if( $policy )
							<p class="form-control" disabled=""> {{ $policy->policy_insurance_company }}</p>
						@elseif(isset($vehicle->insurance_company))
							<p class="form-control" disabled=""> {{ $vehicle->insurance_company->name }}</p>
						@else
							{{ Form::text('insurance_company_name', '', array('id'=>'insurance_company_name', 'class' => 'form-control tips upper',  'placeholder' => 'Zakład ubezpieczeń', 'title' => 'Nazwa zakładu ubezpieczeń'))  }}
						@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Data ważności polisy" >
							<span class="input-group-addon">Data ważności</span>
							@if($policy )
								<p class="form-control" disabled=""> {{ $policy->policy_date_to }}</p>
							@elseif(isset($vehicle->insurance_expire_date))
								<p class="form-control" disabled=""> {{ $vehicle->insurance_expire_date }}</p>
							@else
								{{ Form::text('expire', '', array('id'=>'expire', 'class' => 'form-control tips upper',  'placeholder' => 'Data ważności polisy', 'title' => 'Data ważności polisy'))  }}
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Nr polisy" >
							<span class="input-group-addon">Nr polisy</span>
							@if($policy )
								<p class="form-control" disabled=""> {{ $policy->policy_number }}</p>
							@elseif(isset($vehicle->nr_policy) && $vehicle->nr_policy != '')
								<p class="form-control" disabled=""> {{ $vehicle->nr_policy }}</p>
							@else
							{{ Form::text('nr_policy', '', array('id'=>'nr_policy', 'class' => 'form-control tips upper',  'placeholder' => 'Numer polisy', 'title' => 'Nr polisy'))  }}
							@endif
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group  tips" title="Suma ubezpieczenia [zł]" >
							<span class="input-group-addon">Suma ubezp.</span>
							@if( $policy )
								<p class="form-control" disabled=""> {{ $policy->policy_insurance_amount }}</p>
							@elseif(isset($vehicle->insurance) && $vehicle->insurance > 0)
								<p class="form-control" disabled=""> {{ $vehicle->insurance }}</p>
							@else
								{{ Form::text('insurance', '', array('id'=>'insurance', 'class' => 'form-control tips upper',  'placeholder' => 'Suma ubezpieczenia [zł]', 'title' => 'Suma ubezpieczenia [zł]'))  }}
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group tips" title="wkład własny [zł]">
							<span class="input-group-addon">Wkład własny</span>
							{{ Form::text('contribution', '', array('id'=>'contribution', 'class' => 'form-control tips upper',  'placeholder' => 'Wkład własny [zł]', 'title' => 'Wkład własny [zł]'))  }}
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group tips" title="[netto/brutto]">
							<span class="input-group-addon">Netto/brutto</span>
							<select name="netto_brutto" class="form-control " disabled="true">
								<option value="1" @if($policy && ( $policy->policy_type_price == 'Netto')) selected @endif>netto</option>
								<option value="2" @if($policy &&  $policy->policy_type_price == 'Brutto') selected @endif>brutto</option>
								<option value="3" @if($policy && $policy->policy_type_price == 'Netto50') selected @endif>netto +50%</option>
							</select>
						</div>
					</div>

					<div class="col-md-4 col-lg-3 editable marg-btm tips" title="Assistance [tak/nie]">
						<div class="input-group " >
							<span class="input-group-addon">Assistance</span>
							<select name="assistance" class="form-control" @if($policy) disabled="true" @endif>
								<option value="0" @if($policy && ( $policy->policy_assistance == 'Nie') ) selected @endif>nie</option>
								<option value="1" @if($policy &&  $policy->policy_assistance != 'Nie') selected @endif>tak</option>
							</select>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 editable marg-btm">
						<div class="input-group tips" title="Nazwa pakietu Assistance">
							<span class="input-group-addon">Nazwa pakietu</span>
						{{ Form::text('assistance_name', '', array('id'=>'assistance_name', 'class' => 'form-control tips upper',  'placeholder' => 'Nazwa pakietu Assistance', 'title' => 'Nazwa pakietu Assistance'))  }}
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Dane kierowcy:</span></h4>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('driver_name', $eaInjury ? $eaInjury->driver_name : '', array('class' => 'form-control bold tips upper', 'placeholder' => 'imię',  'title' => 'Imię kierowcy'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('driver_surname', $eaInjury ? $eaInjury->driver_surname : '', array('class' => 'form-control bold tips upper', 'placeholder' => 'nazwisko',  'title' => 'Nazwisko kierowcy'))  }}
					</div>

					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('driver_phone', $eaInjury ? $eaInjury->driver_phone : '', array('class' => 'form-control tips bold upper', 'placeholder' => 'telefon',  'title' => 'Telefon kierowcy'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('driver_email', $eaInjury ? $eaInjury->driver_email : '', array('class' => 'form-control tips bold email ', 'placeholder' => 'email',  'title' => 'Email kierowcy'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('driver_city', $eaInjury ? $eaInjury->driver_city : '', array('class' => 'form-control tips bold upper', 'placeholder' => 'miasto',  'title' => 'Miasto kierowcy'))  }}
					</div>
					<div class="col-md-5 col-lg-4 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="dont_send_sms" value="1"> nie wysyłaj powiadomienia SMS o zarejestrowaniu szkody
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Dane zgłaszającego: <button type="button" class="btn btn-primary btn-xs" id="cp_driver"> kopiuj dane kierowcy</button></span></h4>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm tips" title ='Imię zgłaszającego'>
						{{ Form::text('notifier_name', $mobileInjury ? $mobileInjury->notifier_name : ($eaInjury ? $eaInjury->claimant_name : ''), array('class' => 'form-control bold   upper', 'placeholder' => 'imię'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nazwisko zgłaszającego'>
						{{ Form::text('notifier_surname', $mobileInjury ? $mobileInjury->notifier_surname : ($eaInjury ? $eaInjury->claimant_surname : ''), array('class' => 'form-control bold  upper', 'placeholder' => 'nazwisko'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm tips"  title ='Telefon zgłaszającego'>
						{{ Form::text('notifier_phone', $mobileInjury ? $mobileInjury->notifier_phone : ($eaInjury ? $eaInjury->claimant_phone : ''), array('class' => 'form-control bold  upper', 'placeholder' => 'telefon'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm tips" title = 'Email zgłaszającego'>
						{{ Form::text('notifier_email', $mobileInjury ? $mobileInjury->notifier_email : ($eaInjury ? $eaInjury->claimant_email : ''), array('class' => 'form-control bold email', 'placeholder' => 'email'))  }}
					</div>
				</div>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm " >
						<div class="radio">
							<label class="tips" title ='Zgłaszający jest osobą kontaktową'>
								{{ Form::checkbox('contact_person', '2') }}
								osoba do kontaktu
							</label>
						</div>
					</div>
					<div class="col-md-5 col-lg-4 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox"  name="dont_send_sms" value="1"> nie wysyłaj powiadomienia SMS o zarejestrowaniu szkody
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Dane szkody:</span></h4>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-4 marg-btm">
						<label >Data zdarzenia:</label>
						{{ Form::text('date_event', $mobileInjury ? $mobileInjury->date_event :  ($eaInjury ? $eaInjury->injury_event_date : ''), array('class' => 'form-control bold required', 'id'=>'date_event', 'placeholder' => 'data zdarzenia', 'autocomplete' => 'off'))  }}
					</div>
					<div class="col-md-4 col-lg-4 marg-btm">
						<label >Godzina zdarzenia:</label>
						{{ Form::text('time_event',  ($eaInjury ? $eaInjury->injury_event_time : ''), array('class' => 'form-control bold', 'id'=>'time_event', 'placeholder' => 'godzina zdarzenia'))  }}
					</div>
				</div>

				<div class="row injury_bg">
					<div class="col-md-12 marg-btm">
						<label >Miejsce zdarzenia:</label>
					</div>
					<div class="col-md-4 col-lg-4 marg-btm">
						{{ Form::text('event_city', $mobileInjury ? $mobileInjury->event_city : ($eaInjury ? $eaInjury->injury_event_city : '') , array('class' => 'form-control bold  upper', 'id' => 'city', 'placeholder' => 'miasto'))  }}
					</div>
					<div class="col-md-4 col-lg-4 marg-btm">
						{{ Form::text('event_street', ($eaInjury ? $eaInjury->injury_event_street : ''), array('class' => 'form-control bold  upper', 'id' => 'street', 'placeholder' => 'ulica'))  }}
					</div>

					<div class="col-md-12 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="mapShow" name="if_map"> Zlokalizuj na mapie
							</label>
						</div>
					</div>
					<div class="col-md-12 marg-btm">
						<div class="checkbox"  style="display:none;">
							<label >
								<input type="checkbox" id="correctMap" name="if_map_correct"> Skoryguj niezależnie pozycję
							</label>
						</div>
					</div>
					<div class="col-md-12 marg-btm">
						<div id="map-canvas" style="width:100%; height:400px; display:none; "></div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<h4 class="inline-header"><span>Rodzaj zdarzenia:</span></h4>
				<div class="row injury_bg">
					<div class="col-md-12 marg-btm">
                        <?php
                        $lp = 0;

                        foreach ($type_incident as $k => $v) {
                        if($lp == 0) echo '<div class="row">';
                        ?>

						<div class="col-md-3 col-lg-2 ">
							<div class="radio">
								<label>
									<input type="radio" name="zdarzenie" id="zdarzenie{{ $v->id }}" value="{{ $v->id }}"
								   	@if( ($mobileInjury && $mobileInjury->injuries_type_id == $v->id) || ($eaInjury && $eaInjury->injury_type_incident_id == $v->id))
									   checked
									@endif
									>
									{{ $v->name }}
								</label>
							</div>
						</div>
                        <?php
                        $lp++;
                        if($lp == 6){
                            echo '</div>';
                            $lp = 0;
                        }
                        }?>
					</div>
				</div>

				<div class="col-md-12 marg-btm">
					<label >Opis szkody:</label>
					{{ Form::textarea('remarks', $mobileInjury ? $mobileInjury->description() : ($eaInjury ? $eaInjury->description() : ''), array('class' => 'form-control bold ', 'placeholder' => 'Opis szkody'))  }}
				</div>
			</div>

			@if(! $mobileInjury || count($mobileInjury->damagesA()) == 0)
			<div class="form-group">
				<h4 class="inline-header"><span>Uszkodzenia: <button type="button" class="btn btn-primary btn-xs"  data-toggle="collapse" data-target="#faults" aria-expanded="false" aria-controls="faults"> oznacz szczegółowe uszkodzenia</button></span></h4>
				<div class="row injury_bg collapse" id="faults">
					@include('injuries.create_damage_part')
				</div>
			</div>
			@endif

			<div class="form-group">
				<h4 class="inline-header"><span>Informacje dodatkowe:</span></h4>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="form-group">
							<label>
								Szkoda zgłoszona do TU
							</label><br>
							<div class="btn-group" data-toggle="buttons" id="reported_ic_color">
								<label class="btn btn-default">
									<input type="radio" name="reported_ic" id="reported_ic" autocomplete="off" value="1" required @if($eaInjury && $eaInjury->injury_reported_insurance_company) checked @endif > TAK
								</label>
								<label class="btn btn-primary active">
									<input type="radio" name="reported_ic" id="reported_ic" autocomplete="off" checked value="0" required @if($eaInjury && ! $eaInjury->injury_reported_insurance_company) checked @endif> NIE
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Typ szkody: <button type="button" class="btn btn-primary btn-xs" id="offender_info" data-toggle="modal" data-target="#modal-offender" style="display:none;"> dane sprawcy</button></label>
						<select name="injuries_type" id="injuries_type" class="form-control required" >
							<option value="">---wybierz---</option>
                            @foreach($injuries_type as $k => $v)
                                 <option value="{{ $v->id }}"
									@if($mobileInjury && $mobileInjury->injuries_type_id == $v->id)
										selected
									@endif
								>{{ $v->name }}</option>
                            @endforeach
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Nr szkody:</label>
						{{ Form::text('injury_nr', $mobileInjury ? $mobileInjury->nr_injurie : ($eaInjury ? $eaInjury->injury_number : ''), array('class' => 'form-control upper bold', 'id'=>'injury_nr', 'placeholder' => 'nr szkody'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Zakład ubezpieczeń:</label>
						<span class="input-group  " >

					    <select name="insurance_company_id" id="insurance_company_id" class="form-control required" >
					    	<option value="">---wybierz---</option>
                            @foreach($insurance_companies as $k => $v)
                                <option value="{{ $v->id }}"
									@if($insurance_company_id == $v->id)
										selected="selected"
									@endif
								>{{ $v->name }}</option>
                            @endforeach
				    	</select>
				    	<span class="input-group-btn">
				        	<button target="{{ URL::route('insurance_companies-create-injury') }}" class="btn btn-default add-insurance_company tips" data-toggle="modal" data-target="#modal"  type="button" title="Dodaj nowy ZU"><span class="fa fa-plus"></span></button>
				      	</span>
						<span class="input-group-btn">
				        	<button class="btn btn-default show-insurance_company" type="button" ><span class="fa fa-search"></span></button>
				      	</span>
			      	</span>
					</div>
				</div>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Odbiór odszkodowania:</label>
						<select name="receives" id="receives" class="form-control" >
							<option value="">---wybierz---</option>
                            <?php foreach($receives as $k => $v){
                                echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                            }?>
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Odbiór faktury:</label>
						<select name="invoicereceives" id="invoicereceives" class="form-control " >
							<option value="">---wybierz---</option>
                            <?php foreach($invoicereceives as $k => $v){
                                echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                            }?>
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox marg-top">
							<label>
								<input type="checkbox" name="settlement_cost_estimate" id="settlement_cost_estimate" value="1"> kosztorysowe rozliczenie
							</label>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox marg-top">
							<label>
								<input type="checkbox" name="dsp_notification" id="dsp_notification" value="1"> zgłoszenie DSP
							</label>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox marg-top">
							<label>
								<input type="checkbox" name="vindication" id="vindication" value="1"> windykacja
							</label>
						</div>
					</div>
				</div>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Zawiadomiono policję:</label>
						<select name="police" id="police" class="form-control required" >
							<option value="-1" @if($mobileInjury && $mobileInjury->police() == '-1') selected @elseif($eaInjury && $eaInjury->injury_police_notified == '-1') @else selected @endif>nie ustalono</option>
							<option value="0" @if($eaInjury && $eaInjury->injury_police_notified == '0') selected @endif>nie</option>
							<option value="1" @if($mobileInjury && $mobileInjury->police() == '1') selected @elseif($eaInjury && $eaInjury->injury_police_notified == '1') selected @endif >tak</option>
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('police_nr', $mobileInjury ? $mobileInjury->nr_case : ($eaInjury ? $eaInjury->injury_police_number : ''), array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_nr', 'disabled' => 'disabled', 'placeholder' => 'nr zgłoszenia policji', 'title' => 'nr zgłoszenia policji'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('police_unit', $mobileInjury ? $mobileInjury->police_unite : ($eaInjury ? $eaInjury->injury_police_unit : ''), array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_unit', 'disabled' => 'disabled', 'placeholder' => 'jednostka policji', 'title' => 'jednostka policji' ))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						{{ Form::text('police_contact', $mobileInjury ? $mobileInjury->policeman_phone : ($eaInjury ? $eaInjury->injury_police_contact : ''), array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_contact', 'disabled' => 'disabled', 'placeholder' => 'kontakt z policją', 'title' => 'kontakt z policją'))  }}
					</div>
				</div>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="if_statement" value="1" @if($eaInjury && $eaInjury->injury_statement) checked @endif>
								Spisano oświadczenia
							</label>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="if_registration_book " value="1" @if($eaInjury && $eaInjury->injury_taken_registration) checked @endif>
								Zabrano dowód rejestracyjny
							</label>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Wina kierowcy:</label>
						<select name="if_driver_fault" class="form-control">
							<option value="-1" selected>nie ustalono</option>
							<option value="0">nie</option>
							<option value="1">tak</option>
						</select>
					</div>
				</div>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="if_towing" value="1" @if($eaInjury && $eaInjury->injury_towing) checked @endif>
								Wymaga holowania
							</label>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="if_courtesy_car" value="1" @if($eaInjury && $eaInjury->injury_replacement_vehicle) checked @endif>
								Wymagane auto zastępcze
							</label>
						</div>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="if_door2door" value="1">
								Usługa door2door
							</label>
						</div>
					</div>
				</div>
				<div class="row injury_bg">
					<div class="col-md-4 col-lg-3 marg-btm">
						<label >Samochód znajduje się w serwisie:</label>
						<select name="in_service" class="form-control" required>
							<option value="" selected>---wybierz---</option>
							<option value="-1">nie ustalono</option>
							<option value="0" @if($eaInjury && !$eaInjury->injury_vehicle_in_service) checked @endif>nie</option>
							<option value="1" @if($eaInjury && $eaInjury->injury_vehicle_in_service) checked @endif>tak</option>
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label>Naprawa w sieci IL:</label>
						<select name="if_il_repair" class="form-control" id="if_il_repair" required>
							<option value="" selected>---wybierz---</option>
							<option value="-1">nie ustalono</option>
							<option value="0">nie</option>
							<option value="1" @if($branch && !$if_company_groups) selected @endif>tak</option>
							<option value="2" @if($branch && $if_company_groups) selected @endif>tak - lista warunkowa</option>
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm" @if(! $if_company_groups) style="display:none;" @endif id="il_repair_info">
						<label>Przyczyna:</label>
						<select name="il_repair_info" class="form-control required">
							<option value="">---wybierz---</option>
							@foreach(RepairInformation::lists('name', 'id') as $key=>$value)
								<option value="{{$key}}" @if($if_company_groups && $key == 5) selected @endif >{{$value}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4 col-lg-3 marg-btm" @if($if_company_groups ) @else style="display:none;" @endif id="il_repair_info_description">
						{{ Form::text('il_repair_info_description', '', array('class' => 'form-control tips marg-top', 'placeholder' => 'opis', 'title' => 'opis'))  }}
					</div>
					<div class="col-md-4 col-lg-3 marg-btm">
						<label>ZGODA NA OFERTĘ CAS:</label>
						<br>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="cas_offer_agreement" value="1">
							</label>
						</div>
					</div>
				</div>

				<div class="row injury_bg">
					<div class="center-block" style="text-align:center">
						@if($branch)
							<h4 id="branch_text" >Przypisany serwis</h4>
							<div id="branch_data">
								<h4>{{ $branch->short_name }}</h4><p>Adres: {{ $branch->address }}</p>
							</div>
						@else
							<h4 id="branch_text" style="display:none"></h4>
							<div id="branch_data"  style="display:none"></div>
							<a href="#" target="{{ URL::route('injuries-assignCompany', array('all')) }}" class="modal-open-lg-special btn btn-primary" data-toggle="modal" data-target="#modal-lg" >przypisz serwis</a>
						@endif
					</div>
				</div>

				<div class="row injury_bg marg-btm">
					<div class="col-md-12 marg-btm">
						<label >Informacja wewnętrzna:</label>
						{{ Form::textarea('info', '', array('class' => 'form-control  bold', 'placeholder' => 'Informacja wewnętrzna'))  }}
					</div>
				</div>
			</div>


			@if($mobileInjury && count($mobileInjury->damagesA()) > 0)
				<div class="form-group">
					<h4 class="inline-header"><span>Zgłoszone uszkodzenia:</span></h4>
					<div class="row">
						<div class="col-sm-12 col-md-6 col-lg-4 marg-btm">
							<div id="damage_div">
								<div class="part2">
									<div class="elem" style="height: 20%;">&nbsp;</div>
									<div class="elem" elem="1" >
										<img src="/assets/css/images/damages/1{{ (isset($mobileInjury->damagesA()[1])) ? 'p' : '' }}.png"
											 style="width: 62%; margin-right: 16%; margin-bottom: 10%;" />
									</div>
									<div class="elem" elem="2" >
										<img src="/assets/css/images/damages/2{{ (isset($mobileInjury->damagesA()[2])) ? 'p' : '' }}.png"
											 style="width: 64%; margin-left: 30%" />
									</div>
									<div class="elem" elem="3" >
										<img src="/assets/css/images/damages/3{{ (isset($mobileInjury->damagesA()[3])) ? 'p' : '' }}.png"
											 style="width: 64%; margin-left: 30%" />
									</div>
									<div class="elem" elem="4" >
										<img src="/assets/css/images/damages/4{{ (isset($mobileInjury->damagesA()[4])) ? 'p' : '' }}.png"
											 style="width: 82%;" />
									</div>
								</div>
								<div class="part3">
									<div class="elem" elem="5" >
										<img src="/assets/css/images/damages/5{{ (isset($mobileInjury->damagesA()[5])) ? 'p' : '' }}.png"
											 style="width: 90%;" /></div>
									<div class="elem" elem="6" >
										<img src="/assets/css/images/damages/6{{ (isset($mobileInjury->damagesA()[6])) ? 'p' : '' }}.png"
											 style="width: 70%;" /></div>
									<div class="elem" elem="7" >
										<img src="/assets/css/images/damages/7{{ (isset($mobileInjury->damagesA()[7])) ? 'p' : '' }}.png"
											 style="width: 70%;" /></div>
									<div class="elem" elem="8" >
										<img src="/assets/css/images/damages/8{{ (isset($mobileInjury->damagesA()[8])) ? 'p' : '' }}.png"
											 style="width: 58%;" /></div>
									<div class="elem" elem="9" >
										<img src="/assets/css/images/damages/9{{ (isset($mobileInjury->damagesA()[9])) ? 'p' : '' }}.png"
											 style="width: 62%;" /></div>
									<div class="elem" elem="10" >
										<img src="/assets/css/images/damages/10{{ (isset($mobileInjury->damagesA()[10])) ? 'p' : '' }}.png"
											 style="width: 60%;" /></div>
									<div class="elem" elem="11" >
										<img src="/assets/css/images/damages/11{{ (isset($mobileInjury->damagesA()[11])) ? 'p' : '' }}.png"
											 style="width: 90%;" /></div>
								</div>
								<div class="part2">
									<div class="elem" style="height: 20%;">&nbsp;</div>
									<div class="elem" elem="12" >
										<img src="/assets/css/images/damages/12{{ (isset($mobileInjury->damagesA()[12])) ? 'p' : '' }}.png"
											 style="width: 62%; margin-left: 16%; margin-bottom: 10%;" /></div>
									<div class="elem" elem="13" >
										<img src="/assets/css/images/damages/13{{ (isset($mobileInjury->damagesA()[13])) ? 'p' : '' }}.png"
											 style="width: 64%; margin-right: 30%" /></div>
									<div class="elem" elem="14" >
										<img src="/assets/css/images/damages/14{{ (isset($mobileInjury->damagesA()[14])) ? 'p' : '' }}.png"
											 style="width: 64%; margin-right: 30%" /></div>
									<div class="elem" elem="15" >
										<img src="/assets/css/images/damages/15{{ (isset($mobileInjury->damagesA()[15])) ? 'p' : '' }}.png"
											 style="width: 82%;" /></div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 col-md-6 col-lg-8 marg-btm">
							<div class="row">
								<div class="col-md-6">
									<table class="table table-hover">
                                        <?php for($i = 0 ; $i < ($ct_damage/2); $i++){?>
										<tr >
											<td>
												<input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" <?php if(isset($damageInjury[$damage[$i]['id']])){?> checked="checked" <?php }?> />
											</td>
											<td class="check" style="text-align:left; padding-left:5px;">
												<b><label for="uszkodzenia_check<?php echo $damage[$i]['id'];?>"><?php echo $damage[$i]['name'];?></label></b>
											</td>
                                            <?php if($damage[$i]['param'] != 0){?>
											<td  class="check">
												<label for="strona<?php echo $damage[$i]['id'];?>l">
                                                    <?php switch($damage[$i]['param']){
                                                        case 1:
                                                            echo 'lewy:';
                                                            break;
                                                        case 2:
                                                            echo 'lewe:';
                                                            break;
                                                        case 3:
                                                            echo 'lewa:';
                                                            break;
                                                    }?>
												</label>
												<input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>l" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="1" <?php if(isset($damageInjury[$damage[$i]['id']][1])){?> checked="checked" <?php }?>/>
											</td>
											<td class="check">
												<label for="strona<?php echo $damage[$i]['id'];?>r">
                                                    <?php switch($damage[$i]['param']){
                                                        case 1:
                                                            echo 'prawy:';
                                                            break;
                                                        case 2:
                                                            echo 'prawe:';
                                                            break;
                                                        case 3:
                                                            echo 'prawa:';
                                                            break;
                                                    }?>
												</label>
												<input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2" <?php if(isset($damageInjury[$damage[$i]['id']][2])){?> checked="checked" <?php }?>/>
											</td>
                                            <?php }else{?>
											<td colspan="2"></td>
                                            <?php }?>
										</tr>
                                        <?php }?>
									</table>
								</div>
								<div class="col-md-6">
									<table class="table table-hover">
                                        <?php for($i ; $i < $ct_damage; $i++){?>
										<tr>
											<td>
												<input type="checkbox" id="uszkodzenia_check<?php echo $damage[$i]['id'];?>" class="uszkodzenia_check" name="uszkodzenia[]" value="<?php echo $damage[$i]['id'];?>" />
											</td>
											<td class="check" style="text-align:left; padding-left:5px;">
												<b><label for="uszkodzenia_check<?php echo $damage[$i]['id'];?>"><?php echo $damage[$i]['name'];?></label></b>
											</td>
                                            <?php if($damage[$i]['param'] != 0){?>
											<td  class="check">
												<label for="strona<?php echo $damage[$i]['id'];?>l">
                                                    <?php switch($damage[$i]['param']){
                                                        case 1:
                                                            echo 'lewy:';
                                                            break;
                                                        case 2:
                                                            echo 'lewe:';
                                                            break;
                                                        case 3:
                                                            echo 'lewa:';
                                                            break;
                                                    }?>
												</label>
												<input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>l" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="1" />
											</td>
											<td class="check">
												<label for="strona<?php echo $damage[$i]['id'];?>r">
                                                    <?php switch($damage[$i]['param']){
                                                        case 1:
                                                            echo 'prawy:';
                                                            break;
                                                        case 2:
                                                            echo 'prawe:';
                                                            break;
                                                        case 3:
                                                            echo 'prawa:';
                                                            break;
                                                    }?>
												</label>
												<input class="check_strona" id="strona<?php echo $damage[$i]['id'];?>r" name="strona<?php echo $damage[$i]['id'];?>[]" disabled="disabled" type="checkbox" value="2"  />
											</td>
                                            <?php }else{?>
											<td colspan="2"></td>
                                            <?php }?>
										</tr>
                                        <?php }?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			@endif

			@if($mobileInjury && !$mobileInjury->files->isEmpty())
				<div class="form-group">
					<h4 class="inline-header"><span>Przesłane zdjęcia:</span></h4>
					<div class="row">
						<div class="col-sm-12 marg-btm">
                            <?php foreach ($mobileInjury->files as $k => $v) {?>
							<div class="col-sm-6 col-md-3" id="image-{{$v->id}}">
								<div class="thumbnail">
									<a href="/file/uploads/mobile_images/{{$v->file}}/full" data-lightbox="image-before" >
										{{ HTML::image('/file/uploads/mobile_images/'.$v->file.'/thumb') }}
									</a>
									<label ><input name="pictures[]" type="checkbox" value="{{$v->id}}" /> dołącz do kartoteki</label>

								</div>
							</div>
                            <?php }?>
						</div>
					</div>
				</div>
			@endif


			{{Form::token()}}
			@if($mobileInjury)
				{{ Form::hidden('mobile_injury_id', $mobileInjury->id) }}
			@endif
			@if($eaInjury)
				{{ Form::hidden('ea_injury_id', $eaInjury->id) }}
			@endif
			{{Form::hidden('lat', $mobileInjury ? $mobileInjury->lat :'', array('id' => 'lat'))}}
			{{Form::hidden('lng', $mobileInjury ? $mobileInjury->lng :'', array('id' => 'lng'))}}
			{{Form::hidden('vehicle_type', $vehicle_type, array('id' => 'vehicle_type'))}}
			{{Form::hidden('driver_id', '', array('id' => 'driver_id'))}}
			{{Form::hidden('register_as', '', array('id' => 'register_as'))}}
			{{Form::hidden('branch_id', $branch ? $branch->id : 0, array('id' => 'branch_id'))}}
			{{Form::hidden('branch_dont_send_sms', '', array('id' => 'branch_dont_send_sms'))}}
            <?php //adm-administator, inf-infolinia?>
			{{Form::hidden('insert_role', 'adm')}}

			{{Form::hidden('vehicle_id', $vehicle->id, array('id' => 'vehicle_id'))}}
			{{Form::hidden('contract_internal_agreement_id', $contract_internal_agreement_id, array('id' => 'contract_internal_agreement_id'))}}
			{{Form::hidden('contract_id', $contract ? $contract->id : null, array('id' => 'contract_id'))}}
			{{Form::hidden('policy_id', $policy  ? $policy->policy_id : null, array('id' => 'policy_id'))}}
			{{ Form::hidden('policy_insurance_company_id',  $policy_insurance_company_id) }}
			{{ Form::hidden('owner_id', isset($vehicle->owner_id) ? $vehicle->owner_id : null) }}
			{{ Form::hidden('client_id', isset($vehicle->client_id) ? $vehicle->client_id : null ) }}
			{{ Form::hidden('is_as_vehicle', $is_as_vehicle) }}
			{{Form::hidden('seller_id', $vehicle ? $vehicle->seller_id : null)}}
		</div>

		<div class="modal fade " id="modal-offender" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog ">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Dane sprawcy</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<div class="row">
								<div class="col-md-6 marg-btm">
									{{ Form::text('offender_surname', '', array('class' => 'form-control upper', 'placeholder' => 'nazwisko', 'disabled' => 'disabled'))  }}
								</div>
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_name', '', array('class' => 'form-control upper',  'placeholder' => 'imię', 'disabled' => 'disabled'))  }}
								</div>
							</div>

							<h4 class="inline-header"><span>Adres zameldowania:</span></h4>
							<div class="row">
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_post', '', array('class' => 'form-control upper',  'placeholder' => 'kod pocztowy', 'disabled' => 'disabled'))  }}
								</div>
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_city', '', array('class' => 'form-control upper',  'placeholder' => 'miasto', 'disabled' => 'disabled'))  }}
								</div>
							</div>
							<div class="row">
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_street', '', array('class' => 'form-control upper',  'placeholder' => 'ulica', 'disabled' => 'disabled'))  }}
								</div>
							</div>

							<h4 class="inline-header"><span>Dane pojazdu:</span></h4>
							<div class="row">
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_registration', '', array('class' => 'form-control upper',  'placeholder' => 'nr rejestracyjny', 'disabled' => 'disabled'))  }}
								</div>
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_car', '', array('class' => 'form-control upper',  'placeholder' => 'marka i model pojazdu', 'disabled' => 'disabled'))  }}
								</div>
							</div>
							<div class="row">
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_oc_nr', '', array('class' => 'form-control upper',  'placeholder' => 'nr polisy OC', 'disabled' => 'disabled'))  }}
								</div>
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_zu', '', array('class' => 'form-control upper',  'placeholder' => 'nazwa ZU', 'disabled' => 'disabled'))  }}
								</div>
							</div>
							<div class="row">
								<div class="col-md-6  marg-btm">
									{{ Form::text('offender_expire', '', array('class' => 'form-control upper',  'placeholder' => 'data ważności polisy', 'disabled' => 'disabled'))  }}
								</div>
								<div class="col-md-6  marg-btm">
									<select name="offender_owner" class="form-control" disabled="disabled" >
										<option value="1">Sprawca jest właścicielem pojazdu:</option>
										<option value="1">tak</option>
										<option value="0">nie</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12  marg-btm">
									{{ Form::textarea('offender_remarks', '', array('class' => 'form-control ',  'placeholder' => 'uwagi', 'disabled' => 'disabled', 'style' => 'height:50px;'))  }}
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button"  class="btn btn-default" data-dismiss="modal">Zamknij</button>
					</div>
				</div>
			</div>
		</div>
		<div class="row marg-btm">
			<h4 class="inline-header "></h4>
			<div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
				{{ Form::submit('Zapisz',  array('id'=>'addInjurySubmit', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }}
			</div>
			<style>
				.bootstrap-timepicker-widget{
					max-width: 200px!important;
				}
			</style>
		</div>
	</form>

	@stop

	@section('headerJs')
	@parent
	<script type="text/javascript" >
        var modal_template = '<div class="modal-header"><h4 class="modal-title"></h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button></div>';
        var mapa;
        var geocoder = new google.maps.Geocoder();
        var marker ;
        var infowindow = new google.maps.InfoWindow();
        var correlation = 0;

        function initialize() {

            var myOptions = {
                zoom: 7,
                scrollwheel: true,
                navigationControl: false,
                mapTypeControl: false,
                center: new google.maps.LatLng(52.528846,17.071874)
            };

            mapa = new google.maps.Map(document.getElementById('map-canvas'), myOptions);

            google.maps.event.addListener(mapa, 'click', function(event) {
                placeMarker(event.latLng);
            });

            var slat = $('#lat').val();
            var slng = $('#lng').val();

            if(slat != ''  && slng != ''){
                latlng = new google.maps.LatLng(slat,slng);
                placeMarker(latlng);
            }

        };

        function initListenerDrag(){
            google.maps.event.addListener(marker, "dragend", function(event) {
                var lat_d = event.latLng.lat();
                var lng_d = event.latLng.lng();
                $('#lat').val(lat_d);
                $('#lng').val(lng_d);
                var latlng = new google.maps.LatLng(lat_d, lng_d);
                if(correlation == 0){
                    geocoder.geocode( {'latLng': latlng}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {

                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(mapa, marker);

                            var adressA = results[0].formatted_address.split(', ');
                            var kod = adressA[1];
                            var rx = /\d\d-\d\d\d/;
                            var wynik = rx.exec(kod);
                            if (wynik)
                            {
                                kod = wynik[0];
                                adressA[1] = adressA[1].replace(rx, '');
                                adressA[1] = $.trim(adressA[1]);
                            }
                            else
                            {
                                kod = '';
                            }

                            //$('#id_kod_pocztowy').val(kod);

                            $("#city").val(adressA[1]);
                            $('#street').val(adressA[0]);

                        }
                    });
                }
            });
        }

        function placeMarker(location) {
            if ( marker ) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    draggable:true,
                    map: mapa
                });
            }
            $('#lat').val(location.lat());
            $('#lng').val(location.lng());
            if(correlation == 0){
                geocoder.geocode( {'latLng': location}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {

                        infowindow.setContent('<div style="height:30px;">'+results[0].formatted_address+'</div>');
                        infowindow.open(mapa, marker);

                        var adressA = results[0].formatted_address.split(', ');

                        var kod = adressA[1];
                        var rx = /\d\d-\d\d\d/;
                        var wynik = rx.exec(kod);
                        if (wynik)
                        {
                            kod = wynik[0];
                            adressA[1] = adressA[1].replace(rx, '');
                            adressA[1] = $.trim(adressA[1]);
                        }
                        else
                        {
                            kod = '';
                        }

                        //$('#code').val(kod);

                        $("#city").val(adressA[1]);
                        $('#street').val(adressA[0]);


                    }
                });
            }
            initListenerDrag() ;

        }

        function lokalizacja_wyszukiwanie(){
            var slat = 0;
            var slng = 0;
            var address = $("#city").val()+' '+$('#street').val();
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    mapa.panTo(results[0].geometry.location);
                    slat = results[0].geometry.location.lat();
                    slng = results[0].geometry.location.lng();

                    $('#lat').val(slat);
                    $('#lng').val(slng);

                    var latlng = new google.maps.LatLng(slat,slng);
                    if(marker == null){
                        marker = new google.maps.Marker({
                            position: latlng,
                            draggable:true,
                            map: mapa
                        });
                    }else{
                        marker.setPosition(latlng);
                    }

                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(mapa, marker);

                    initListenerDrag() ;

                    if( $('#street').val() == '')
                        mapa.setZoom(12);
                    else
                        mapa.setZoom(16);
                } else {
                    //alert("Nie można zlokalizować podanego adresu.");
                }
            });
        }

        function lokalizacja_wyszukiwanie_simple(){
            var slat = 0;
            var slng = 0;
            var address = $("#city").val()+' '+$('#street').val();
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    slat = results[0].geometry.location.lat();
                    slng = results[0].geometry.location.lng();

                    $('#lat').val(slat);
                    $('#lng').val(slng);
                }
            });
        }

        var delay = ( function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();


        jQuery.validator.setDefaults({
            errorPlacement: function(error, element) {
                if(element.parent()[0]['nodeName'] == 'LABEL')
                    error.insertAfter(element.parent());
                else
                    error.insertAfter(element);
            }
        });


        $(document).ready(function(){
						btnClick = false;


            $("form#create-form").submit(function(e) {
							if(!btnClick){
                var self = this;
                e.preventDefault();
                var btn = $('#addInjurySubmit');
                btn.attr('disabled', 'disabled');

                if($("form#create-form").valid()){
                    if($('#vehicle_id').val() != ''){
												btnClick=true;
												self.submit();
										}
                    else{
                        $('#modal .modal-content').html(modal_template);
                        $('#modal .modal-title').html('Komunikat');
                        $('#modal .modal-body').html('Proszę wprowadzić dane pojazdu istniejącego w bazie.');
                        $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                        $('#modal').modal('show');
												btnClick = false;
                        btn.removeAttr('disabled');
                    }
                }else{
										btnClick = false;
                    btn.removeAttr('disabled');
                }
								btnClick = false;
							}
							return false; //is superfluous, but I put it here as a fallback
            });

            $('#owner_id').change(function(){
                if($(this).val()!=''){
                    $('.modal-open-lg-special').removeAttr("disabled");
                }
                else
                    $('.modal-open-lg-special').attr('disabled');
            });
            $('.uszkodzenia_check').change(function(){
                if($(this).is(':checked')){
                    $(this).parent().nextAll('td').children('.check_strona').removeAttr('disabled');
                } else {
                    $(this).parent().nextAll('td').children('.check_strona').attr('disabled', 'disabled');
                }
            }).change();

            $('#reported_ic_color .btn').click(function(){
                $('#reported_ic_color .btn').toggleClass('btn-default').toggleClass('btn-primary');
            });
            $('#if_il_repair').on('change', function(){
                if($('#if_il_repair option:selected').val()=='0' || $('#if_il_repair option:selected').val()=='2'){
                    $('#il_repair_info').show();
                }
                else{
                    $('#il_repair_info').hide();
                    $('#il_repair_info_description').hide();
                }
            });
            $('body').on('change','#il_repair_info select',function(){
            	console.log('a');
                if($('#il_repair_info select option:selected').val()=='5'){
                    $('#il_repair_info_description').show();
                }
                else{
                    $('#il_repair_info_description').hide();
                }
            });

            $('#city, #street').keyup(function() {
                delay( function() {
                    lokalizacja_wyszukiwanie_simple();
                }, 100);
            }).focusout(function(){
                lokalizacja_wyszukiwanie_simple();
            });


            $('#mapShow').on('change', function(){

                if($('#mapShow').is(':checked')){
                    $('#map-canvas').show();
                    $('#correctMap').parent().parent().show();
                    initialize();
                    lokalizacja_wyszukiwanie();
                    $('#city, #street').keyup(function() {
                        delay( function() {
                            lokalizacja_wyszukiwanie();
                        }, 500);
                    }).focusout(function(){
                        lokalizacja_wyszukiwanie();
                    });

                    $('#correctMap').on('change', function(){
                        if($('#correctMap').is(':checked')){
                            correlation = 1;
                        }else{
                            correlation = 0;
                        }

                    });

                }else{
                    $('#map-canvas').hide();
                    $('#correctMap').parent().parent().hide();
                }
            });

            $(document).on('click', '#show_exists', function(){
                $('#exists_injuries').toggle(500);
            });
            $(document).on('click', '#show_matched_letters', function(){
                $('#matched_letters').toggle(500);
            });
            $('#time_event').timepicker({
                showMeridian: false,
                defaultTime: false,
            });
            var focus = 0;

            <?php //wyszukiwanie kierowcy?>
            $( "input[name=driver_surname], input[name=driver_name]" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ URL::route('drivers-getList') }}",
                        data: {
                            id_client: $('#client_id').val(),
                            term: request.term,
                            ele: $(this).attr('name'),
                            vehicle_type: $('#vehicle_type').val(),
                            vehicle_id: $('#vehicle_id').val(),
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 1,
                select: function(event, ui) {
                    $('input[name=driver_surname]').val(ui.item.surname);
                    $('input[name=driver_name]').val(ui.item.name);
                    $('input[name=driver_phone]').val(ui.item.phone);
                    $('input[name=driver_email]').val(ui.item.email);
                    $('input[name=driver_city]').val(ui.item.city);
                    $('#driver_id').val(ui.item.id);
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                }
            });

            <?php //wyszukiwanie zgłaszającego?>
            $( "input[name=notifier_surname], input[name=notifier_name]" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ URL::route('drivers-getList') }}",
                        data: {
                            id_client: $('#client_id').val(),
                            term: request.term,
                            ele: $(this).attr('name'),
                            vehicle_type: $('#vehicle_type').val(),
                            vehicle_id: $('#vehicle_id').val(),
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 1,
                select: function(event, ui) {
                    $('input[name=notifier_surname]').val(ui.item.surname);
                    $('input[name=notifier_name]').val(ui.item.name);
                    $('input[name=notifier_phone]').val(ui.item.phone);
                    $('input[name=notifier_email]').val(ui.item.email);
                    $('input[name=notifier_city]').val(ui.item.city);
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                }
            });

            <?php //obsługa przycisku edycji ?>
            $('.edit-radio').click(function(){
                desc = $(this).attr('desc');

                $('input[name='+desc+']').removeAttr('disabled');

                if(desc == 'end_leasing'){
                    $('input[name='+desc+']').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,minDate: "+0D",dateFormat: "yy-mm-dd" }).focus();
                }else if(desc == 'expire'){
                    $('input[name='+desc+']').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true,minDate: "+0D",dateFormat: "yy-mm-dd" }).focus();
                }else{
                    $('input[name='+desc+']').focus();
                    $('select[name='+desc+']').focus();
                }

                //$(this).attr('disabled', 'disabled');

            });

            <?php //kopiowanie danych kierowcy?>
            $('#cp_driver').click(function(){
                $('input[name=notifier_surname]').val($('input[name=driver_surname]').val());
                $('input[name=notifier_name]').val($('input[name=driver_name]').val());
                $('input[name=notifier_phone]').val($('input[name=driver_phone]').val());
                $('input[name=notifier_email]').val($('input[name=driver_email]').val());
                $('input[name=notifier_city]').val($('input[name=driver_city]').val());
            });
            <?php //wykrycie zmian w danych kierowcy?>
            $('input[name=driver_surname], input[name=driver_name], input[name=driver_phone], input[name=driver_email], input[name=driver_city]').keypress(function() {
                $('#driver_id').val('');
            });

            <?php //dane sprawcy?>
            $('#injuries_type').change(function(){
                if($(this).val() == 2 || $(this).val() == 4 || $(this).val() == 5){
                    $('#offender_info').show();
                    $('#modal-offender input, #modal-offender select, #modal-offender textarea').removeAttr('disabled');
                    $('input[name=offender_expire]').datepicker({ showOtherMonths: true, selectOtherMonths: true,  showOtherMonths: true, selectOtherMonths: true, changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
                    $('#modal-offender').modal('show');
                }else{
                    $('#offender_info').hide();
                    $('#modal-offender input, #modal-offender select, #modal-offender textarea').attr('disabled','disabled');
                }
            });

            <?php //edycja danych sprawcy?>
            $('#offender_info').click(function(){
                $('#modal-offender').modal('show');
            });

            <?php //zmiana statusu zawiadomienia policji?>
            $('#police').change(function(){
                if($(this).val() == 1 ){
                    $('#police_nr').removeAttr('disabled').focus();
                    $('#police_unit').removeAttr('disabled');
                    $('#police_contact').removeAttr('disabled');
                }else{
                    $('#police_nr').attr('disabled','disabled');
                    $('#police_unit').attr('disabled','disabled');
                    $('#police_contact').attr('disabled','disabled');
                }
            });

            $('#date_event').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",showOtherMonths: true, selectOtherMonths: true, dateFormat: "yy-mm-dd" });

            <?php //podgląd danych właściciela?>
            $('.show-owner').click(function(){
                hrf='/injuries/owner/show/'+$('#owner_id').val()+'/';

                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(modal_template);
                    $('#modal .modal-title').html('Dane właściciela');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //podgląd danych klienta?>
            $('.show-client').click(function(){
                hrf='/injuries/client/show/'+$('#client_id').val()+'/';

                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(modal_template);
                    $('#modal .modal-title').html('Dane klienta');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //edycja właściciela?>
            $('.edit-owner').click(function(){
                id = $('#owner_id').val();
                if(id == '') id = 0;

                hrf='/injuries/owner/edit/'+id+'/';
                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(modal_template);
                    $('#modal .modal-title').html('Zmiana właściciela pojazdu');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //edycja klienta?>
            $('.edit-client').click(function(){
                id = $('#client_id').val();
                if(id == '') id = 0;

                hrf='/injuries/client/edit/'+id+'/';
                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(modal_template);
                    $('#modal .modal-title').html('Zmiana klienta');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });



            <?php //podgląd danych ubezpieczalni?>
            $('.show-insurance_company').click(function(){
                hrf='/injuries/insurance_company/show/'+$('#insurance_company_id').val()+'/';
                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(data);
                    $('#modal').modal('show');
                });
            });

            <?php //dodawanie ubezpieczalni?>
            $('.add-insurance_company').click(function(){
                var hrf=$(this).attr('target');

                $.get( hrf, function( data ) {
                    $('#modal .modal-content').html(data);
                });
            });

            $('#modal').on('click', '#save', function(){
                $('#insurance_companies-form').validate();
                if($('#insurance_companies-form').valid() ){
                    $.post(
                        $('#insurance_companies-form').prop( 'action' ),
                        $('#insurance_companies-form').serialize(),
                        function( data ) {
                            if(data.code == '0'){
                                var hrf="<?php echo URL::route('insurance_companies-list');?>";

                                $.get( hrf, function( data ) {
                                    $('#modal').modal('hide');
                                    $('#insurance_company_id').html(data);
                                });
                            }else{
                                $('#modal .modal-content').html(data);
                            }
                        },
                        'json'
                    );
                    return false;
                }
            });

            $('body').on('click', '.modal-open-lg-special', function(){
                var hrf=$(this).attr('target')+'/'+$('#owner_id').val();
                $('.modal-open-lg-special').removeAttr("disabled");
                $.get( hrf, function( data ) {
                    $('#modal-lg .modal-content').html(data);
                });
            });
            $('#modal-lg').on('click', '#set-branch-special', function(){
                //    var $btn = $(this).button('loading');
                //  if($('#assign-branch-form').valid()) {
                $('.modal-open-lg-special').removeAttr("disabled");
                if($('#id_warsztat').val())
                    $('#branch_id').val($('#id_warsztat').val());
                else
                    $('#branch_id').val(0);
                if($('#dont_send_sms').is(':checked'))
                    $('#branch_dont_send_sms').val(1);
                else
                    $('#branch_dont_send_sms').val(0);
                $('#branch_text').text('Przypisany serwis');
                $('#branch_data').html($('#data_warsztat').html());
                $('#branch_data').show();
                $('#branch_text').show();
                $('#modal-lg').modal("hide");
                return false;
            });


            $('input[name="dont_send_sms"]').on('click', function(){
                if( $(this).is(':checked') ){
                    $('input[name="dont_send_sms"]').prop('checked', true);
                }else{
                    $('input[name="dont_send_sms"]').prop('checked', false);
                }
            });
        });



	</script>

@stop
