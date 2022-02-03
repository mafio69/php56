<div class="tab-pane fade in " id="agreement-data">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3 item-m">
            <!-- dane leasingobiorcy -->
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane leasingobiorcy</span>
                        @if(! $agreement->cessions->isEmpty())
                            <p class="pull-right small pointer">
                                <span class="label label-info modal-open tips" target="{{ URL::to('insurances/info/show-cessions', [$agreement->id]) }}" data-toggle="modal" data-target="#modal"  title="pokaż poprzednich leasingobiorców">
                                    Cesja
                                    <i class="fa fa-search" style="font-size: 100%;"></i>
                                </span>
                            </p>
                        @endif
                        @if($agreement->potential_cession == 1)
                                <span class="label label-danger tips pull-right small" title="potencjalna cesja">
                                    <i class="fa fa-exclamation-triangle" style="font-size: 100%; color:white;"></i>
                                </span>
                        @endif
                        @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                            <a href="{{ URL::to('insurances/info-client/edit', [$agreement->id]) }}" class="fa fa-pencil-square-o pull-right tips" title="edytuj" style="font-size: 17px;cursor: pointer;"></a>
                        @endif
                    </h4>
                </div>
                <table class="table">
                    <tr>
                        <td><label>Nazwa:</label></td>
                        <Td>{{ $agreement->client->name }}</td>
                    </tr>
                    <tr>
                        <td><label>NIP:</label></td>
                        <Td>{{ $agreement->client->NIP }}</td>
                    </tr>
                    <tr>
                        <td><label>Regon:</label></td>
                        <Td>{{ $agreement->client->REGON }}</td>
                    </tr>
                    <tr>
                        <td><label>Kod klienta:</label></td>
                        <Td>{{ $agreement->client->firmID }}</td>
                    </tr>
                    <tr>
                        <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                    </tr>
                    <tr>
                        <td><label>Kod pocztowy:</label></td>
                        <td>{{ $agreement->client->registry_post }}</td>
                    </tr>
                    <tr>
                        <td><label>Miasto:</label></td>
                        <td>{{ $agreement->client->registry_city }}</td>
                    </tr>
                    <tr>
                        <td><label>Ulica:</label></td>
                        <td>{{ $agreement->client->registry_street }}</td>
                    </tr>
                    <tr>
                        <Td colspan="2">
                            <span class="sm-title">Adres kontaktowy:</span>

                        </td>
                    </tr>
                    <tr>
                        <td><label>Kod pocztowy:</label></td>
                        <td>{{ $agreement->client->correspond_post }}</td>
                    </tr>
                    <tr>
                        <td><label>Miasto:</label></td>
                        <td>{{ $agreement->client->correspond_city }}</td>
                    </tr>
                    <tr>
                        <td><label>Ulica:</label></td>
                        <td>{{ $agreement->client->correspond_street }}</td>
                    </tr>
                    <tr>
                        <td><label>Telefon:</label></td>
                        <td>{{ $agreement->client->phone }}</td>
                    </tr>
                    <tr>
                        <td><label>Email:</label></td>
                        <td>{{ $agreement->client->email }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3 item-m">
            <div class="panel panel-default small">
                <div class="panel-heading ">
                    <h4 class="panel-title">
                        <span>Dane umowy</span>
                        @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                            <i class="fa fa-pencil-square-o pull-right tips modal-open" target="{{ URL::to('insurances/info-dialog/edit-agreement', [$agreement->id]) }}" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 17px;cursor: pointer;"></i>
                        @endif
                    </h4>
                </div>
                <table class="table">
                    <Tr>
                        <td><label>Nr umowy:</label></td>
                        <td>{{ $agreement->nr_contract }}</td>
                    </Tr>
                    <tr>
                        <td><label>Finansujący:</label></td>
                        <td>{{ $agreement->owner->name }}</td>
                    </tr>
                    <tr>
                        <td><label>Ilość miesięcy:</label></td>
                        <td>{{ checkIfEmpty($agreement->months) }}</td>
                    </tr>
                    <tr>
                        <td><label>Wart. netto pożyczki:</label></td>
                        <td>{{ number_format($agreement->loan_net_value,2,"."," ") }} zł</td>
                    </tr>
                    <tr>
                        <td><label>Wart. brutto:</label></td>
                        <td>{{ number_format($agreement->loan_gross_value,2,"."," ") }} zł</td>
                    </tr>
                    <tr>
                        <td><label>Rodzaj umowy:</label></td>
                        <td>
                            @if($agreement->leasingAgreementType)
                                {{ $agreement->leasingAgreementType->name }}
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Aktualny ubezpieczyciel:</label></td>
                        <td>
                            @if(!$agreement->insurances->isEmpty() && $agreement->insurances()->active()->first() && $agreement->insurances()->active()->first()->insuranceCompany)
                                {{ $agreement->insurances()->active()->first()->insuranceCompany->name }}
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Ubezpieczenie od kwoty:</label></td>
                        <td>
                            @if($agreement->net_gross == 1)
                                netto
                            @elseif($agreement->net_gross == 2)
                                brutto
                            @else
                                <i>nie zdefiniowano</i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Okres leasingu od:</label></td>
                        <Td>{{ $agreement->insurance_from }}</td>
                    </tr>
                    <tr>
                        <td><label>Okres leasingu do:</label></td>
                        <Td>{{ $agreement->insurance_to }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3 item-m">
            <div class="panel panel-default small">
                <div class="panel-body">
                    @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                        <span class="btn btn-primary btn-sm btn-block modal-open"
                        target="{{ URL::to('insurances/info-dialog/mark-as-foreign', [$agreement->id]) }}"
                        data-toggle="modal" data-target="#modal"><i
                              class="{{$agreement->if_foreign == 1 ? '': 'fa fa-globe'}}"></i> {{$agreement->if_foreign == 1? "Anuluj oznaczenie obca" : "Oznacz jako umowę obcą"}}</span>
                  @endif
                </div>
            </div>    
        </div>
    </div>


</div>
