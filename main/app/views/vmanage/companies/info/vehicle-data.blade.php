<div class="tab-pane fade in active" id="vehicle-data">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane techniczne pojazdu</span>
                        <a href="{{ URL::action('VmanageVehicleInfoController@getTechnicalInfo', [$vehicle->id]) }}" class="fa fa-pencil-square-o pull-right tips" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
                    </h4>
                </div>
                <table class="table">
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
                        <span>Dane rejestracyjne pojazdu</span>
                        <a href="{{ URL::action('VmanageVehicleInfoController@getTechnicalInfo', [$vehicle->id]) }}" class="fa fa-pencil-square-o pull-right tips" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
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
                        <td><label>Data pierwszej rejestracji:</label></td>
                        <td>{{ checkIfEmpty($vehicle->first_registration) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane bierzące</span>
                        <a href="{{ URL::action('VmanageVehicleInfoController@getTechnicalInfo', [$vehicle->id]) }}" class="fa fa-pencil-square-o pull-right tips" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
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
                        <a href="{{ URL::action('VmanageVehicleInfoController@getTechnicalInfo', [$vehicle->id]) }}" class="fa fa-pencil-square-o pull-right tips" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
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
                        <Td>{{ $vehicle->owner->name }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
