<div class="tab-pane fade in" id="injury-data">
    <div class="row">

        <div class="col-sm-6 col-md-4 col-lg-3 item-m">
            <!-- status umowy -->
            <div class="panel panel-default small">
                 <div class="panel-heading
                 @if(
                     str_contains(mb_strtoupper($object->contract_status, 'UTF-8'), 'AKTYWNA')
                 )
                 bg-success
                 @else
                 bg-danger
                 @endif
                 ">
                    Status umowy
                 </div>
                 <table class="table">
                    <tr>
                        <td><label>Status:</label></td>
                        <Td>{{ $object->contract_status }}</td>
                    </tr>
                    <tr>
                        <td><label>Data ważności:</label></td>
                        <td>{{ $object->end_leasing }}</td>
                    </tr>
                    <tr>
                        <td><label>Saldo:</label></td>
                        <td></td>
                    </tr>
                 </table>
            </div>

            <!-- dane klienta -->
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane klienta</div>
                <?php $client = $injury->client()->first();?>
                <table class="table">
                    <tr>
                        <td><label>Nazwa:</label></td>
                        <Td>{{ $client->name }}</td>
                    </tr>
                    <tr>
                        <td><label>NIP:</label></td>
                        <Td>{{ $client->NIP }}</td>
                    </tr>
                    <tr>
                        <td><label>Regon:</label></td>
                        <Td>{{ $client->REGON }}</td>
                    </tr>
                    <tr>
                        <td><label>Kod klienta:</label></td>
                        <Td>{{ $client->firmID }}</td>
                    </tr>
                    <tr>
                        <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                    </tr>
                    <tr>
                        <td><label>Kod pocztowy:</label></td>
                        <td>{{ $client->registry_post }}</td>
                    </tr>
                    <tr>
                        <td><label>Miato:</label></td>
                        <td>{{ $client->registry_city }}</td>
                    </tr>
                    <tr>
                        <td><label>Ulica:</label></td>
                        <td>{{ $client->registry_street }}</td>
                    </tr>
                    <tr>
                        <Td colspan="2">
                            <span class="sm-title">Adres kontaktowy:</span>

                        </td>
                    </tr>
                    <tr>
                        <td><label>Kod pocztowy:</label></td>
                        <td>{{ $client->correspond_post }}</td>
                    </tr>
                    <tr>
                        <td><label>Miato:</label></td>
                        <td>{{ $client->correspond_city }}</td>
                    </tr>
                    <tr>
                        <td><label>Ulica:</label></td>
                        <td>{{ $client->correspond_street }}</td>
                    </tr>
                    <tr>
                        <td><label>Telefon:</label></td>
                        <td>{{ $client->phone }}</td>
                    </tr>
                    <tr>
                        <td><label>Email:</label></td>
                        <td>{{ $client->email }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3 item-m">
            <!-- dane szkody -->
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane szkody:

                </div>
                <table class="table">
                    <tr>
                        <td><label>Typ szkody:</label></td>
                        <td>{{ $injury->injuries_type()->first()->name }}</td>
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
                        <td><label>Data zdarzenia:</label></td>
                        <Td>{{ $injury->date_event }}</td>
                    </tr>
                    <tr>
                        <td><label>Rodzaj zdarzenia:</label></td>
                        <Td>
                            @if( $injury->type_incident_id != 0 && $injury->type_incident_id != NULL)
                            {{ $injury->type_incident()->first()->name}}
                            @else
                            ---
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>ZU:</label></td>
                        <td>
                            @if($injury->object->insurance_company_id != 0)
                            {{ $injury->object->insurance_company()->first()->name }}
                            @else
                            ---
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Nr szkody:</label></td>
                        <td>
                            @if( $injury->injury_nr != '' && $injury->injury_nr != NULL)
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
                </table>
            </div>

            <!-- dane polisy -->
            @if($injury->injuries_type_id == 1)
            <div class="panel panel-default small">
                 <div class="panel-heading ">Dane polisy leasingowej

                 </div>
                 <table class="table">
                    <tr>
                        <td><label>Zakład ubezpieczeń:</label></td>
                        <Td>
                            @if($object->insurance_company_id != 0)
                            {{ $object->insurance_company()->first()->name }}
                            @else
                            ---
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Data ważności polisy:</label></td>
                        <td>{{ $object->end_leasing }}</td>
                    </tr>
                    <tr>
                        <td><label>Nr polisy:</label></td>
                        <td>{{ $object->nr_policy }}</td>
                    </tr>
                    <tr>
                        <td><label>Suma ubezpieczenia [zł]:</label></td>
                        <td>{{ $object->insurance }}</td>
                    </tr>
                    <tr>
                        <td><label>Wkład własny [zł]:</label></td>
                        <td>
                        {{ $object->contribution }}
                        </td>
                    </tr>
                    <tr>
                        <td><label>[netto/brutto]:</label></td>
                        <td>
                            @if( $object->netto_brutto == 1)
                            netto
                            @else
                            brutto
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>GAP:</label></td>
                        <td>
                            @if($object->gap == 0)
                            <i class="red">
                            @endif
                            {{ Config::get('definition.insurance_options_definition.'.$object->gap) }}
                            @if($object->gap == 0)
                            </i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><label>Ochrona prawna:</label></td>
                        <td>
                            @if($object->legal_protection == 0)
                                <i class="red">
                            @endif
                            {{ Config::get('definition.insurance_options_definition.'.$object->legal_protection) }}
                            @if($object->legal_protection == 0)
                                </i>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @endif
        </div>

        <div class="col-sm-6 col-md-4  col-lg-3 item-m">
            <!-- dane przedmiotu szkody -->
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane przedmiotu szkody</div>
                <table class="table">
                    <tr>
                        <td><label>Nr umowy leasingowej:</label></td>
                        <td>{{ $object->nr_contract }}</td>
                    </tr>
                    <tr>
                        <td><label>Opis:</label></td>
                        <td>{{ $object->description }}</td>
                    </tr>
                    <tr>
                        <td><label>Typ:</label></td>
                        <td>{{ $object->assetType }} </td>
                    </tr>
                    <tr>
                        <td><label>Nr fabryczny:</label></td>
                        <td>{{ $object->factoryNbr }}</td>
                    </tr>
                    <tr>
                        <td><label>Rok produkcji:</label></td>
                        <td>{{ $object->year_production }}</td>
                    </tr>
                </table>
            </div>

            <!-- dane zgłaszajacego -->
            <div class="panel panel-default small">
                <div class="panel-heading overflow">
                    <span class="pull-left">Dane zgłaszającego</span>
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

        </div>

        <div class="col-sm-6 col-md-4 col-lg-3 item-m">
            <!-- dane wlasciciela -->
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane właściciela</div>
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

                </div>
                @if($injury->offender_id != 0)
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
        </div>

    </div>

    <div class="row">
        <div class="col-sm-6  item-m">
            <div class="panel panel-default small">
               <div class="panel-heading ">Informacja wewnętrzna:

               </div>
               <table class="table">
                <?php if($injury->info != 0){?>
                <tr>
                  <td>{{ $info->content }}</td>
                </tr>
                <?php }?>
               </table>
            </div>
        </div>

        <div class="col-sm-6 item-m">
            <div class="panel panel-default small">
               <div class="panel-heading ">Opis szkody:</div>
               <table class="table">
                <?php if($injury->remarks != 0){?>
                <tr>
                  <td>{{ $remarks->content }}</td>
                </tr>
                <?php }?>
               </table>
            </div>
        </div>
    </div>

</div>
