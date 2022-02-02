@if(Auth::user()->can('kartoteka_szkody#dane_szkody'))
    <div class="tab-pane fade in" id="injury-data">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
                @if(! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] ))
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Etap sprawy
                        </div>
                        <div class="panel-body text-center">
                            @if($injury->stepStage)
                                <span class="label label-warning label-full">{{ $injury->stepStage->name }}</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if(in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,'-7'] ) && $injury->totalStepStage)
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Etap sprawy
                        </div>
                        <div class="panel-body text-center">
                            <span class="label label-warning label-full">{{ $injury->totalStepStage->name }}</span>
                        </div>
                    </div>
                @endif


                @if(
                    $branch
                    &&
                    (
                        ( $branch->company->groups->contains(1) || ( $branch->company->groups->contains(5) && $injury->vehicle->cfm == 1 ) )
                        &&
                        ( isset($genDocumentsA[60]) || isset($genDocumentsA[52]) )
                    )
                    &&
                    ! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] )
                )
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Etap naprawy
                        </div>
                        <div class="panel-body text-center">
                            <span class="label label-notify label-full currentRepairStage">
                            @if($injury->currentRepairStage)
                                    @if($injury->currentRepairStage->value == 1)
                                        {{ $injury->currentRepairStage->stage->checked_description }}
                                    @else
                                        {{ $injury->currentRepairStage->stage->unchecked_description }}
                                    @endif
                                    @if($injury->currentRepairStage->date_value)
                                        ( {{ $injury->currentRepairStage->date_value->format('Y-m-d') }} )
                                    @endif
                                @else
                                    w oczekiwaniu na potwierdzenie przyjęcia zlecenia
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
                @if(in_array($injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46]) )
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Etap procesowania
                        </div>
                        <div class="panel-body text-center">
                            @if(in_array($injury->step, [30,31,32,33,34,35,36,37]))
                                @if($injury->totalStatus)
                                    @if($injury->totalStatus->manual_changeable != 0 && Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#zmien_etap_procesowania'))
                                        <button type="button" class="btn btn-xs btn-info  modal-open-sm "
                                                target="{{ URL::route('injuries.total.getChangeStatus', array($injury->id)) }}"
                                                data-toggle="modal" data-target="#modal-sm"
                                                title="{{ $injury->totalStatus->name }}">
                                            {{ $injury->totalStatus->name }}
                                        </button>
                                    @else
                                        <span class="bold">{{ $injury->totalStatus->name }}</span>
                                    @endif
                                @endif
                            @else
                                @if($injury->theftStatus)
                                    <span class="bold">{{ $injury->theftStatus->name }}</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            <!-- status umowy -->
                <div class="panel panel-default small">
                    <div class="panel-heading
                    @if(mb_strtoupper($vehicle->contract_status, 'UTF-8') == mb_strtoupper('Aktywna', 'UTF-8') )
                        bg-success
                    @else
                        bg-danger
                    @endif
                    ">
                        @if( Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#dane_umowy_w_syjon') && $vehicle->syjon_contract_id)
                            <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/contract/card-file-external/info/'.$vehicle->syjon_contract_id }}" class="btn btn-xs btn-info" off-disable>
                                <i class="fa fa-search" style="font-size: 12px;"></i>
                            </a>

                        @endif
                        Status umowy
                    </div>
                    <table class="table">
                        <tr>
                            <td><label>Status na dzień zgłoszenia:</label></td>
                            <Td>{{ $vehicle->contract_status }}</td>
                        </tr>
                        <tr>
                            <td>
                                <label>Aktualny status:</label>
                                <span class="label label-danger"  id="status-loader" style="display: none;">
                                    <i style="font-size: 11px;" class="fa fa-cog fa-spin fa-3x fa-fw"></i> trwa aktualizacja...
                                </span>
                            </td>
                            @if($injury->contractStatus)
                                <td id="current_contract_status">
                                    @if($injury->contractStatus->is_active == 0)
                                        <span class="label label-danger " style="font-size: 120%;">
                                            <i class="fa fa-exclamation-triangle fa-fw"></i>
                                            {{ $injury->contractStatus->name }}
                                        </span>
                                    @else
                                        {{ $injury->contractStatus->name }}
                                    @endif
                                </td>
                            @else
                                <td id="current_contract_status">
                                    <i>nie sprawdzono</i>
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td><label>Data ważności:</label></td>
                            <td>{{ $vehicle->end_leasing }}</td>
                        </tr>
                        <tr>
                            <td><label>Saldo:</label></td>
                            <td></td>
                        </tr>
                    </table>
                </div>

                <!-- dane klienta -->
                @if($injury->client)
                    <div class="panel panel-default small">
                        <?php $client = $injury->client()->first();?>
                        <div class="panel-heading ">

                            @if($client->syjon_contractor_id)
                                <a target="_blank"
                                   href="{{ Config::get('webconfig.SYJON_URL').'/contractor/card-file/show/'.$client->syjon_contractor_id }}"
                                   class="btn btn-xs btn-info" off-disable
                                >
                                    <i class="fa fa-search" style="font-size: 12px;"></i>
                                </a>
                            @endif

                            <span>Dane klienta</span>

                            @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_klienta'))
                                <i class="fa fa-pencil-square-o pull-right tips modal-open-lg"
                                   target="{{ URL::route('injuries-getEditInjuryClient', array($injury->id)) }}"
                                   data-toggle="modal" data-target="#modal-lg" title="edytuj"
                                   style="font-size: 17px;cursor: pointer;"></i>
                            @endif
                            @if($vehicle->syjon_contract_id && Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_klienta'))
                                <form action="{{ url('injuries/update-syjon-client', [$injury->id]) }}" method="post" target="_blank" style="display: inline;">
                                    {{ Form::token() }}
                                    <button class="btn btn-xs btn-warning tips" title="zaktualizuj dane klient z SYJON"  type="submit"><i class="fa fa-fw fa-exchange"></i></button>
                                </form>
                            @endif
                        </div>
                        <table class="table">
                            <tr>
                                <td><label>Nazwa:</label></td>
                                <Td>{{ checkObjectIfNotNull($client, 'name') }}</td>
                            </tr>
                            <tr>
                                <td><label>NIP:</label></td>
                                <Td>{{ checkObjectIfNotNull($client, 'NIP') }}</td>
                            </tr>
                            <tr>
                                <td><label>Regon:</label></td>
                                <Td>{{ checkObjectIfNotNull($client, 'REGON') }}</td>
                            </tr>
                            <tr>
                                <td><label>Kod klienta:</label></td>
                                <Td>{{ checkObjectIfNotNull($client, 'firmID') }}</td>
                            </tr>
                            <tr>
                                <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                            </tr>
                            <tr>
                                <td><label>Kod pocztowy:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'registry_post') }}</td>
                            </tr>
                            <tr>
                                <td><label>Miasto:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'registry_city') }}</td>
                            </tr>
                            <tr>
                                <td><label>Ulica:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'registry_street') }}</td>
                            </tr>
                            <tr>
                                <td><label>Województwo:</label></td>
                                <td>{{ ($injury->client_id == 0 || ! $injury->client->registryVoivodeship) ? '---' : $injury->client->registryVoivodeship->name }}</td>
                            </tr>
                            <tr>
                                <Td colspan="2">
                                    <span class="sm-title">Adres kontaktowy:</span>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Kod pocztowy:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'correspond_post') }}</td>
                            </tr>
                            <tr>
                                <td><label>Miasto:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'correspond_city')}}</td>
                            </tr>
                            <tr>
                                <td><label>Ulica:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'correspond_street') }}</td>
                            </tr>
                            <tr>
                                <td><label>Województwo:</label></td>
                                <td>{{ ($injury->client_id == 0 || ! $injury->client->correspondVoivodeship) ? '---' : $injury->client->correspondVoivodeship->name }}</td>
                            </tr>
                            <tr>
                                <td><label>Telefon:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'phone') }}</td>
                            </tr>
                            <tr>
                                <td><label>Email:</label></td>
                                <td>{{ checkObjectIfNotNull($client, 'email') }}</td>
                            </tr>
                        </table>
                    </div>
                @else
                    <div class="panel panel-default small">
                        <div class="panel-heading ">
                            <span>Dane klienta</span>
                        </div>
                        <div class="panel-body">
                                <span class="btn btn-primary btn-sm btn-block modal-open-lg"
                                      target="{{ URL::to('/injuries/card/getAssignClient', [$injury->id]) }}"
                                      data-toggle="modal" data-target="#modal-lg">
                                    <i class="fa fa-plus fa-fw"></i> przypisz klienta
                                </span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
                <!-- dane szkody -->
                <div class="panel panel-default small">
                    <div class="panel-heading ">Dane szkody:
                        @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_szkody'))
                            <i class="fa fa-pencil-square-o pull-right tips modal-open-lg"
                               target="{{ URL::route('injuries-getEditInjury', array($injury->id)) }}" data-toggle="modal"
                               data-target="#modal-lg" title="edytuj"></i>
                        @endif
                    </div>
                    <table class="table">
                        <tr>
                            <td><label>Typ szkody:</label></td>
                            <td>{{ $injury->injuries_type()->first()->name }}</td>
                        </tr>
                        <tr>
                            <td><label>Rodzaj szkody:</label></td>
                            <td>
                                @if( $injury->status->injuryGroup )
                                    @if($injury->total_status_source == 1)
                                        KRADZIEŻ
                                    @else {{ $injury->status->injuryGroup->name }}
                                    @endif
                                @elseif($injury->type_incident_id == 12){
                                KRADZIEŻ
                                @else
                                    SZKODA CAŁKOWITA
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Odbiór odszkodowania:</label></td>
                            <td>
                                @if($injury->receive_id == 0)
                                    <i class="red">nieustalone</i>
                                @else
                                    {{ $injury->receive()->first()->name }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Odbiór faktury:</label></td>
                            <td>
                                @if($injury->invoicereceives_id == 0)
                                    <i class="red">nieustalone</i>
                                @else
                                    {{ $injury->invoicereceive()->first()->name }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table style="width: 100%;">
                                    <tr>
                                        <td class="text-center"><label>Kosztorysowe rozliczenie</label></td>
                                        <td class="text-center"><label>Zgłoszenie DSP</label></td>
                                        <td class="text-center"><label>Windykacja</label></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            @if($injury->settlement_cost_estimate == 1)
                                                <i class="fa fa-check"></i>
                                            @else
                                                <i class="fa fa-minus"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($injury->dsp_notification == 1)
                                                <i class="fa fa-check"></i>
                                            @else
                                                <i class="fa fa-minus"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($injury->vindication == 1)
                                                <i class="fa fa-check"></i>
                                            @else
                                                <i class="fa fa-minus"></i>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Data zdarzenia:</label></td>
                            <Td>{{ $injury->date_event }}</td>
                        </tr>
                        <tr>
                            <td><label>Godzina zdarzenia:</label></td>
                            <Td>
                                @if($injury->time_event)
                                    {{ Carbon\Carbon::parse($injury->time_event)->format('H:i') }}
                                @else
                                    ---
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Rodzaj zdarzenia:</label></td>
                            <Td>
                                @if( $injury->type_incident_id != 0 && $injury->type_incident_id != null)
                                    {{ $injury->type_incident()->first()->name}}
                                @else
                                    ---
                                @endif
                            </td>
                        </tr>
                        <tr class="@if($injury->insuranceCompany && $injury->insuranceCompany->active == 9) danger @endif ">
                            <td><label>ZU:</label></td>
                            <td>
                                @if($injury->insuranceCompany)
                                    {{ $injury->insuranceCompany->name }}
                                @else
                                    ---
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Nr szkody:</label></td>
                            <td>
                                @if( $injury->injury_nr != '' && $injury->injury_nr != null)
                                    {{ $injury->injury_nr}}
                                @else
                                    ---
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Zawiadomiono policję:</label></td>
                            @if($injury->police == 1)
                                <td>tak</td>
                        </tr>
                        <tr>
                            <td><label>Nr zgłoszenia policji:</label></td>
                            <td>{{ $injury->police_nr}}</td>
                        </tr>
                        <tr>
                            <td><label>Jednostka policji:</label></td>
                            <td>{{ $injury->police_unit}}</td>
                        </tr>
                        <tr>
                            <td><label>Kontakt z policją:</label></td>
                            <td>{{ $injury->police_contact}}</td>
                            @elseif($injury->police == 0)
                                <td>nie</td>
                            @else
                                <td>nie ustalono</td>
                            @endif
                        </tr>
                        <tr>
                            <td><label>Spisano oświadczenie:</label></td>
                            <Td>
                                @if( $injury->if_statement != 1 )
                                    nie
                                @else
                                    tak
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Zabrano dowód rejestracyjny:</label></td>
                            <Td>
                                @if( $injury->if_registration_book != 1 )
                                    nie
                                @else
                                    tak
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Wina kierowcy:</label></td>
                            @if($injury->if_driver_fault == 1)
                                <td>tak</td>
                            @elseif($injury->if_driver_fault == 0)
                                <td>nie</td>
                            @else
                                <td>nie ustalono</td>
                            @endif
                        </tr>
                        <tr>
                            <td><label>Wymaga holowania:</label></td>
                            <Td>
                                @if( $injury->if_towing != 1 )
                                    nie
                                @else
                                    tak
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Wymagane auto zastępcze:</label></td>
                            <Td>
                                @if( $injury->if_courtesy_car != '1' )
                                    nie
                                @else
                                    tak
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Door2door:</label></td>
                            <Td>
                                @if( $injury->if_door2door != 1 )
                                    nie
                                @else
                                    tak
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Szkoda zgłoszona do TU:</label></td>
                            <Td>
                                @if( $injury->reported_ic != 1 )
                                    nie
                                @else
                                    tak
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Samochód znajduje się w serwisie:</label></td>
                            @if($injury->in_service == 1)
                                <td>tak</td>
                            @elseif($injury->in_service == 0)
                                <td>nie</td>
                            @else
                                <td>nie ustalono</td>
                            @endif
                        </tr>
                        <tr>
                            <td><label>Naprawa w sieci IL:</label></td>
                            @if($injury->if_il_repair == 1)
                                <td>tak</td>
                            @elseif($injury->if_il_repair == 0)
                                <td>nie</td>
                            @else
                                <td>nie ustalono</td>
                            @endif
                        </tr>
                        @if($injury->if_il_repair == 0)
                            <tr>
                                <td><label>Przyczyna naprawy poza siecią IL:</label></td>
                                @if($injury->repairInformation)
                                    <td>{{ $injury->repairInformation->name }}
                                        @if(isset($injury->il_repair_info_description))
                                            <br>{{$injury->il_repair_info_description}}
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <label>SAP rodzszk:</label>
                            </td>
                            <td>
                                {{ $injury->sap_rodzszk }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>ZGODA NA OFERTĘ CAS:</label>
                            </td>
                            <td>
                                @if($injury->cas_offer_agreement == 1)
                                    <i class="fa fa-check"></i>
                                @else
                                    <i class="fa fa-minus"></i>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>


            </div>

            <div class="col-sm-6 col-md-4  col-lg-3 item-m">
                <!-- dane pojazdu -->
                <div class="panel panel-default small">
                    <div class="panel-heading ">
                        @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#dane_pojazdu_w_syjon'))
                            <a target="_blank"
                               href="{{ Config::get('webconfig.SYJON_URL').'/object/card/info/'.$vehicle->syjon_vehicle_id }}"
                               class="btn btn-xs btn-info" off-disable
                                @if(!$vehicle->syjon_vehicle_id )
                                   disabled readonly="readonly"
                                @endif
                                >
                                <i class="fa fa-search" style="font-size: 12px;"></i>
                            </a>
                        @endif
                        <span>Dane pojazdu</span>

                        @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_pojazdu'))
                            @if($injury->vehicle_type == 'Vehicles')
                                <i class="fa fa-pencil-square-o pull-right tips modal-open"
                                   target="{{ URL::route('injuries-getEditVehicle', array($injury->id)) }}" data-toggle="modal"
                                   data-target="#modal" title="edytuj"></i>
                            @else
                                <i class="fa fa-pencil-square-o pull-right tips modal-open"
                                   target="{{ URL::action('VmanageVehicleInfoController@getEditInjuryVehicle', [$injury->id]) }} }}"
                                   data-toggle="modal" data-target="#modal" title="edytuj"></i>
                            @endif
                        @endif
                    </div>
                    <table class="table">
                        <tr>
                            <td><label>Rejestracja:</label></td>
                            <Td>{{ $vehicle->registration }}</td>
                        </tr>
                        <tr>
                            <td><label>Nr umowy leasingowej:</label></td>
                            <td>{{ $vehicle->nr_contract }}</td>
                        </tr>
                        <tr>
                            <td><label>Program sprzedaży:</label></td>
                            <td><span class="label label-primary" style="font-size: 110%">{{ $vehicle->program }}</span></td>
                        </tr>
                        <tr>
                            <td><label>VIN:</label></td>
                            <td>{{ ($injury->vehicle_type == 'Vehicles') ? $vehicle->VIN : $vehicle->vin }}</td>
                        </tr>
                        <tr>
                            <td><label>Marka i model:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand) }} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model)  }}</td>
                        </tr>
                        <tr>
                            <td><label>Rok produkcji:</label></td>
                            <td>{{ $vehicle->year_production }}</td>
                        </tr>
                        <tr>
                            <td><label>Data pierwszej rejestracji:</label></td>
                            <td>{{ $vehicle->first_registration }}</td>
                        </tr>
                        <tr>
                            <td><label>Rodzaj pojazdu:</label></td>
                            <td>{{ $vehicle->object_type }}</td>
                        </tr>
                        <tr>
                            <td><label>Dostawca pojazdu:</label></td>
                            <td>{{ $vehicle->seller ? $vehicle->seller->name : '' }}</td>
                        </tr>
                        <tr>
                            <td><label>CFM:</label></td>
                            <td>
                                @if($vehicle->cfm == 1)
                                    <i class="fa fa-check"></i>
                                @else
                                    <i class="fa fa-minus"></i>
                                @endif
                            </td>
                        </tr>
                        @if($owner->wsdl != '')
                            <tr class="alert-warning">
                                <td>
                                    <label>Samochód rejestrowany w AS</label>
                                </td>
                                <td>
                                    @if($vehicle->register_as == 1)
                                        tak
                                    @else
                                        nie
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>

                <!-- dane zgłaszajacego -->
                <div class="panel panel-default small">
                    <div class="panel-heading overflow">
                        <span class="pull-left">Dane zgłaszającego</span>
                        @if($injury->contact_person == 2)
                            <i class="fa fa-phone blue pull-left change_contact"></i>
                        @elseif($injury->contact_person == 1 && Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#zmien_osobe_kontaktowa'))
                            <span class="ico-md phone_empty change_contact tips modal-open-sm"
                                  title="zmień osobę kontaktową"
                                  target="{{ URL::route('injuries-getChangeContact', array($injury->id)) }}"
                                  data-toggle="modal" data-target="#modal-sm"></span>
                        @endif

                        @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_zglaszajacego'))
                            <i class="fa fa-pencil-square-o pull-right tips modal-open"
                               target="{{ URL::route('injuries-getEditInjuryNotifier', array($injury->id)) }}"
                               data-toggle="modal" data-target="#modal" title="edytuj"></i>
                        @endif
                    </div>
                    <table class="table">
                        <tr>
                            <td><label>Imię:</label></td>
                            <td>{{ $injury->notifier_name }}</td>
                        </tr>
                        <tr>
                            <td><label>Nazwisko:</label></td>
                            <Td>{{ $injury->notifier_surname }}</td>
                        </tr>
                        <tr>
                            <td><label>Telefon:</label></td>
                            <td>{{ $injury->notifier_phone }}</td>
                        </tr>
                        <tr>
                            <td><label>Email:</label></td>
                            <td>{{ $injury->notifier_email }}</td>
                        </tr>
                    </table>
                </div>

                <!-- dane kierowcy -->
                <div class="panel panel-default small">
                    <div class="panel-heading overflow">
                        <span class="pull-left">Dane kierowcy</span>
                        @if($injury->contact_person == 1)
                            <i class="fa fa-phone blue pull-left change_contact"></i>
                        @elseif($injury->contact_person == 2 && Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#zmien_osobe_kontaktowa'))
                            <span class="ico-md phone_empty change_contact tips modal-open-sm"
                                  title="zmień osobę kontaktową"
                                  target="{{ URL::route('injuries-getChangeContact', array($injury->id)) }}"
                                  data-toggle="modal" data-target="#modal-sm"></span>
                        @endif

                        @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_kierowcy'))
                            <i class="fa fa-pencil-square-o pull-right tips modal-open"
                               target="{{ URL::route('injuries-getEditInjuryDriver', array($injury->id)) }}" data-toggle="modal"
                               data-target="#modal" title="edytuj"></i>
                        @endif
                    </div>
                    <table class="table">
                        <tr>
                            <td><label>Imię:</label></td>
                            <td>{{ isset($driver->name)?$driver->name:'---' }}</td>
                        </tr>
                        <tr>
                            <td><label>Nazwisko:</label></td>
                            <Td>{{ isset($driver->surname)?$driver->surname:'---' }}</td>
                        </tr>
                        <tr>
                            <td><label>Telefon:</label></td>
                            <td>{{ isset($driver->phone)?$driver->phone:'---' }}</td>
                        </tr>
                        <tr>
                            <td><label>Email:</label></td>
                            <td>{{ isset($driver->email)?$driver->email:'---' }}</td>
                        </tr>
                        <tr>
                            <td><label>Miasto:</label></td>
                            <td>{{ isset($driver->city)?$driver->city:'---' }}</td>
                        </tr>
                    </table>
                </div>

            </div>

            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
                <!-- dane wlasciciela -->
                <div class="panel panel-default small">
                    <div class="panel-heading ">
                        @if( Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#dane_wlasciciela_w_syjon'))
                            <a target="_blank"
                               href="{{ Config::get('webconfig.SYJON_URL').'/contractor/card-file/show/'.$owner->syjon_contractor_id }}"
                               class="btn btn-xs btn-info" off-disable
                                @if(!$owner->syjon_contractor_id)
                               disabled readonly="readonly"
                                @endif
                            >
                                <i class="fa fa-search" style="font-size: 12px;"></i>
                            </a>
                        @endif
                        <span>Dane właściciela</span>
                        @if($injury->vehicle_type == 'Vehicles')
                            @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_wlasciciela'))
                                <i class="fa fa-pencil-square-o pull-right tips modal-open"
                                   target="{{ URL::route('injuries-getEditVehicleOwner', array($injury->id)) }}"
                                   data-toggle="modal" data-target="#modal" title="edytuj"></i>
                            @endif
                        @else
                            <i class="fa fa-pencil-square-o pull-right tips"
                               title="edycja pojazdu dostępna poprzez panel zarządzania pojazdami" disabled></i>
                        @endif
                    </div>
                    <table class="table">
                        <tr>
                            <td><label>Nazwa:</label></td>
                            <Td>{{ $owner->name }}</td>
                        </tr>
                        @if($owner->old_name)
                            <tr>
                                <td><label>Dawna nazwa:</label></td>
                                <Td>{{ $owner->old_name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td><label>NIP:</label></td>
                            <td>{{ ($owner->data()->where('parameter_id', 8)->first() ) ? $owner->data()->where('parameter_id', 8)->first()->value  : '---' }}</td>
                        </tr>
                        <tr>
                            <td><label>Kod pocztowy:</label></td>
                            <td>{{ $owner->post }}</td>
                        </tr>
                        <tr>
                            <td><label>Miasto:</label></td>
                            <td>{{ $owner->city }}</td>
                        </tr>
                        <tr>
                            <td><label>Ulica:</label></td>
                            <td>{{ $owner->street }}</td>
                        </tr>
                    </table>
                </div>

                <!-- dane sprawcy -->
                <div class="panel panel-default small">
                    <div class="panel-heading ">Dane sprawcy
                        @if($injury->offender_id != 0 && Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_sprawcy'))
                            <i class="fa fa-pencil-square-o pull-right tips modal-open"
                               target="{{ URL::route('injuries-getEditInjuryOffender', array($injury->id)) }}"
                               data-toggle="modal" data-target="#modal" title="edytuj"></i>
                        @endif
                    </div>
                    @if($injury->offender_id != 0 && ($injury->injuries_type_id == '2' || $injury->injuries_type_id == '4' || $injury->injuries_type_id == '5'))
                        <?php $offender = $injury->offender()->first();?>
                        <table class="table">
                            <tr>
                                <td><label>Imię:</label></td>
                                <td>{{ $offender->name }}</td>
                            </tr>
                            <tr>
                                <td><label>Nazwisko:</label></td>
                                <Td>{{ $offender->surname }}</td>
                            </tr>
                            <tr>
                                <td><label>Adres zamieszkania:</label></td>
                                <td>{{ $offender->post }} {{$offender->city}}, {{$offender->street}}</td>
                            </tr>
                            <tr>
                                <td><label>Rejestracja:</label></td>
                                <td>{{$offender->registration}}</td>
                            </tr>
                            <tr>
                                <td><label>Samochoód:</label></td>
                                <td>{{$offender->car}}</td>
                            </tr>
                            <tr>
                                <td><label>Nr polisy OC:</label></td>
                                <td>{{$offender->oc_nr}}</td>
                            </tr>
                            <tr>
                                <td><label>Nazwa ZU</label></td>
                                <td>{{$offender->zu}}</td>
                            </tr>
                            <tr>
                                <td><label>Data ważności polisy:</label></td>
                                <td>{{$offender->expire}}</td>
                            </tr>
                            <tr>
                                <td><label>Sprawca właścicielem:</label></td>
                                <td>
                                    @if($offender->owner == 1)
                                        tak
                                    @elseif($offender->owner == 0)
                                        nie
                                    @else
                                        <i>nieustalono</i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Uwagi:</label></td>
                                <td>{{$offender->remarks}}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-center marg-top-min"><i>moduł nieaktywny</i></p>
                    @endif
                </div>

                @if($injury->branches->count() > 0)
                    <div class="panel panel-info small">
                        <div class="panel-body">
                            <span class="btn btn-info btn-xs btn-block off-disable"
                                  role="button" data-toggle="collapse" href="#collapseBranches" aria-expanded="false" aria-controls="collapseBranches"
                            >
                                <i class="fa fa-history fa-fw"></i> historia serwisów <span class="badge">{{ $injury->branches->count() }}</span>
                            </span>

                            <div class="collapse" id="collapseBranches">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>#</th>
                                    <th>nazwa</th>
                                    <th>adres</th>
                                    <th>telefon</th>
                                    <th>data przypisania</th>
                                    </thead>
                                    @foreach($injury->branches as $k => $injuryBranch)
                                        @if($injuryBranch->branch)
                                            <tr>
                                                <td>{{ ++$k }}.</td>
                                                <td>{{ $injuryBranch->branch->short_name }}</td>
                                                <td>{{ $injuryBranch->branch->code }} {{$injuryBranch->branch->city}}, {{$injuryBranch->branch->street}}</td>
                                                <td>{{ $injuryBranch->branch->phone }}</td>
                                                <td>{{ $injuryBranch->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            </div>

                            <span class="btn btn-warning btn-xs btn-block marg-top modal-open" target="{{ URL::to('injuries/add-branches-history', array($injury->id)) }}"
                                  data-toggle="modal" data-target="#modal">
                                <i class="fa fa-plus fa-fw"></i>
                                dodaj wpis historii
                            </span>
                        </div>
                    </div>
                @endif
                <!-- przypisany serwis -->
                @if($injury->branch_id != 0 && $injury->branch_id != null)
                    <div class="panel @if($injury->branch_id > 0 && $injury->branch->trashed() ) panel-danger @else panel-default @endif small">
                        <div class="panel-heading ">
                            @if($injury->branch_id > 0 && $injury->branch->company->groups->count() > 0)
                                <i class="fa fa-wrench blue tips sm-ico pull-left" data-html="true"
                                   title="<i>Serwis w grupie: {{ implode(',', $injury->branch->company->groups->lists('name')) }}</i>"
                                   style="margin-top: -5px;"></i>
                            @elseif($injury->branch_id > 0)
                                <i class="fa fa-wrench red tips sm-ico pull-left" data-html="true"
                                   title="<i>Serwis poza grupą</i>" style="margin-top: -5px;"></i>
                            @endif
                            @if(!$injury->original_branch_id)
                                <span>Przypisany serwis</span>
                                @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_warsztat'))
                                    @if($injury->branch_id != '-1')
                                        <i class="fa fa-minus-square-o pull-right tips modal-open"
                                           target="{{ URL::route('injuries-getDeleteInjuryBranch', array($injury->id)) }}"
                                           data-toggle="modal" data-target="#modal" title="usuń warsztat"></i>
                                    @endif
                                    <i class="fa fa-pencil-square-o pull-right tips modal-open"
                                       target="{{ URL::route('injuries-getEditInjuryBranch', array($injury->id)) }}"
                                       data-toggle="modal" data-target="#modal" title="przypisz warsztat/edycja"></i>
                                @endif
                            @endif


                        </div>
                        @if($injury->branch_id != '-1')
                            @if( $injury->branch->trashed() )
                                <h4 class="text-center">
                                    <i class="red">
                                        oryginalnie przypisany serwis został usunięty
                                    </i>
                                </h4>
                            @endif
                            <table class="table">
                                <tr>
                                    <td><label>Skrócona nazwa:</label></td>
                                    <Td>{{ $branch->short_name }}</td>
                                </tr>
                                <tr>
                                    <td><label>Adres:</label></td>
                                    <td>{{ $branch->code }} {{$branch->city}}, {{$branch->street}}</td>
                                </tr>
                                <tr>
                                    <td><label>Telefon:</label></td>
                                    <td>{{ $branch->phone }}</td>
                                </tr>
                                <tr style="background-color: #8cb0d9;">
                                    <td><label>Email oddziału:</label></td>
                                    <td><b>{{ $branch->email }}</b> {{ $branch->other_emails }}</td>
                                </tr>
                                @if($branch->company->guardian)
                                <tr>
                                    <td><label>Opiekun DR CAS:</label></td>
                                    <td>{{ $branch->company->guardian->name().' | <br>'.$branch->company->guardian->email().' | '.$branch->company->guardian->phone}}</td>
                                </tr>
                                @endif
                            </table>
                        @else
                            <h4 class="text-center"><i class="red">Szkoda procedowana bez serwisu</i></h4>
                        @endif
                    </div>
                    @if($injury->original_branch_id != 0 && $injury->original_branch_id != null)
                        <div class="panel @if($injury->original_branch_id > 0 && $injury->originalBranch->trashed() ) panel-danger @else panel-default @endif small">
                            <div class="panel-heading ">
                                @if($injury->original_branch_id > 0 && $injury->originalBranch->company->groups->count() > 0)
                                    <i class="fa fa-wrench blue tips sm-ico pull-left" data-html="true"
                                       title="<i>Serwis w grupie: {{ implode(',', $injury->originalBranch->company->groups->lists('name')) }}</i>"
                                       style="margin-top: -5px;"></i>
                                @elseif($injury->original_branch_id > 0)
                                    <i class="fa fa-wrench red tips sm-ico pull-left" data-html="true"
                                       title="<i>Serwis poza grupą</i>" style="margin-top: -5px;"></i>
                                @endif

                                Oryginalnie przypisany serwis
                                @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_warsztat'))
                                    <i class="fa fa-pencil-square-o pull-right tips modal-open"
                                       target="{{ URL::route('injuries-getEditInjuryBranchOriginal', array($injury->id)) }}"
                                       data-toggle="modal" data-target="#modal" title="przypisz warsztat/edycja"></i>
                                @endif
                            </div>
                            @if($injury->original_branch_id != '-1')
                                <table class="table">
                                    <tr>
                                        <td><label>Skrócona nazwa:</label></td>
                                        <Td>{{ $injury->originalBranch->short_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><label>Adres:</label></td>
                                        <td>{{ $injury->originalBranch->code.' '.$injury->originalBranch->city}}
                                            , {{$injury->originalBranch->street}}</td>
                                    </tr>
                                    <tr>
                                        <td><label>Telefon:</label></td>
                                        <td>{{ $injury->originalBranch->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><label>Email:</label></td>
                                        <td>{{ $injury->originalBranch->email }}</td>
                                    </tr>
                                </table>
                            @else
                                <h4 class="text-center"><i class="red">Szkoda procedowana bez serwisu</i></h4>
                            @endif

                        </div>
                    @endif
                @elseif($injury->step > 0)
                    <div class="panel panel-default small">
                        <div class="panel-heading ">Brak przypisanego serwisu</div>

                        <div class="panel-body text-center">
                            @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_warsztat'))
                                <span class="btn btn-primary modal-open"
                                      target="{{ URL::route('injuries-getEditInjuryBranch', array($injury->id)) }}"
                                      data-toggle="modal" data-target="#modal">
                                    przypisz serwis
                                    <i class="fa fa-pencil-square-o  "></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                @if(count($injury->activeInvoices)>0)
                <div class="panel panel-default small">
                    <div class="panel-heading">
                        <span>Przypisane serwisy na podstawie faktury</span>
                    </div>
                    {{-- @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_warsztat')) --}}
                    <table class="table">
                        <select class="form-control" id='invoices' name='invoices'>
                            @foreach ($injury->activeInvoices as $key => $activeInvoice)
                                @if($activeInvoice->branch)
                                    <option value='{{$activeInvoice->branch_id}}'>
                                        {{'Faktura '.$activeInvoice->invoice_nr.' z dnia '.$activeInvoice->invoice_date}}
                                    </option>
                               @endif
                            @endforeach
                        </select>
                        <table class="table">
                            <tr>
                                <td><label>Skrócona nazwa:</label></td>
                                <Td><span id="ic_short_name"></span></td>
                            </tr>
                            <tr>
                                <td><label>Adres:</label></td>
                                <td><span id="ic_adress"></span></td>
                            </tr>
                            <tr>
                                <td><label>Telefon:</label></td>
                                <td><span id="ic_tel"></span></td>
                            </tr>
                            <tr>
                                <td><label>E-mail:</label></td>
                                <td><span id="ic_email"></span></td>
                            </tr>
                        </table>
                    </table>
                </div>
                @endif
            </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-4  col-lg-3 item-m"></div>
        <div class="col-sm-6 col-md-4  col-lg-3 item-m">
            <!-- dane polisy -->
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane polisy AC
                    @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_polisy_ac'))
                        <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::route('injuries-getEditInjuryInsurance', array($injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj"></i>
                    @endif
                </div>
                <table class="table">
                    <tr>
                        <td><label>Zakład ubezpieczeń:</label></td>
                        <Td>
                            @if($injury->injuryPolicy->insuranceCompany)
                                {{ $injury->injuryPolicy->insuranceCompany->name }}
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Data ważności polisy:</label></td>
                        <td>{{ $injury->injuryPolicy->expire }}</td>
                    </tr>
                    <tr>
                        <td><label>Nr polisy:</label></td>
                        <td>{{ $injury->injuryPolicy->nr_policy }}</td>
                    </tr>
                    <tr>
                        <td><label>Suma ubezpieczenia [zł]:</label></td>
                        <td>{{ $injury->injuryPolicy->insurance }}</td>
                    </tr>
                    <tr>
                        <td><label>Wkład własny [zł]:</label></td>
                        <td>
                            {{ $injury->injuryPolicy->contribution }}
                        </td>
                    </tr>
                    <tr>
                        <td><label>[netto/brutto]:</label></td>
                        <td>
                            {{ Config::get('definition.compensationsNetGross')[$injury->injuryPolicy->netto_brutto] }}
                        </td>
                    </tr>
                    <tr>
                        <td><label>Assistance:</label></td>
                        <td>
                            @if($injury->injuryPolicy->assistance == 1)
                                {{ $vehicle->assistance_name}}
                            @else
                                nie
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Zakres ubezpieczenia:</label>
                        </td>
                        <td>
                            {{ $injury->injuryPolicy->risks }}
                        </td>
                    </tr>
                    <tr>
                        <td><label>GAP:</label></td>
                        <td>
                            @if($injury->injuryPolicy->gap == 2)
                                <i class="red">
                            @endif
                                {{ Config::get('definition.insurance_options_definition.'.$injury->injuryPolicy->gap) }}
                            @if($injury->injuryPolicy->gap == 2)
                                </i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Ochrona prawna:</label></td>
                        <td>
                            @if($injury->injuryPolicy->legal_protection == 0)
                                <i class="red">
                                    @endif
                                    {{ Config::get('definition.insurance_options_definition.'.$injury->injuryPolicy->legal_protection) }}
                                    @if($injury->injuryPolicy->legal_protection == 0)
                                </i>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4  col-lg-3 item-m">
            @if($injury->injuryPolicy->gap == 1)
            <!-- dane polisy GAP-->
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane polisy GAP
                    @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_polisy_ac'))
                        <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::to('injuries/edit-gap', array($injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj"></i>
                    @endif
                </div>
                @if($injury->injuryGap)
                <table class="table">
                    <tr>
                        <td><label>Zakład ubezpieczeń:</label></td>
                        <Td>
                            @if($injury->injuryGap->insurance_company_id )
                                {{ $injury->injuryGap->insuranceCompany->name }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>GAP:</label></td>
                        <td>{{ $injury->injuryGap->gapType ? $injury->injuryGap->gapType->name : '' }}</td>
                    </tr>
                    <tr>
                        <td><label>Suma ubezpieczenia [zł]:</label></td>
                        <td>
                            {{number_format($injury->injuryGap->insurance_amount,2,",","")}}
                            @if( $injury->injuryGap->netto_brutto == 1)
                                netto
                            @else
                                brutto
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Prognoza GAP [zł]:</label></td>
                        <td>
                            @if($injury->injuryGap->forecast)
                                {{number_format($injury->injuryGap->forecast,2,",","")}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Numer szkody w TU:</label></td>
                        <td>{{  $injury->injuryGap->injury_number }}</td>
                    </tr>
                </table>
                @endif
            </div>
            @endif
        </div>
        <div class="col-sm-6 col-md-4  col-lg-3 item-m"></div>
    </div>
    <div class="row">
          <div class="col-sm-6  item-m">
            <div class="panel panel-default small">
               <div class="panel-heading ">Informacja wewnętrzna:
                   @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_informacje_wewnetrzna'))
                  <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::route('injuries-getEditInjuryInfo', array($injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj"></i>
                @endif
               </div>
               <table class="table">
                @if($injury->info != 0)
                <tr>
                  <td>{{ $info->content }}</td>
                </tr>
                @endif
               </table>
            </div>
          </div>

          <div class="col-sm-6 item-m">
            <div class="panel panel-default small">
               <div class="panel-heading ">Opis szkody:
                   @if(Auth::user()->can('kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_opis_szkody'))
                       <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::route('routes.get', array('injuries', 'card', 'editRemarks', $injury->id)) }}" data-toggle="modal" data-target="#modal" title="edytuj"></i>
                   @endif
               </div>
               <table class="table">
                @if($injury->remarks != 0)
                <tr>
                  <td>{{ $remarks->content }}</td>
                </tr>
                @endif
               </table>
            </div>
          </div>

      </div>

    @if(!$injuriesExistsOnVehicle->isEmpty())
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-header ">Istniejące w systemie szkody na pojeździe</h4>
            <table class="table table-hover">
                <thead>
                <th>data zgłoszenia</th>
                <th>osoba zgłaszająca</th>
                <th>miejsce zdarzenia</th>
                <th>nr sprawy</th>
                <th>nr szkodu (ZU)</th>
                <th>opis zdarzenia</th>
                <th>status</th>
            </thead>
            @foreach ($injuriesExistsOnVehicle as $k => $injuryOnVehicle)
                <tr class="vertical-middle">
                    <td>
                        {{ substr($injuryOnVehicle->created_at, 0, -3) }}
                    </td>
                    <td>
                        {{ $injuryOnVehicle->notifier_surname . ' ' . $injuryOnVehicle->notifier_name . '<br>tel:' . $injuryOnVehicle->notifier_phone . ' email:' . $injuryOnVehicle->notifier_email  }}
                    </td>
                    <td>
                        {{ $injuryOnVehicle->event_city . ' ' . $injuryOnVehicle->event_street . '<br>' . $injuryOnVehicle->date_event  }}
                    </td>
                    <td>
                        @if(Auth::user()->can('kartoteka_szkody#wejscie'))
                            <a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($injuryOnVehicle->id))}}" >
                                {{$injuryOnVehicle->case_nr}}
                            </a>
                        @else
                            {{$injuryOnVehicle->case_nr}}
                        @endif
                    </td>
                    <td>
                        {{ (($injuryOnVehicle->injury_nr == '') ? '---' : $injuryOnVehicle->injury_nr) }}
                    </td>
                    <td>
                        {{ (($injuryOnVehicle->info != 0 && $injuryOnVehicle->info != null) ? $injuryOnVehicle->getInfo->content : '---') }}
                    </td>
                    <td>
                        {{ $injuryOnVehicle->status->name }}
                    </td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
    @endif
</div>
@endif