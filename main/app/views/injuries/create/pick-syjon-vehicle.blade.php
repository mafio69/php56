@extends('layouts.main')

@section('header')
    Wybierz pojazd dla zgłaszanej szkody mobilnej
@stop

@section('main')
<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
        <div class="panel panel-info">
            <div class="panel-heading">
                Dane zgłoszenia
                -
                źródło zgłoszenia
                -

                @if($mobileInjury->source == 1)
                    formularz internetowy
                @else
                    aplikacja mobilna
                @endif
            </div>
            <div class="panel-body">
                @if($mobileInjury->source == 1)
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <h4>Dane zgłoszenia</h4>
                            <table class="table table-bordered">
                        <tr>
                            <td style="font-weight: bold;">Typ szkody:</td>
                            <td>
                                @if($mobileInjury->injuries_type == 2)
                                    komunikacyjna OC
                                @elseif($mobileInjury->injuries_type == 1)
                                    komunikacyjna AC
                                @elseif($mobileInjury->injuries_type == 3)
                                    komunikacyjna kradzież
                                @elseif($mobileInjury->injuries_type == 4)
                                    majątkowa
                                @elseif($mobileInjury->injuries_type == 5)
                                    majątkowa kradzież
                                @elseif($mobileInjury->injuries_type == 6)
                                    komunikacyjna AC - Regres
                                @endif
                            </td>
                        </tr>
                        <Tr>
                            <td style="font-weight: bold;">Nr umowy leasingu:</td>
                            <td>{{ $mobileInjury->nr_contract }}</td>
                        </Tr>
                        @if($mobileInjury->injuries_type == 4 || $mobileInjury->injuries_type == 5)
                            <Tr>
                                <td style="font-weight: bold;">Rodzaj przedmiotu leasingu:</td>
                                <td>{{ $mobileInjury->rdl }} </td>
                            </Tr>
                            <Tr>
                                <td style="font-weight: bold;">Nr identyfikacyjny przedmiotu leasingu:</td>
                                <td>{{ $mobileInjury->ipl }} </td>
                            </Tr>
                        @endif
                        <Tr>
                            <td style="font-weight: bold;">Towarzystwo Ubezpieczeniowe:</td>
                            <td>{{ $mobileInjury->name_zu }} </td>
                        </Tr>
                        <Tr>
                            <td style="font-weight: bold;">Data zdarzenia:</td>
                            <td>{{ $mobileInjury->date_event }} </td>
                        </Tr>
                        <Tr>
                            <td style="font-weight: bold;">Miejsce zdarzenia:</td>
                            <td>{{ $mobileInjury->event_city }} </td>
                        </Tr>
                        <Tr>
                            <td style="font-weight: bold;">Nr szkody:</td>
                            <td>{{ $mobileInjury->nr_injurie }} </td>
                        </Tr>
                        <Tr>
                            <td style="font-weight: bold;">Opis zdarzenia:</td>
                            <td>{{ $mobileInjury->desc_event }} </td>
                        </Tr>
                        <Tr>
                            <td style="font-weight: bold;">Lokalizacja przedmiotu leasingu:</td>
                            <td>{{ $mobileInjury->location_upl }} </td>
                        </Tr>

                        @if($mobileInjury->injuries_type == 3 || $mobileInjury->injuries_type == 5)
                            <Tr>
                                <td style="font-weight: bold;">Jednostka policji:</td>
                                <td>{{ $mobileInjury->police_unite }} </td>
                            </Tr>
                            <Tr>
                                <td style="font-weight: bold;">Nr sprawy policji:</td>
                                <td>{{ $mobileInjury->nr_case }} </td>
                            </Tr>
                            <Tr>
                                <td style="font-weight: bold;">Nr telefonu policji:</td>
                                <td>{{ $mobileInjury->policeman_phone }} </td>
                            </Tr>
                        @endif
                        <Tr>
                            <td style="font-weight: bold;">Warsztat naprawczy:</td>
                            <td>{{ $mobileInjury->company }} </td>
                        </Tr>
                    </table>
                        </div>
                        @if($mobileInjury->injuries_type == 0 || $mobileInjury->injuries_type == 1 || $mobileInjury->injuries_type == 2 || $mobileInjury->injuries_type == 3)
                            <div class="col-sm-6 col-lg-3">
                                <h3>Dane pojazdu</h3>
                                <div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
                                    <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="font-weight: bold;">Nr rejestracyjny:</td>
                                            <td>{{ $mobileInjury->registration }}</td>
                                        </tr>
                                        <Tr>
                                            <td style="font-weight: bold;">Marka:</td>
                                            <td>{{ $mobileInjury->marka }}</td>
                                        </Tr>
                                        <Tr>
                                            <td style="font-weight: bold;">Model:</td>
                                            <td>{{ $mobileInjury->model }}</td>
                                        </Tr>
                                    </table>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-6 col-lg-3">
                            <h3>Dane klienta</h3>
                            <div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
                                <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="font-weight: bold;">Nazwa :</td>
                                        <td>{{ $mobileInjury->name_client }}</td>
                                    </tr>
                                    <Tr>
                                        <td style="font-weight: bold;">Adres :</td>
                                        <td>{{ $mobileInjury->code_client }} {{ $mobileInjury->city_client }}, {{ $mobileInjury->adres_client }} </td>
                                    </Tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <h3>Dane zgłaszającego</h3>
                            <div style ="margin-top:20pt; font-size:9pt; text-align:justify;text-justify:inter-word; line-height: 10pt;">
                                <table class="pad-medium bordered-all" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="font-weight: bold;">Nazwisko:</td>
                                        <td>{{ $mobileInjury->notifier_surname }} {{ $mobileInjury->notifier_name }}</td>
                                    </tr>
                                    <Tr>
                                        <td style="font-weight: bold;">Telefon:</td>
                                        <td>{{ $mobileInjury->notifier_phone }} </td>
                                    </Tr>
                                    <Tr>
                                        <td style="font-weight: bold;">Email:</td>
                                        <td>{{ $mobileInjury->notifier_email }} </td>
                                    </Tr>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <h4>Dane pojazdu</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td style="font-weight: bold;">Nr rejestracyjny:</td>
                                    <td>{{ $mobileInjury->registration }}</td>
                                </tr>
                                <Tr>
                                    <td style="font-weight: bold;">Nr umowy:</td>
                                    <td>{{ $mobileInjury->nr_contract }}</td>
                                </Tr>
                            </table>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <h4>Dane klienta</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td style="font-weight: bold;">NIP :</td>
                                    <td>{{ $mobileInjury->nip }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <h4>Dane zgłaszającego</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td style="font-weight: bold;">Nazwisko:</td>
                                    <td>{{ $mobileInjury->notifier_surname }} {{ $mobileInjury->notifier_name }}</td>
                                </tr>
                                <Tr>
                                    <td style="font-weight: bold;">Telefon:</td>
                                    <td>{{ $mobileInjury->notifier_phone }} </td>
                                </Tr>
                                <Tr>
                                    <td style="font-weight: bold;">Email:</td>
                                    <td>{{ $mobileInjury->notifier_email }} </td>
                                </Tr>
                            </table>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <h4>Dane zgłoszenia</h4>
                            <table class="table table-bordered">
                                <Tr>
                                    <td style="font-weight: bold;">Rodzaj zdarzenia:</td>
                                    <td>{{ ($mobileInjury->injuries_type()->first()) ? $mobileInjury->injuries_type()->first()->name : ''}}</td>
                                </Tr>
                                <Tr>
                                    <td style="font-weight: bold;">Data zdarzenia:</td>
                                    <td>{{ $mobileInjury->date_event }} </td>
                                </Tr>
                                <Tr>
                                    <td style="font-weight: bold;">Miejsce zdarzenia:</td>
                                    <td>{{ $mobileInjury->event_city }} </td>
                                </Tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<h4>Dane pojazdów z Syjon</h4>
<?php $lp = 0;?>
<table class="table table-condensed table-contracts">
    <thead>
    <th>#</th>
    <th>Nr rej.</th>
    <th>Nr umowy</th>
    <th>Marka</th>
    <th>Model</th>
    <th>Właściciel</th>
    <th>Klient</th>
    <th>Status umowy</th>
    <th>Data ważności umowy</th>
    <th>Nazwa TU</th>
    <th></th>
    </thead>
    @foreach($contracts as $contract)
        @foreach($contract->vehicles as $vehicle)
            <tr
                @if( ($contract->contract_number == $mobileInjury->nr_contract && $mobileInjury->nr_contract != '')
                        ||
                    ($vehicle->registration == $mobileInjury->registration && $mobileInjury->registration != '')
                )
                    class="bg-info"
                @endif
            >
                <td>
                    {{ ++ $lp  }}.
                </td>
                <td>
                    <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/object/card/info/'.$vehicle->id }}" class="btn btn-xs btn-info" off-disable>
                        <i class="fa fa-search"></i>
                    </a>
                    {{ $vehicle->registration }}
                </td>
                <td>
                    <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/contract/card-file-external/info/'.$contract->id }}" class="btn btn-xs btn-info" off-disable>
                        <i class="fa fa-search"></i>
                    </a>
                    {{ $contract->contract_number }}
                </td>
                <td>
                    {{ $vehicle->brand }}
                </td>
                <td>
                    {{ $vehicle->model }}
                </td>
                <td>
                    <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/contractor/card-file/show/'.$contract->owner->contractor_id }}" class="btn btn-xs btn-info" off-disable>
                        <i class="fa fa-search"></i>
                    </a>
                    {{ $contract->owner->contractor_name }}
                </td>
                <td>
                    <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/contractor/card-file/show/'.$contract->object_user->contractor_id }}" class="btn btn-xs btn-info" off-disable>
                        <i class="fa fa-search"></i>
                    </a>
                    {{ $contract->object_user->contractor_name }}
                </td>
                <td>
                    {{ $contract->contract_status }}
                </td>
                <td>
                    {{ $contract->contract_planned_ending_date }}
                </td>
                <td>
                    @if(isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                        {{ $vehicle->contract_internal_agreements[0]->policies[0]->policy_insurance_company }}
                    @endif
                </td>
                <td>
                    <form action="{{ url('injuries/make/create-new-entity') }}" method="post">
                        {{ Form::token() }}
                        {{ Form::hidden('contract_id', $contract->id) }}
                        {{ Form::hidden('vehicle_id', $vehicle->id) }}
                        {{ Form::hidden('contract_internal_agreement_id', isset($vehicle->contract_internal_agreements[0]) ? $vehicle->contract_internal_agreements[0]->id : null) }}
                        @if(isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                            {{ Form::hidden('policy_id', $vehicle->contract_internal_agreements[0]->policies[0]->policy_id) }}
                        @endif
                        {{ Form::hidden('mobile_injury_id', $mobileInjury->id) }}
                        <button type="submit" class="btn btn-primary btn-xs">
                            PRZYJMIJ SZKODĘ
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    @endforeach
</table>
@endsection
