<ul class="nav nav-tabs" id="info_tabs">
    @if(Auth::user()->can('kartoteka_szkody#komunikator'))
        <li class="active"><a href="#communicator" data-toggle="tab">Komunikator</a></li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#dane_szkody'))
        <li><a href="#injury-data" data-toggle="tab">Dane szkody i pojazdu</a></li>
    @endif
    @if($injury->vehicle_type == 'VmanageVehicle')
        <li><a href="#csm" data-toggle="tab">Info Flota</a></li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#lokalizacja_zdarzenia'))
        <li><a href="#localization" data-toggle="tab">Lokalizacja zdarzenia</a></li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#uszkodzenia'))
        <li><a href="#damage" data-toggle="tab">Uszkodzenia</a></li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#dokumentacja'))
        <li>
            <a href="#documentation" data-toggle="tab">
                Dokumentacja <span class="badge">{{ $documents->count() }}</span>
            </a>
        </li>
    @endif
    @if(
        $branch &&
            (
                ( $branch->company->groups->contains(1) || ( $branch->company->groups->contains(5) && $injury->vehicle->cfm == 1 ) )
                &&
                ( isset($genDocumentsA[60]) || isset($genDocumentsA[52]) || isset($genDocumentsA[6]) || isset($genDocumentsA[49]))
            )
        &&
        ! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] )
    )
    <li><a href="#repair_stages" data-toggle="tab">Etapy naprawy</a></li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody'))
        <li>
            <a href="#settlements" data-toggle="tab">Rozliczenia szkody
                @if(count($injury->compensations) > 0)
                    <span class="badge">
                        <i class="fa fa-exclamation"></i>
                    </span>
                @endif
            </a>
        </li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#zdjecia'))
        <li>
            <a href="#photos" data-toggle="tab">Zdjęcia
                <span class="badge">{{ $imagesBefore->count() + $imagesInprogress->count() + $imagesAfter->count() }}</span>
            </a>
        </li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#generowanie_dokumentow') && !is_null($documentsTypes))
        <li><a href="#gen_docs" data-toggle="tab">Generowanie dokumentów</a></li>
    @endif

    @if(Auth::user()->can('kartoteka_szkody#sprzedaz_wraku') && (in_array($injury->step, [30,31,32,33,34,35,36,37]) || $injury->wreck) )
    <li><a href="#selling_wreck" data-toggle="tab">Sprzedaż wraku</a></li>
    @endif

    @if($injury->wreck && Auth::user()->can('kartoteka_szkody#bilans_sprzedazy'))
    <li><a href="#balance_wreck" data-toggle="tab">Bilans sprzedaży</a> </li>
    @endif

    @if( (in_array($injury->step, [30,31,32,33,34,35,36,37]) && $injury->getDocument(3,15)->first()) || $injury->totalRepair)
    <li><a href="#repair_total" data-toggle="tab">Naprawa po szk. całk.</a></li>
    @endif

    @if(Auth::user()->can('kartoteka_szkody#kradziez') && (in_array($injury->step, [40,41,42,43,44,45,46]) || $injury->theft ) )
    <li><a href="#theft" data-toggle="tab">Kradzież</a></li>
    @endif

    @if(Config::get('webconfig.WEBCONFIG_SETTINGS_bramka_sms') == 1 && Auth::user()->can('kartoteka_szkody#bramka_sms') )
    <li><a href="#sms" data-toggle="tab">Bramka SMS</a></li>
    @endif
    @if(Auth::user()->can('kartoteka_szkody#historia'))
        <li><a href="#history" data-toggle="tab">Historia</a></li>
        <li><a href="#step_history" data-toggle="tab">Historia statusów</a></li>
    @endif
    <li><a href="#notes" data-toggle="tab">Notatki SAP <span class="badge">{{ $injury->notes->count() }}</span></a></li>
    <li><a href="#premiums" data-toggle="tab">Wypłaty z SAP <span class="badge">{{ $injury->sapPremiums->count() + ( ($injury->sap && $injury->sap->kwotaOdsz > 0) ? 1 : 0 ) }}</span></a></li>
    <li><a href="#tasks" data-toggle="tab">Zadania <span class="badge">{{ $injury->tasks->count() }}</span></a></li>
</ul>
