<ul class="nav nav-tabs" id="info_tabs">
    <li class="active"><a href="#communicator" data-toggle="tab">Komunikator</a></li>
    <li><a href="#injury-data" data-toggle="tab">Dane szkody i przedmiotu leasingu</a></li>
    <li><a href="#localization" data-toggle="tab">Lokalizacja zdarzenia</a></li>
    <li><a href="#documentation" data-toggle="tab">Dokumentacja</a></li>
    <li><a href="#settlements" data-toggle="tab">Rozliczenia</a></li>
    <li><a href="#photos" data-toggle="tab">Zdjęcia</a></li>
    <li><a href="#gen_docs" data-toggle="tab">Generowanie dokumentów</a></li>

    @if( $injury->step == '-9' || $injury->theft)
        <li><a href="#theft" data-toggle="tab">Kradzież</a></li>
    @endif

    @if(Config::get('webconfig.WEBCONFIG_SETTINGS_bramka_sms') == 1 )
    <li><a href="#sms" data-toggle="tab">Bramka SMS</a></li>
    @endif
    <li><a href="#history" data-toggle="tab">Historia</a></li>
</ul>
