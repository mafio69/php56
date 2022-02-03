<div class="tab-pane fade in active" id="vehicle-data">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane techniczne pojazdu</span>
                        <a target="{{ URL::action('VmanageVehicleInfoController@getTechnicalInfo', [$vehicle->id]) }}"
                               class="fa fa-pencil-square-o pull-right tips modal-open-lg" data-toggle="modal" data-target="#modal-lg" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
                    </h4>
                </div>
                <table class="table">
                    <tr>
                        <td><label>Typ pojazdu:</label></td>
                        <Td>
                        @if($vehicle->brand && $vehicle->brand->typ == 1)
                            osobowy
                        @elseif($vehicle->brand)
                            ciężarowy
                        @else
                            ---
                        @endif

                        @if($vehicle->if_truck == 0)
                            poniżej 3.5t
                        @else
                            powyżej 3.5t
                        @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Marka pojazdu:</label></td>
                        <Td>{{ checkObjectIfNotNull($vehicle->brand, 'name') }}</td>
                    </tr>
                    <tr>
                        <td><label>Model pojazdu:</label></td>
                        <Td>{{ checkObjectIfNotNull($vehicle->model, 'name') }}</td>
                    </tr>
                    <tr>
                        <td><label>Generacja modelu:</label></td>
                        <td>{{ checkObjectIfNotNull($vehicle->generation, 'name') }}</td>
                    </tr>
                    <tr>
                        <td><label>Wersja:</label></td>
                        <td>{{ $vehicle->version }}</td>
                    </tr>
                    <tr>
                        <td><label>Rok produkcji:</label></td>
                        <td>{{ $vehicle->year_production }}</td>
                    </tr>
                    <tr>
                        <td><label>Nadwozie:</label></td>
                        <td>{{ checkObjectIfNotNull($vehicle->car_category, 'name') }}</td>
                    </tr>
                    <tr>
                        <td><label>Liczba drzwi:</label></td>
                        <td>{{ $vehicle->doors_nb }}</td>
                    </tr>
                    <tr>
                        <td><label>Typ silnika:</label></td>
                        <td>{{ checkObjectIfNotNull($vehicle->car_engine, 'name') }}</td>
                    </tr>
                    <tr>
                        <td><label>Skrzynia biegów:</label></td>
                        <td>{{ checkObjectIfNotNull($vehicle->car_gearbox, 'name') }}</td>
                    </tr>
                    <tr>
                        <td><label>Pojemność silnika:</label></td>
                        <td>{{ $vehicle->engine_capacity }}</td>
                    </tr>
                    <tr>
                        <td><label>Moc silnika:</label></td>
                        <td>{{ $vehicle->horse_power }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane pojazdu</span>
                        <a target="{{ URL::action('VmanageVehicleInfoController@getRegistrationInfo', [$vehicle->id]) }}"
                               class="fa fa-pencil-square-o pull-right tips modal-open" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
                    </h4>
                </div>
                <table class="table">
                    <tr>
                        <td><label>Numer rejestracyjny:</label></td>
                        <Td>{{ $vehicle->registration }}</td>
                    </tr>
                    <tr>
                        <td><label>Numer VIN:</label></td>
                        <Td>{{ $vehicle->vin }}</td>
                    </tr>
                    <tr>
                        <td><label>Nr umowy leasingowej:</label></td>
                        <Td>{{ checkIfEmpty($vehicle->nr_contract) }}</td>
                    </tr>
                    <tr>
                        <td><label>Status umowy:</label></td>
                        <td>{{ checkIfEmpty($vehicle->contract_status) }}</td>
                    </tr>
                    <tr>
                        <td><label>Data pierwszej rejestracji:</label></td>
                        <td>{{ checkIfEmpty($vehicle->first_registration) }}</td>
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
                    <tr>
                        <td><label>Pojazd VIP:</label></td>
                        <td>
                            @if($vehicle->if_vip == 1)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Pojazd zwrócony:</label></td>
                        <td>
                            @if($vehicle->if_return == 1)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Plan sprzedaży:</label></td>
                        <td>
                            {{ $vehicle->program }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane bierzące</span>
                        <a target="{{ URL::action('VmanageVehicleInfoController@getCurrentInfo', [$vehicle->id]) }}"
                               class="fa fa-pencil-square-o pull-right tips modal-open" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
                    </h4>
                </div>
                <table class="table">
                    <tr>
                        <td><label>Bieżący użytkownik:</label></td>
                        <Td>{{ checkObjectIfNotNull($vehicle->user, 'name').' '.checkObjectIfNotNull($vehicle->user, 'surname') }}</td>
                    </tr>
                    <tr>
                        <td><label>Miejsce użytkowania:</label></td>
                        <Td>{{ checkIfEmpty($vehicle->place_of_usage) }}</td>
                    </tr>
                    <tr>
                        <td><label>Przebieg deklarowany:</label></td>
                        <td>{{ checkIfEmpty($vehicle->declare_mileage) }}</td>
                    </tr>
                    <tr>
                        <td><label>Przebieg bieżący:</label></td>
                        <td>{{ checkIfEmpty($vehicle->actual_mileage) }}</td>
                    </tr>
                    <tr>
                        <td><label>Termin badania technicznego:</label></td>
                        <td>{{ checkIfEmpty($vehicle->technical_exam_date) }}</td>
                    </tr>
                    <tr>
                        <td><label>Termin przeglądu:</label></td>
                        <td>{{ checkIfEmpty($vehicle->servicing_date) }}</td>
                    </tr>
                    <tr>
                        <td><label>Termin ważności polisy:</label></td>
                        <td>{{ checkIfEmpty($vehicle->insurance_expire_date) }}</td>
                    </tr>
                    <tr>
                        <td><label>Suma ubezpieczenia AC:</label></td>
                        <td>{{ checkIfEmpty($vehicle->insurance) }}</td>
                    </tr>
                    <tr>
                        <td><label>Assistance:</label></td>
                        <td>{{ checkIfEmpty($vehicle->assistance) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Właściciel pojazdu</span>
                        <a target="{{ URL::action('VmanageVehicleInfoController@getOwnerInfo', [$vehicle->id]) }}"
                           class="fa fa-pencil-square-o pull-right tips modal-open" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 17px;cursor: pointer;">
                        </a>

                        <span class="btn btn-xs btn-warning pull-right modal-open" target="{{ URL::action('VmanageVehicleInfoController@getChangeOwner', [$vehicle->id]) }}" data-toggle="modal" data-target="#modal" >
                            <i class="fa fa-exchange fa-fw"></i> zmień właściciela
                        </span>
                    </h4>
                </div>
                <table class="table">
                    <tr>
                        <td><label>Pojazd w leasingu:</label></td>
                        <Td>
                            @if($vehicle->owner_id == $vehicle->company->owner_id)
                                nie
                            @else
                                tak
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Właściciel:</label></td>
                        <Td>
                            {{ $vehicle->owner->name }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane klienta</span>
                        @if($vehicle->client)
                            <i class="fa fa-pencil-square-o pull-right tips modal-open-lg" target="{{ URL::action('VmanageVehicleInfoController@getEditClient', [$vehicle->id]) }}" data-toggle="modal" data-target="#modal-lg" title="edytuj" style="font-size: 17px;cursor: pointer;"></i>
                        @endif
                        <i class="fa fa-exchange pull-right tips modal-open-lg marg-right" target="{{ URL::action('VmanageVehicleInfoController@getChangeClient', [$vehicle->id]) }}" data-toggle="modal" data-target="#modal-lg" title="zmień klienta" style="font-size: 17px;cursor: pointer;"></i>
                    </h4>
                </div>
                @if($vehicle->client)
                    <table class="table">
                        <tr>
                            <td><label>Nazwa:</label></td>
                            <Td>{{ checkObjectIfNotNull($vehicle->client, 'name') }}</td>
                        </tr>
                        <tr>
                            <td><label>NIP:</label></td>
                            <Td>{{ checkObjectIfNotNull($vehicle->client, 'NIP') }}</td>
                        </tr>
                        <tr>
                            <td><label>Regon:</label></td>
                            <Td>{{ checkObjectIfNotNull($vehicle->client, 'REGON') }}</td>
                        </tr>
                        <tr>
                            <td><label>Kod klienta:</label></td>
                            <Td>{{ checkObjectIfNotNull($vehicle->client, 'firmID') }}</td>
                        </tr>
                        <tr>
                            <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                        </tr>
                        <tr>
                            <td><label>Kod pocztowy:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'registry_post') }}</td>
                        </tr>
                        <tr>
                            <td><label>Miato:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'registry_city') }}</td>
                        </tr>
                        <tr>
                            <td><label>Ulica:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'registry_street') }}</td>
                        </tr>
                        <tr>
                            <Td colspan="2">
                                <span class="sm-title">Adres kontaktowy:</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Kod pocztowy:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'correspond_post') }}</td>
                        </tr>
                        <tr>
                            <td><label>Miasto:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'correspond_city')}}</td>
                        </tr>
                        <tr>
                            <td><label>Ulica:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'correspond_street') }}</td>
                        </tr>
                        <tr>
                            <td><label>Telefon:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'phone') }}</td>
                        </tr>
                        <tr>
                            <td><label>Email:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->client, 'email') }}</td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dostawca pojazdu</span>
                        @if($vehicle->seller)
                            <i class="fa fa-pencil-square-o pull-right tips modal-open-lg" target="{{ URL::action('VmanageVehicleInfoController@getEditSeller', [$vehicle->id]) }}" data-toggle="modal" data-target="#modal-lg" title="edytuj" style="font-size: 17px;cursor: pointer;"></i>
                        @endif
                        {{--
                        <i class="fa fa-exchange pull-right tips modal-open-lg marg-right" target="{{ URL::action('VmanageVehicleInfoController@getChangeSeller', [$vehicle->id]) }}" data-toggle="modal" data-target="#modal-lg" title="zmień sprzedawcę" style="font-size: 17px;cursor: pointer;"></i>
                        --}}
                    </h4>
                </div>
                @if($vehicle->seller)
                    <table class="table">
                        <tr>
                            <td><label>Nazwa:</label></td>
                            <Td>{{ checkObjectIfNotNull($vehicle->seller, 'name') }}</td>
                        </tr>
                        <tr>
                            <td><label>NIP:</label></td>
                            <Td>{{ checkObjectIfNotNull($vehicle->seller, 'nip') }}</td>
                        </tr>
                        <tr>
                            <td><label>Telefon:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->seller, 'phone') }}</td>
                        </tr>
                        <tr>
                            <Td colspan="2"><span class="sm-title">Adres:</span></td>
                        </tr>
                        <tr>
                            <td><label>Kod pocztowy:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->seller, 'post') }}</td>
                        </tr>
                        <tr>
                            <td><label>Miato:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->seller, 'city') }}</td>
                        </tr>
                        <tr>
                            <td><label>Ulica:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->seller, 'street') }}</td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
