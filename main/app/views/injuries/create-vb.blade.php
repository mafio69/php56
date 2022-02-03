@extends('layouts.main')

@section('styles')
    @parent
@stop

@section('header')

    Dodawanie nowej szkody

@stop

@section('main')

    <form action="{{ URL::route('injuries.vb.post', ['store'] ) }}" method="post" role="form">
        <div class="row marg-btm">
            <div class="pull-right">
                <a href="{{ URL::route('injuries-new' ) }}" class="btn btn-default">Anuluj</a>
            </div>
            @if($vehicle->if_vip == 1 || $is_vip)
                <h3 class="text-center">
                    <span class="text-warning marg-top-min" id="vip_client">
				        <i class="fa fa-star"></i> klient VIP
			        </span>
                </h3>
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
        @if(!$vehicle_info)
            <div class="jumbotron text-center">
                <h3>Wykryto błąd w zawartości lub strukturze pliku.</h3>
                <p>Skontaktuj się z administratorem: <a href="mailto:biuro@ebusters.pl">biuro@ebusters.pl</a></p>
            </div>
        @else
            <div class="row">
                <div class="form-group">
                    <h4 class="inline-header"><span>Zgłoszenie szkody:</span></h4>
                    <div class="row injury_bg ">
                        <div class="col-md-4 col-lg-3 marg-btm tips" title= 'Rejestracja'>
                            {{ Form::text('registration', $vehicle->registration, array('class' => 'form-control required upper', 'id' => 'registration', 'placeholder' => 'Rejestracja', 'tabindex' => '1', 'readonly'))  }}
                        </div>

                        <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy leasingowej'>
                            {{ Form::text('nr_contract', $vehicle->nr_contract, array('class' => 'form-control  required upper', 'id' => 'nr_contract', 'placeholder' => 'Nr umowy leasingowej', 'readonly'))  }}
                        </div>

                        <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy VB Leasing'>
                            {{ Form::text('nr_vb', $vehicle->nr_vb, array('class' => 'form-control  upper', 'id' => 'nr_vb', 'placeholder' => 'Nr umowy VB Leasing', 'readonly'))  }}
                        </div>

                        <div class="col-md-4 col-lg-3 " >
                            <button type="button" class="btn btn-warning btn-sm btn-block hidden marg-btm " id="show_exists">W systemie istnieją już zgłoszenia dla tego pojazdu <span class="badge" id="num_exists"></span></button>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <span class="btn btn-info btn-sm hidden btn-block  marg-btm" disabled="disabled" id="liqudation_card"></span>
                        </div>
                        <div class="col-md-4 col-lg-3 " >
                            <button type="button" class="btn btn-info btn-sm btn-block hidden marg-btm " id="show_matched_letters">W systemie istnieją pisma dopasowane do pojazdu <span class="badge" id="num_matched"></span></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-md-offset-2 marg-btm " style="display:none;" id="exists_injuries" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-md-offset-2 marg-btm " style="display:none;" id="matched_letters" >
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <h4 class="inline-header"><span>Dane identyfikacyjne pojazdu:</span></h4>
                    <div class="row">
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Nr VIN z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="VIN" value="{{ $vbCarInfo['VIN'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['VIN']}}"/>
                                </label>
                                @if($vbCarInfo['brand'] != $vehicle->brand)
                                    <label class="input-group tips focus-change" title="Nr VIN z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="VIN" value="{{ $vehicle->VIN }}">
                                    </span>
                                        <input class="form-control" value="{{ $vehicle->VIN }}" disabled/>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Marka z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="brand" value="{{ $vbCarInfo['brand'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['brand'] }}"/>
                                </label>
                                @if($vbCarInfo['brand'] != $vehicle->brand)
                                    <label class="input-group tips focus-change" title="Marka z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="brand" value="{{ $vehicle->brand }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->brand }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Model z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="model" value="{{ $vbCarInfo['model'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['model'] }}"/>
                                </label>
                                @if($vbCarInfo['model'] != $vehicle->model)
                                    <label class="input-group tips focus-change" title="Model z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="model" value="{{ $vehicle->model }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->model }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Silnik z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="engine" value="{{ $vbCarInfo['engine'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['engine'] }}"/>
                                </label>
                                @if($vbCarInfo['engine'] != $vehicle->engine)
                                    <label class="input-group tips focus-change" title="Silnik z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="engine" value="{{ $vehicle->engine }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->engine }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Rok produkcji z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="year_production" value="{{ $vbCarInfo['year_production'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['year_production'] }}"/>
                                </label>
                                @if($vbCarInfo['year_production'] != $vehicle->year_production)
                                    <label class="input-group tips focus-change" title="Rok produkcji z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="year_production" value="{{ $vehicle->year_production }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->year_production }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Data pierwszej rejestracji z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="first_registration" value="{{ $vbCarInfo['first_registration'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" id="first_registration" value="{{ $vbCarInfo['first_registration'] }}"/>
                                </label>
                                @if($vbCarInfo['first_registration'] != $vehicle->first_registration)
                                    <label class="input-group tips focus-change" title="Data pierwszej rejestracji z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="first_registration" value="{{ $vehicle->first_registration }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->first_registration }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Przebieg'>
                            {{ Form::text('mileage', $vehicle->mileage, array('class' => 'form-control upper', 'id' => 'mileage', 'placeholder' => 'Przebieg'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 editable marg-btm">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="cfm" id="cfm" value="1"
                                           @if($vehicle->cfm == 1)
                                           checked
                                            @endif
                                    > CFM
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <h4 class="inline-header"><span>Dane właściciela i klienta:</span></h4>
                    <div class="row">
                        <div class="col-md-6 col-lg-6 editable marg-btm">
                            <div class="form-group @if($vehicle_info['existing'] == 1) has-error @endif">
                                <label class="input-group  marg-btm" id="owner_idea">
                                    {{ Form::select('owner_id', $owners, ($vehicle_info['existing'] == 1) ? $vehicle->owner_id : '', ['class' => 'form-control tips required', 'id' =>'owner_id', 'required'])}}
                                    <span class="input-group-btn">
                                        <button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-owner"  type="button" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 editable marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Klient z serwera VB Leasing" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="client_id" value="{{ $vbCarInfo['client']['id'] }}" checked>
                                    </span>
                                    <p class="form-control tips required" title="Klient" disabled="true" name="client">{{ $vbCarInfo['client']['name'] }}</p>
                                    <span class="input-group-btn">
                                        <button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-client" type="button" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
                                    </span>
                                </label>
                                @if($vbCarInfo['client']['id'] != $vehicle->client_id)
                                    <label class="input-group tips focus-change" title="Klient z systemu" >
                                        <span class="input-group-addon">
                                            <input type="radio" name="client_id" value="{{ $vehicle->client_id }}">
                                        </span>
                                        <p class="form-control tips required" title="Klient" disabled="true" name="client">{{ $vehicle->client->name }}</p>
                                        <span class="input-group-btn">
                                            <button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-client" type="button" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
                                        </span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <h4 class="inline-header"><span>Status umowy:</span></h4>
                    <div class="row">
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Data końca leasingu z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="end_leasing" value="{{ $vbCarInfo['end_leasing'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" id="end_leasing" value="{{ $vbCarInfo['end_leasing'] }}"/>
                                </label>
                                @if($vbCarInfo['end_leasing'] != $vehicle->end_leasing)
                                    <label class="input-group tips focus-change" title="Data końca leasingu z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="end_leasing" value="{{ $vehicle->end_leasing }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->end_leasing }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Status umowy z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="contract_status" value="{{ $vbCarInfo['contract_status'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['contract_status'] }}"/>
                                </label>
                                @if($vbCarInfo['contract_status'] != $vehicle->contract_status)
                                    <label class="input-group tips focus-change" title="Status umowy z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="contract_status" value="{{ $vehicle->contract_status }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->contract_status }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            <div class="form-group">
                                <div class="checkbox ">
                                    <label>
                                        <input type="checkbox" name="if_vip" id="if_vip" value="1"
                                        @if($vehicle->if_vip == 1 || $is_vip)
                                            checked
                                        @endif
                                        > Klient VIP
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <h4 class="inline-header"><span>Polisa ubezpieczeniowa:</span></h4>
                    <div class="row">
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                {{ Form::text('insurance_company_name', $vbCarInfo['insurance_company_name'], array('id'=>'insurance_company_name', 'class' => 'form-control upper tips', 'placeholder' => 'Zakład ubezpieczeń', 'title' => 'Nazwa zakładu ubezpieczeń', 'readonly'))  }}
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Data ważności polisy z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="expire" value="{{ $vbCarInfo['expire'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" id="expire" value="{{ $vbCarInfo['expire'] }}" placeholder="Data ważności polisy"/>
                                </label>
                                @if($vbCarInfo['expire'] != $vehicle->expire)
                                    <label class="input-group tips focus-change" title="Data ważności polisy z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="expire" value="{{ $vehicle->expire }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->expire }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Nr polisy z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="nr_policy" value="{{ $vbCarInfo['nr_policy'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['nr_policy'] }}" placeholder="Nr polisy z serwera"/>
                                </label>
                                @if($vbCarInfo['nr_policy'] != $vehicle->nr_policy)
                                    <label class="input-group tips focus-change" title="Nr polisy z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="nr_policy" value="{{ $vehicle->nr_policy }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->nr_policy }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                {{ Form::text('insurance', $vehicle->insurance, array('id'=>'insurance', 'class' => 'form-control tips upper',  'placeholder' => 'Suma ubezpieczenia [zł]', 'title' => 'Suma ubezpieczenia [zł]'))  }}
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Wkład własny [zł] z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="contribution" value="{{ $vbCarInfo['contribution'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['contribution'] }}" placeholder="Wkład własny [zł]"/>
                                </label>
                                @if($vbCarInfo['contribution'] != $vehicle->contribution)
                                    <label class="input-group tips focus-change" title="Wkład własny [zł] z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="contribution" value="{{ $vehicle->contribution }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->contribution }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="[netto/brutto] z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="netto_brutto" value="{{ $vbCarInfo['netto_brutto'] }}" checked>
                                </span>
                                    <select class="form-control group-choice-select" >
                                        <option value="1"
                                                @if($vbCarInfo['netto_brutto'] == 1)
                                                selected
                                                @endif
                                        >netto</option>
                                        <option value="2"
                                                @if($vbCarInfo['netto_brutto'] == 2)
                                                selected
                                                @endif
                                        >brutto</option>
                                    </select>
                                </label>
                                @if($vbCarInfo['netto_brutto'] != $vehicle->netto_brutto)
                                    <label class="input-group tips focus-change" title="[netto/brutto] z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="netto_brutto" value="{{ $vehicle->netto_brutto }}">
                                    </span>
                                        <p class="form-control">
                                            @if($vehicle->netto_brutto == 1)
                                                netto
                                            @else
                                                brutto
                                            @endif
                                        </p>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Assistance [tak/nie] z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="assistance" value="{{ $vbCarInfo['assistance'] }}" checked>
                                </span>
                                    <select class="form-control group-choice-select" >
                                        <option value="0"
                                                @if($vbCarInfo['assistance'] == 0)
                                                selected
                                                @endif
                                        >nie</option>
                                        <option value="1"
                                                @if($vbCarInfo['assistance'] == 1)
                                                selected
                                                @endif
                                        >tak</option>
                                    </select>
                                </label>
                                @if($vbCarInfo['assistance'] != $vehicle->assistance)
                                    <label class="input-group tips focus-change" title="Assistance [tak/nie] z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="assistance" value="{{ $vehicle->assistance }}">
                                    </span>
                                        <p class="form-control">
                                            @if($vehicle->assistance == 0)
                                                nie
                                            @else
                                                tak
                                            @endif
                                        </p>
                                    </label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3  marg-btm">
                            <div class="form-group">
                                <label class="input-group tips" title="Nazwa pakietu Assistance z serwera VB Leasing" >
                                <span class="input-group-addon">
                                    <input type="radio" name="assistance_name" value="{{ $vbCarInfo['assistance_name'] }}" checked>
                                </span>
                                    <input class="form-control group-choice-input" value="{{ $vbCarInfo['assistance_name'] }}" placeholder="Nazwa pakietu Assistance"/>
                                </label>
                                @if($vbCarInfo['assistance_name'] != $vehicle->assistance_name)
                                    <label class="input-group tips focus-change" title="Nazwa pakietu Assistance z systemu" >
                                    <span class="input-group-addon">
                                        <input type="radio" name="assistance_name" value="{{ $vehicle->assistance_name }}">
                                    </span>
                                        <p class="form-control">{{ $vehicle->assistance_name }}</p>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <h4 class="inline-header"><span>Dane kierowcy:</span></h4>
                    <div class="row injury_bg">
                        <div class="col-md-4 col-lg-3 marg-btm">
                            {{ Form::text('driver_name', '', array('class' => 'form-control bold tips upper', 'placeholder' => 'imię',  'title' => 'Imię kierowcy'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            {{ Form::text('driver_surname', '', array('class' => 'form-control bold tips upper', 'placeholder' => 'nazwisko',  'title' => 'Nazwisko kierowcy'))  }}
                        </div>

                        <div class="col-md-4 col-lg-3 marg-btm">
                            {{ Form::text('driver_phone', '', array('class' => 'form-control tips bold upper', 'placeholder' => 'telefon', 'title' => 'Telefon kierowcy'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            {{ Form::text('driver_email', '', array('class' => 'form-control tips bold email ', 'placeholder' => 'email',  'title' => 'Email kierowcy'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm">
                            {{ Form::text('driver_city', '', array('class' => 'form-control tips bold upper', 'placeholder' => 'miasto',  'title' => 'Miasto kierowcy'))  }}
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
                            {{ Form::text('notifier_name', '', array('class' => 'form-control bold   upper', 'placeholder' => 'imię'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nazwisko zgłaszającego'>
                            {{ Form::text('notifier_surname', '', array('class' => 'form-control bold  upper', 'placeholder' => 'nazwisko'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm tips"  title ='Telefon zgłaszającego'>
                            {{ Form::text('notifier_phone', '', array('class' => 'form-control bold  upper', 'placeholder' => 'telefon'))  }}
                        </div>
                        <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Email zgłaszającego'>
                            {{ Form::text('notifier_email', '', array('class' => 'form-control bold email', 'placeholder' => 'email'))  }}
                        </div>
                    </div>
                    <div class="row injury_bg">
                        <div class="col-md-4 col-lg-3 marg-btm" >
                            <div class="radio">
                                <label class="tips" title ='Zgłaszający jest osobą kontaktową'>
                                    {{ Form::checkbox('contact_person', '2'); }}
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
                            {{ Form::text('date_event', '', array('class' => 'form-control bold required', 'id'=>'date_event', 'placeholder' => 'data zdarzenia'))  }}
                        </div>
                        <div class="col-md-4 col-lg-4 marg-btm">
                            <label >Godzina zdarzenia:</label>
                            {{ Form::text('time_event', '', array('class' => 'form-control bold', 'id'=>'time_event', 'placeholder' => 'godzina zdarzenia'))  }}
                        </div>
                    </div>


                    <div class="row injury_bg">
                        <div class="col-md-12 marg-btm">
                            <label >Miejsce zdarzenia:</label>
                        </div>
                        <div class="col-md-4 col-lg-4 marg-btm">
                            {{ Form::text('event_city', '', array('class' => 'form-control bold  upper', 'id' => 'city', 'placeholder' => 'miasto'))  }}
                        </div>
                    <!--
                    <div class="col-md-4 col-lg-4 marg-btm">
                    {{ Form::text('event_post', '', array('class' => 'form-control  ', 'id' => 'code', 'placeholder' => 'kod pocztowy'))  }}
                            </div>
                            -->
                        <div class="col-md-4 col-lg-4 marg-btm">
                            {{ Form::text('event_street', '', array('class' => 'form-control bold  upper', 'id' => 'street', 'placeholder' => 'ulica'))  }}
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
                                        <input type="radio" name="zdarzenie" id="zdarzenie{{ $v->id }}" value="{{ $v->id }}" >
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
                        {{ Form::textarea('remarks', '', array('class' => 'form-control bold ', 'placeholder' => 'Opis szkody'))  }}
                    </div>
                </div>

            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Uszkodzenia: <button type="button" class="btn btn-primary btn-xs"  data-toggle="collapse" data-target="#faults" aria-expanded="false" aria-controls="faults"> oznacz szczegółowe uszkodzenia</button></span></h4>
                <div class="row injury_bg collapse" id="faults">
                    @include('injuries.create_damage_part')
                </div>
            </div>
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
                                    <input type="radio" name="reported_ic" id="reported_ic" autocomplete="off" value="1"> TAK
                                </label>
                                <label class="btn btn-primary active">
                                    <input type="radio" name="reported_ic" id="reported_ic" autocomplete="off" checked value="0"> NIE
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <label >Typ szkody: <button type="button" class="btn btn-primary btn-xs" id="offender_info" data-toggle="modal" data-target="#modal-offender" style="display:none;"> dane sprawcy</button></label>
                        <select name="injuries_type" id="injuries_type" class="form-control required" >
                            <option value="">---wybierz---</option>
                            <?php foreach($injuries_type as $k => $v){
                                echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                            }?>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <label >Nr szkody:</label>
                        {{ Form::text('injury_nr', '', array('class' => 'form-control upper bold', 'id'=>'injury_nr', 'placeholder' => 'nr szkody'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <label >Zakład ubezpieczeń:</label>
                        <span class="input-group  " >

                            <select name="insurance_company_id" id="insurance_company_id" class="form-control required" >
                                <option value="">---wybierz---</option>
                                <?php foreach($insurance_companies as $k => $v){
                                    echo '<option value="'.$v->id.'">'.$v->name.'</option>';
                                }?>
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
                        <select name="receives" id="receives" class="form-control " >
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
                </div>
                <div class="row injury_bg">
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <label >Zawiadomiono policję:</label>
                        <select name="police" id="police" class="form-control required" >
                            <option value="-1" selected>nie ustalono</option>
                            <option value="0">nie</option>
                            <option value="1">tak</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('police_nr', '', array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_nr', 'disabled' => 'disabled', 'placeholder' => 'nr zgłoszenia policji', 'title' => 'nr zgłoszenia policji'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('police_unit', '', array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_unit', 'disabled' => 'disabled', 'placeholder' => 'jednostka policji', 'title' => 'jednostka policji' ))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('police_contact', '', array('class' => 'form-control tips marg-top upper bold', 'id'=>'police_contact', 'disabled' => 'disabled', 'placeholder' => 'kontakt z policją', 'title' => 'kontakt z policją'))  }}
                    </div>
                </div>
                <div class="row injury_bg">
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="if_statement" value="1">
                                Spisano oświadczenia
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="if_registration_book " value="1">
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
                                <input type="checkbox" name="if_towing" value="1">
                                Wymaga holowania
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="if_courtesy_car" value="1">
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
                            <option value="0">nie</option>
                            <option value="1">tak</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <label>Naprawa w sieci IL:</label>
                        <select name="if_il_repair" class="form-control" id="if_il_repair" required>
                            <option value="" selected>---wybierz---</option>
                            <option value="-1">nie ustalono</option>
                            <option value="0">nie</option>
                            <option value="1">tak</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm" style="display:none;" id="il_repair_info">
                        <label>Przyczyna:</label>
                        <select name="il_repair_info" class="form-control required">
                            <option value="">---wybierz---</option>
                            @foreach(RepairInformation::lists('name', 'id') as $key=>$value)
                                <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm"  style="display:none;" id="il_repair_info_description">
                        {{ Form::text('il_repair_info_description', '', array('class' => 'form-control tips marg-top', 'placeholder' => 'opis', 'title' => 'opis'))  }}
                    </div>
                </div>
                <div class="row injury_bg">
                    <div class="center-block" style="text-align:center">
                        <h4 id="branch_text" style="display:none"></h4>
                        <div id="branch_data"  style="display:none"></div>
                        <a href="#" target="{{ URL::route('injuries-assignCompany', array('all')) }}" class="modal-open-lg-special btn btn-primary" data-toggle="modal" data-target="#modal-lg">przypisz serwis</a>
                    </div>
                </div>
                <div class="row injury_bg marg-btm">
                    <div class="col-md-12 marg-btm">
                        <label >Informacja wewnętrzna:</label>
                        {{ Form::textarea('info', '', array('class' => 'form-control  bold', 'placeholder' => 'Informacja wewnętrzna'))  }}
                    </div>
                </div>
            </div>


            {{Form::token()}}
            {{Form::hidden('lat', '', array('id' => 'lat'))}}
            {{Form::hidden('lng', '', array('id' => 'lng'))}}
            {{Form::hidden('vehicle_id', $vehicle->id, array('id' => 'vehicle_id'))}}
            {{Form::hidden('driver_id', '', array('id' => 'driver_id'))}}
            {{Form::hidden('branch_id', 0, array('id' => 'branch_id'))}}
            {{Form::hidden('branch_dont_send_sms', '', array('id' => 'branch_dont_send_sms'))}}


            <?php //adm-administator, inf-infolinia?>
            {{Form::hidden('insert_role', 'adm')}}

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
            </div>
            @endif

    </form>

    <div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>



    @stop

    @section('headerJs')
    @parent
    <script type="text/javascript" >

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
            $("form").submit(function(e) {
                var self = this;
                e.preventDefault();
                btn = $('#addInjurySubmit');
                btn.attr('disabled', 'disabled');

                if($("form").valid()){
                    self.submit();
                }else{
                    btn.removeAttr('disabled');
                }

                return false; //is superfluous, but I put it here as a fallback
            });

            {{-- sprawdzenie czy istnieje szkody na ten pojazd w systemie --}}
            $.ajax({
                url: "<?php echo  URL::route('vehicle-check-injuries');?>",
                data: {
                    vehicle_id: $('#vehicle_id').val(),
                    vehicle_type: 'Vehicles',
                    _token: $('input[name="_token"]').val()
                },
                async: false,
                cache: false,
                type: "POST",
                success: function( data ) {
                    data = tryParseJSON( data );
                    if(data.exists == 1){
                        $('#num_exists').html(data.count);
                        $('#show_exists').removeClass('hidden').addClass('show');
                        $('#exists_injuries').html(data.dataHtml);
                    }else{
                        $('#num_exists').html('0');
                        $('#show_exists').addClass('hidden').removeClass('show');
                        $('#exists_injuries').html('');
                    }
                }
            });

            {{-- sprawdzenie czy istnieje karta likwidacji szkód w systemie --}}
            $.ajax({
                url: "<?php echo  URL::route('vehicle-check-liquidationCard');?>",
                data: {
                    vehicle_id: $('#vehicle_id').val(),
                    vehicle_type: 'Vehicles',
                    _token: $('input[name="_token"]').val()
                },
                async: false,
                cache: false,
                type: "POST",
                success: function( data ) {
                    data = tryParseJSON( data );
                    if(data.exists == 1){
                        $('#liqudation_card').removeClass('hidden').addClass('show').html(data.dataHtml);
                    }else{
                        $('#liqudation_card').removeClass('show').addClass('hidden').html('');
                    }
                }
            });

            {{-- sprawdzenie czy istnieja pasujace pisma --}}
            $.ajax({
                url: "{{ URL::route('routes.post', ['injuries', 'getMatchedLetters']) }}",
                data: {
                    vehicle_id: $('#vehicle_id').val(),
                    vehicle_type: 'Vehicles',
                    _token: $('input[name="_token"]').val()
                },
                async: false,
                cache: false,
                type: "POST",
                dataType: "json",
                success: function( data ) {
                    if(data.matched == 1){
                        $('#num_matched').html(data.count);
                        $('#show_matched_letters').removeClass('hidden').addClass('show');
                        $('#matched_letters').html(data.dataHtml);
                    }else{
                        $('#num_matched').html('0');
                        $('#show_matched_letters').addClass('hidden').removeClass('show');
                        $('#matched_letters').html('');
                    }
                }
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
            $('#if_il_repair').change(function(){
                if($('#if_il_repair option:selected').val()=='0'){
                    $('#il_repair_info').show();
                }
                else{
                    $('#il_repair_info').hide();
                    $('#il_repair_info_description').hide();
                }
            });
            $('body').on('change','#il_repair_info select',function(){
                if($('#il_repair_info select option:selected').val()=='5'){
                    $('#il_repair_info_description').show();
                }
                else{
                    $('#il_repair_info_description').hide();
                }
            });

            $('#time_event').timepicker({
                showMeridian: false,
                defaultTime: false,
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

            $('.group-choice-input').on('keyup', function(){
                $(this).parent().parent().find('span').find('input').val($(this).val());
            });
            $('.group-choice-select').on('change', function(){
                $(this).parent().parent().find('span').find('input').val($(this).val());
            });
            $('#first_registration').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });
            $('#end_leasing').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
            $('#expire').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,dateFormat: "yy-mm-dd" });
            <?php //wyszukiwanie kierowcy?>
            $( "input[name=driver_surname], input[name=driver_name]" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "<?php echo  URL::route('drivers-getList');?>",
                        data: {
                            id_client: $('#client_id').val(),
                            term: request.term,
                            ele: $(this).attr('name'),
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
                        url: "<?php echo  URL::route('drivers-getList');?>",
                        data: {
                            id_client: $('#client_id').val(),
                            term: request.term,
                            ele: $(this).attr('name'),
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
                    $('input[name='+desc+']').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,minDate: "+0D",dateFormat: "yy-mm-dd" }).focus();
                }else if(desc == 'expire'){
                    $('input[name='+desc+']').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,minDate: "+0D",dateFormat: "yy-mm-dd" }).focus();
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
                    $('input[name=offender_expire]').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd" });
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

            $('#date_event').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm-dd" });

            <?php //podgląd danych właściciela?>
            $('.show-owner').click(function(){
                var hrf='/injuries/owner/show/'+$('#owner_id').val()+'/';

                $.get( hrf, function( data ) {
                    $('#modal .modal-title').html('Dane właściciela');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //podgląd danych klienta?>
            $('.show-client').click(function(){
                console.log($('input[name="client_id"]:checked').val());
                hrf='/injuries/client/show/'+$('input[name="client_id"]:checked').val()+'/';

                $.get( hrf, function( data ) {
                    $('#modal .modal-title').html('Dane klienta');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });
            <?php //edycja właściciela?>
            $('.edit-owner').click(function(){
                var id = $('#owner_id').val();
                if(id == '') id = 0;

                var hrf='/injuries/owner/edit/'+id+'/';
                $.get( hrf, function( data ) {
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
                    $('#modal .modal-title').html('Zmiana klienta');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                });
            });



            <?php //podgląd danych ubezpieczalni?>
            $('.show-insurance_company').click(function(){
                hrf='/injuries/insurance_company/show/'+$('#insurance_company_id').val()+'/';

                $.get( hrf, function( data ) {
                    $('#modal .modal-title').html('Dane ubezpieczalni');
                    $('#modal .modal-body').html(data);
                    $('#modal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>');
                    $('#modal').modal('show');
                });
            });

            <?php //dodawanie ubezpieczalni?>
            $('.add-insurance_company').click(function(){
                hrf=$(this).attr('target');

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

            // $('#cfm').on('change', function(){
            //     if($('#owner_id').val() != '') {
            //         if ($("#cfm").is(':checked'))
            //             $('#owner_id option[value="10"]').prop('selected', true);
            //     }
            // }).change();

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
