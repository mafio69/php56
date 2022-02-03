@extends('layouts.main')

@section('header')

    Dodawanie nowej szkody

@stop

@section('main')
    <form action="{{ URL::route('injuries-post-create' ) }}" method="post" role="form" id="create-form">
        <div class="row marg-btm">
            <h3 class="text-center">
                <span class="text-danger" id="vmanage_company_name" style="display:none;"></span>
                <span class="text-warning marg-top-min" id="vip_client" style="display:none;">
				<i class="fa fa-star"></i> klient VIP
			</span>
                <div class="pull-right">
                    <a href="{{ URL::route('injuries-new' ) }}" class="btn btn-default">Anuluj</a>
                </div>
            </h3>
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
            <div class="form-group">
                <h4 class="inline-header"><span>Zgłoszenie szkody:</span></h4>
                <div class="row injury_bg ">
                    <div class="col-md-4 col-lg-3 marg-btm tips" title= 'Rejestracja'>
                        {{ Form::text('registration', '', array('class' => 'form-control  required upper', 'id' => 'registration', 'placeholder' => 'Rejestracja', 'tabindex' => '1'))  }}
                    </div>

                    <div class="col-md-4 col-lg-3 marg-btm tips" title = 'Nr umowy leasingowej'>
                        {{ Form::text('nr_contract', '', array('class' => 'form-control  required upper', 'id' => 'nr_contract', 'placeholder' => 'Nr umowy leasingowej'))  }}
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
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group ">
                            <label class="input-group  " id="vin_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="vin_grp" value="0" checked>
							    </span>
                                <input class="form-control tips required upper" title="VIN z serwera IL" disabled="true" name="vin" placeholder="VIN" tabindex="2"/>
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="vin" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="VIN z systemu" id="vin_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="vin_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  " id="brand_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="brand_grp" value="0" checked>
							    </span>
                                <input class="form-control tips required upper" title="Marka z serwera IL" disabled="true" name="brand" placeholder="marka"/>
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="brand" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="Marka z systemu" id="brand_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="brand_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  " id="model_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="model_grp" value="0" checked>
							    </span>
                                <input class="form-control tips required upper" title="Model z serwera IL" disabled="true" name="model" placeholder="model"/>
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="model" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>

                            </label>
                            <label class="input-group tips" title="Model z systemu" id="model_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="model_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            {{ Form::text('engine', '', array('id'=>'engine', 'class' => 'form-control upper tips', 'disabled'=>'disabled', 'placeholder' => 'silnik', 'title' => 'Dane silnika'))  }}
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  " id="year_production_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="year_production_grp" value="0" checked>
							    </span>
                                <input class="form-control tips required upper" title="Rok produkcji z serwera IL" disabled="true" name="year_production" placeholder="rok produkcji"/>
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="year_production" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>

                            </label>
                            <label class="input-group tips" title="Rok produkcji z systemu" id="year_production_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="year_production_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            {{ Form::text('first_registration', '', array('id'=>'first_registration', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'data pierwszej rejestracji', 'title' => 'Data pierwszej rejestracji'))  }}
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            {{ Form::text('mileage', '', array('id'=>'mileage', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'przebieg', 'title' => 'Przebieg'))  }}

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="cfm" id="cfm" value="1" disabled> CFM
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Dane właściciela i klienta:</span></h4>
                <div class="row">
                    <div class="col-md-6 col-lg-6 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="owner_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="owner_grp" value="0" checked>
							    </span>
                                <p class="form-control tips required" title="Właściel z serwera IL" disabled="true" name="owner">Właściel</p>
                                <span class="input-group-btn">
						        	<button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-owner"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="Właściciel z systemu" id="owner_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="owner_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                                <span class="input-group-btn">
						        	<button class="btn btn-default show-owner"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
						      	</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="client_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="client_grp" value="0" checked>
							    </span>
                                <p class="form-control tips required" title="Klient z serwera IL" disabled="true" name="client">Klient</p>
                                <span class="input-group-btn">
						        	<button style="border-radius:0px;margin-left: -1px;" class="btn btn-default show-client"  type="button" disabled="disabled" data-toggle="modal" data-target="#modal"><span class="fa fa-search"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="Klient z systemu" id="client_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="client_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                                <span class="input-group-btn">
						        	<button class="btn btn-default show-client"  type="button" disabled="disabled"><span class="fa fa-search" data-toggle="modal" data-target="#modal"></span></button>
						      	</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Status umowy:</span></h4>
                <div class="row">
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="end_leasing_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="end_leasing_grp" value="0" checked>
							    </span>
                                <input class="form-control tips required upper" title="Data końca leasingu z serwera IL" disabled="true" name="end_leasing_desc" placeholder="Data końca leasingu"/>
                                <input type="hidden" name="end_leasing"/>
                            </label>
                            <label class="input-group tips" title="Data końca leasingu z systemu" id="end_leasing_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="end_leasing_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>

                        </div>

                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="contract_status_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="contract_status_grp" value="0" checked>
							    </span>
                                <input class="form-control tips required upper" title="Status umowy z serwera IL" disabled="true" name="contract_status_desc" placeholder="Status umowy" />
                                <input type="hidden" name="contract_status"/>
                            </label>
                            <label class="input-group tips" title="Status umowy z systemu" id="contract_status_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="contract_status_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        <div class="form-group">
                            <div class="checkbox ">
                                <label>
                                    <input type="checkbox" name="if_vip" id="if_vip" value="1" disabled> Klient VIP
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
                        <div class="form-group">
                            {{ Form::text('insurance_company_name', '', array('id'=>'insurance_company_name', 'class' => 'form-control upper tips', 'disabled'=>'disabled', 'placeholder' => 'Zakład ubezpieczeń', 'title' => 'Nazwa zakładu ubezpieczeń z serwera IL'))  }}
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="expire_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="expire_grp" value="0" checked>
							    </span>
                                <input class="form-control tips upper" title="Data ważności polisy z serwera IL" disabled="true" name="expire" placeholder="Data ważności polisy" />
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="expire" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="Data ważności polisy z systemu" id="expire_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="expire_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="nr_policy_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="nr_policy_grp" value="0" checked>
							    </span>
                                <input class="form-control tips upper" title="Nr polisy z serwera IL" disabled="true" name="nr_policy" placeholder="Nr polisy" />
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="nr_policy" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="Nr polisy z systemu" id="nr_policy_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="nr_policy_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            {{ Form::text('insurance', '', array('id'=>'insurance', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'Suma ubezpieczenia [zł]', 'title' => 'Suma ubezpieczenia [zł]'))  }}
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            {{ Form::text('contribution', '', array('id'=>'contribution', 'class' => 'form-control tips upper', 'disabled'=>'disabled', 'placeholder' => 'Wkład własny [zł]', 'title' => 'Wkład własny [zł]'))  }}

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <select name="netto_brutto" class="form-control tips" title="[netto/brutto]" disabled="true">
                                <option value="1" selected>netto</option>
                                <option value="2" >brutto</option>
                                <option value="3">netto +50%</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="assistance_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="assistance_grp" value="0" checked>
							    </span>
                                <select name="assistance" class="form-control tips" title="Assistance [tak/nie]" disabled="true">
                                    <option value="0" selected>nie</option>
                                    <option value="1" >tak</option>
                                </select>
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="assistance" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>

                            </label>
                            <label class="input-group tips" title="Assistance [tak/nie]" id="assistance_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="assistance_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3 editable marg-btm">
                        <div class="form-group">
                            <label class="input-group  marg-btm" id="assistance_name_idea">
								<span class="input-group-addon">
							    	<input type="radio" name="assistance_name_grp" value="0" checked>
							    </span>
                                <input class="form-control tips upper" title="Nazwa pakietu Assistance z serwera IL" disabled="true" name="assistance_name" placeholder="Nazwa pakietu Assistance" />
                                <span class="input-group-btn">
						        	<button class="btn btn-default edit-radio" desc="assistance_name" type="button" disabled="disabled"><span class="fa fa-pencil-square-o"></span></button>
						      	</span>
                            </label>
                            <label class="input-group tips" title="Nazwa pakietu Assistance z systemu" id="assistance_name_system" style="display:none;">
								<span class="input-group-addon">
							    	<input type="radio" name="assistance_name_grp" value="1">
							    </span>
                                <p class="form-control"></p>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h4 class="inline-header"><span>Dane kierowcy:</span></h4>
                <div class="row injury_bg">
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('driver_name', '', array('class' => 'form-control bold tips upper', 'placeholder' => 'imię', 'disabled' => 'disabled', 'title' => 'Imię kierowcy'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('driver_surname', '', array('class' => 'form-control bold tips upper', 'placeholder' => 'nazwisko', 'disabled' => 'disabled', 'title' => 'Nazwisko kierowcy'))  }}
                    </div>

                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('driver_phone', '', array('class' => 'form-control tips bold upper', 'placeholder' => 'telefon', 'disabled' => 'disabled', 'title' => 'Telefon kierowcy'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('driver_email', '', array('class' => 'form-control tips bold email ', 'placeholder' => 'email', 'disabled' => 'disabled', 'title' => 'Email kierowcy'))  }}
                    </div>
                    <div class="col-md-4 col-lg-3 marg-btm">
                        {{ Form::text('driver_city', '', array('class' => 'form-control tips bold upper', 'placeholder' => 'miasto', 'disabled' => 'disabled', 'title' => 'Miasto kierowcy'))  }}
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
                    <div class="col-md-4 col-lg-3 marg-btm " >
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
                                    <input type="radio" name="reported_ic" id="reported_ic" autocomplete="off" value="1" required> TAK
                                </label>
                                <label class="btn btn-primary active">
                                    <input type="radio" name="reported_ic" id="reported_ic" autocomplete="off" checked value="0" required> NIE
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
                        <a href="#" target="{{ URL::route('injuries-assignCompany', array('all')) }}" class="modal-open-lg-special btn btn-primary" data-toggle="modal" data-target="#modal-lg"  disabled="disabled">przypisz serwis</a>
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
            {{Form::hidden('vehicle_id', '', array('id' => 'vehicle_id'))}}
            {{Form::hidden('vehicle_type', '', array('id' => 'vehicle_type'))}}
            {{Form::hidden('driver_id', '', array('id' => 'driver_id'))}}
            {{Form::hidden('client_id', '', array('id' => 'client_id'))}}
            {{Form::hidden('owner_id', '', array('id' => 'owner_id'))}}
            {{Form::hidden('register_as', '', array('id' => 'register_as'))}}
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
