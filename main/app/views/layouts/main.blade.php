@extends('layouts.base')

@section('content')
<div id="wrapper">
	<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;    background-color: white;">
	    <div class="navbar-header" style="height: 50px;">
	        <a class="navbar-brand" href="/" style="padding:10px;">
	        	{{ HTML::image('images/cas.jpg', 'Logo', array('style' => 'height: 35px;')) }}
	        </a>
            <span class="navbar-brand pointer btn @if( Session::get('task.visibility', false)) btn-open @endif" id="show-tasks">
                Zadania
                <span class="badge">
                    {{  \Idea\Tasker\Tasker::stats()['new'] +   \Idea\Tasker\Tasker::stats()['inprogress'] }}
                </span>
            </span>

                <span class="navbar-brand pointer btn btn-default upload-btn-wrapper" >
                    <i class="fa fa-plus"></i>
                     <form action="{{ url('tasks/upload-email') }}" enctype="multipart/form-data" method="POST" id="task-file-form">
                        {{ Form::token() }}
                        <input type="file" name="task_file" />
                     </form>
                </span>
	    </div>

	    <!-- /.navbar-header -->
	    <ul class="nav navbar-top-links navbar-right">
	        <!-- /.dropdown -->
            @if(Config::get('webconfig.WEBCONFIG_SETTINGS_bramka_sms') == 1 && Auth::user()->can('menu_gorne#bramka_sms#wejscie'))
            <li>
                <a href="/sms"><i class="fa fa-envelope-o fa-fw"></i></i> <span class="nav-desc">Bramka sms</span></a>
            </li>
            @endif

            @if(Auth::user()->can('ustawienia#wejscie'))
            <li class="dropdown ">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-cogs fa-fw"></i> <span class="nav-desc">Ustawienia</span> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    @if(Auth::user()->can('uzytkownicy#lista_uzytkownikow#wejscie'))
                        <li>
                            <a href="/settings/users"><i class="fa fa-users fa-fw"></i> Użytkownicy</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('grupy#lista_grup#wejscie'))
                        <li>
                            <a href="/settings/user/groups"><i class="fa fa-cubes fa-fw"></i> Grupy</a>
                        </li>
                    @endif
                    <li role="separator" class="divider"></li>
{{--                    <li>--}}
{{--                        <a href="/settings/dok_processes"><i class="fa fa-list fa-fw"></i> Procesy DOK</a>--}}
{{--                    </li>--}}
                    @if(Auth::user()->can('lista_ubezpieczalni#wejscie'))
                        <li>
                            <a href="/settings/insurance_companies"><i class="fa fa-list fa-fw"></i> Lista ubezpieczalni</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('lista_marek_samochodow#wejscie'))
                        <li>
                            <a href="/settings/brands"><i class="fa fa-truck fa-fw"></i> Lista marek samochodów</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('edycja_danych_rejestrowych_idea#wejscie'))
                        <li>
                            <a href="/settings/idea_data"><i class="fa fa-pencil-square-o fa-fw"></i> Edycja danych rejestrowych Idea</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('baza_oddzialow_idealeasing#wejscie'))
                        <li>
                            <a href="/settings/idea_offices"><i class="fa fa-building-o fa-fw"></i> Baza oddziałów IdeaLeasing</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('edycja_procesow#wejscie'))
                        <li>
                            <a href="/settings/processes"><i class="fa fa-sitemap fa-fw"></i> Edycja procesów</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('edycja_godzin_pracy#wejscie'))
                        <li>
                            <a href="/settings/hours"><i class="fa fa-calendar fa-fw"></i> Edycja godzin pracy</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('szablony_sms#wejscie'))
                        <li>
                            <a href="/settings/sms-templates"><i class="fa fa-envelope-o fa-fw"></i> Szablony SMS</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('reklamy_aplikacji_mobilnej#wejscie'))
                        <li>
                            <a href="/settings/adverts"><i class="fa fa-file-image-o fa-fw"></i> Reklamy aplikacji mobilnej</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('karty_likwidacji_szkod#wejscie'))
                        <li class="dropdown-submenu">
                            <a href="#"><i class="fa fa-credit-card fa-fw"></i> Karty likwidacji szkód</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ URL::route('settings.liquidation_cards', array('index')) }}"><i class="fa fa-list fa-fw"></i> Lista kart</a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('settings.liquidation_cards', array('create')) }}"><i class="fa fa-plus-square-o fa-fw"></i> Dodaj kartę</a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('settings.liquidation_cards', array('report')) }}"><i class="fa fa-files-o fa-fw"></i> Raporty</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('przypisywanie_raportow#wejscie'))
                        <li>
                            <a href="{{ URL::route('settings.custom_reports', array('index')) }}"><i class="fa fa-files-o fa-fw"></i> Przypisywanie raportów</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('dostepnosc_dokumentow#wejscie'))
                        <li class="dropdown-submenu">
                            <a href="#"><i class="fa fa-file-text-o fa-fw"></i> Dostępność dokumentów</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ URL::route('settings.documents', array('injuries')) }}"><i class="fa fa-car fa-fw"></i> DLS Pojazdy</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('zarzadzanie_etapami#wejscie'))
                        <li class="dropdown-submenu">
                            <a href="#"><i class="fa fa-car fa-fw"></i> Zarządzanie etapami</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ URL::route('routes.get', array('settings', 'stages', 'index')) }}"> Szkody zwykłe</a>
                                    <a href="{{ URL::route('routes.get', array('settings','stages', 'total-status')) }}"> Szkody całkowite</a>
                                    <a href="{{ URL::route('routes.get', array('settings','stages', 'theft-status')) }}"> Kradzież</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li>
                        <a href="{{ url('settings/document-templates') }}">
                            <i class="fa fa-file-word-o fa-fw"></i> Wzory dokumentów
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-submenu">
                        <a href="#"><i class="fa fa-book fa-fw"></i> Słowniki</a>
                        <ul class="dropdown-menu">
                            @if(Auth::user()->can('slownik_aneksow_ubezpieczen#wejscie'))
                                <li>
                                    <a href="/settings/insurance-annex-refer/index"><i class="fa fa-list fa-fw"></i> Aneksy ubezpieczeń </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ url('settings/contractor-groups') }}">
                                    <i class="fa fa-list fa-fw"></i> Grupy kotrahentów
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('settings/sales-programs') }}">
                                    <i class="fa fa-list fa-fw"></i> Kody programów sprzedaży
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('settings/departments') }}">
                                    <i class="fa fa-list fa-fw"></i> Działy
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('settings/teams') }}">
                                    <i class="fa fa-list fa-fw"></i> Zespoły
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-submenu">
                        <a href="#"><i class="fa fa-code fa-fw"></i> API</a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ URL::to('settings/api/modules') }}"> Moduły API</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('settings/api/users') }}"> Konta API</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->can('dls_pojazdy#wejscie'))
           	<li class="dropdown" >
           		<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-car fa-fw"></i> <span class="nav-desc">DLS Pojazdy</span> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    @if(Auth::user()->can('zlecenia_(szkody)#wejscie'))
                        <li>
                            <a
                                @if(Auth::user()->can('zlecenia_(szkody)#szkody_zarejestrowane'))
                                    href="/injuries/new"
                                @elseif(Auth::user()->can('zlecenia_(szkody)#szkody_calkowite'))
                                    href="{{ URL::route('injuries-total' ) }}"
                                @elseif(Auth::user()->can('zlecenia_(szkody)#szkody_anulowane'))
                                    href="{{ URL::route('injuries-canceled' ) }}"
                                @endif
                            >
                                <i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Zlecenia (szkody)</span>
                            </a>
                        </li>
                    @endif
                    <li role="separator" class="divider"></li>
                    @if(Auth::user()->can('programy#wejscie'))
                        <li>
                            <a href="/plans">
                                <i class="fa fa-th fa-fw"></i>
                                Programy
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('serwisy#wejscie'))
                        <li>
                            <a href="/companies/index"><i class="fa fa-bars fa-fw"></i> Serwisy</a>
                        </li>
                    @endif
                    @if(Auth::user()->can('mapa_serwisow#wejscie'))
                        <li>
                            <a href="{{ URL::action('CompaniesController@getMap')}}"><i class="fa fa-globe fa-fw"></i> Mapa serwisów</a>
                        </li>
                    @endif

                    @if(Auth::user()->can('prowizje#wejscie'))
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="{{ url('commissions/new') }}">
                                <i class="fa fa-dollar fa-fw"></i> Prowizje
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->can('pojazdy#raporty#wejscie'))
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-submenu">
                            <a href="#"><i class="fa fa-file-excel-o fa-fw"></i> Raporty</a>
                            <ul class="dropdown-menu" style="min-width: 200px;">
                                <li>
                                    <a href="{{ URL::route('reports.injuries.get', ['index']) }}"><i class="fa fa-files-o fa-fw"></i> Raporty</a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('reports.custom.get', array('index')) }}">
                                        <i class="fa fa-file-excel-o fa-fw"></i> <span class="nav-desc">Raporty specjalne</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('reports.injuries.get', ['custom']) }}"><i class="fa fa-file-o fa-fw"></i> Raport konfigurowalny</a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('reports.injuries.get', ['sap']) }}"><i class="fa fa-file-o fa-fw"></i> Raport braku polisy w SAP</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if(Auth::user()->can('baza_pism#wejscie'))
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="{{ URL::route('routes.get', ['injuries', 'letters', 'non-appended'])}}"><i class="fa fa-file-o fa-fw"></i> <span class="nav-desc">Baza pism</span></a>
                        </li>
                    @endif

                    @if(Auth::user()->can('zarzadzanie_nabywcami#wejscie'))
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="{{ URL::to('injuries/buyers')}}">
                                <i class="fa fa-list fa-fw"></i> <span class="nav-desc">Zarządzanie nabywcami</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
            @endif

            @if(Config::get('webconfig.WEBCONFIG_SETTINGS_dos_pozostale') == 1 && Auth::user()->can('dls_majatek#wejscie'))
                <li class="dropdown" >
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-archive fa-fw"></i> <span class="nav-desc">DLS Majątek</span> <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu">
                        @if(Auth::user()->can('zlecenia#wejscie'))
                            <li>
                                <a
                                    @if(Auth::user()->can('zlecenia#szkody_zarejestrowane'))
                                        href="{{ URL::route('dos.other.injuries.new') }}"
                                    @elseif(Auth::user()->can('zlecenia#szkody_calkowite#kradzieze'))
                                        href="{{ URL::route('dos.other.injuries.total') }}"
                                    @elseif(Auth::user()->can('zlecenia#szkody_anulowane'))
                                        href="{{ URL::route('dos.other.injuries.canceled') }}"
                                    @elseif(Auth::user()->can('zlecenia#szkody_nieprzetworzone'))
                                        href="{{ URL::route('dos.other.injuries.unprocessed') }}"
                                    @endif
                                >
                                    <i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Zlecenia</span>
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->can('majatek#raporty#wejscie'))
                            <li>
                                <a href="{{ URL::to('dos/other/reports') }}"><i class="fa fa-files-o fa-fw"></i> Raporty</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if( Config::get('webconfig.WEBCONFIG_SETTINGS_dok') == 1)
            <li class="dropdown" >
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-th-list fa-fw"></i> <span class="nav-desc">DOK</span> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                	<li>
		            	<a href="{{ URL::route('dok.notifications.new') }}"><i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Zgłoszenia</span></a>
		           	</li>
		           	<li>
		            	<a href="{{ URL::route('dok.notifications-user.new', ['0']) }}"><i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Widok pracownika</span></a>
		           	</li>
                </ul>
            </li>
            @endif

            @if( Config::get('webconfig.WEBCONFIG_SETTINGS_zarzadzanie_pojazdami') == 1 && Auth::user()->can('zarzadzanie_pojazdami#wejscie'))
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-database fa-fw"></i> <span class="nav-desc">Zarządzanie pojazdami</span> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ URL::action('VmanageCompaniesController@getIndex') }}"><i class="fa fa-cubes fa-fw"></i> <span class="nav-desc">Baza firm</span></a>
                    </li>
                    <li>
                        <a href="{{ URL::action('VmanageVehiclesController@getIndex') }}"><i class="fa fa-cubes fa-fw"></i> <span class="nav-desc">Zarządzana flota</span></a>
                    </li>
                    <li>
                        <a href="{{ URL::action('VmanageImportController@getGetin') }}">
                            <i class="fa fa-upload fa-fw"></i> <span class="nav-desc">Import pojazdów getin > 3.5t</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ URL::action('VmanageImportController@getTrucks') }}">
                            <i class="fa fa-upload fa-fw"></i> <span class="nav-desc">Import pojazdów < 3.5t</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ URL::action('VmanageImportController@getVipClients') }}">
                                <i class="fa fa-upload fa-fw"></i> <span class="nav-desc">Import klientów VIP</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if( Config::get('webconfig.WEBCONFIG_SETTINGS_zarzadzanie_polisami') == 1 && Auth::user()->can('zarzadzanie_polisami#wejscie'))
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-files-o fa-fw"></i> <span class="nav-desc">Zarządzanie polisami</span> <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu">
                        @if(Auth::user()->can('wykaz_polis#wejscie'))
                            <li>
                                <a href="{{ URL::to('insurances/manage/index') }}"><i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Wykaz polis</span></a>
                            </li>
                        @endif
                        @if(Auth::user()->can('wykaz_stawek#wejscie'))
                            <li>
                                <a href="{{ URL::to('insurances/groups/index') }}"><i class="fa fa-table fa-fw"></i> <span class="nav-desc">Wykaz stawek</span></a>
                            </li>
                        @endif
                        @if(Auth::user()->can('wykaz_franszyz#wejscie'))
                            <li>
                                <a href="{{ URL::to('insurances/deductible/index') }}"><i class="fa fa-th fa-fw"></i> <span class="nav-desc">Wykaz franszyz</span></a>
                            </li>
                        @endif
                        @if(Auth::user()->can('raporty#wejscie'))
                            <li>
                                <a href="{{ URL::to('insurances/reports/index') }}"><i class="fa fa-file-excel-o fa-fw"></i> <span class="nav-desc">Raporty</span></a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if( Config::get('webconfig.WEBCONFIG_SETTINGS_gap') == 1)
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-th-large fa-fw"></i> <span class="nav-desc">GAP</span> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ url('gap/agreements/new') }}"><i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Wykaz umów</span></a>
                    </li>
                    <li>
                        <a href="{{ url('gap/contributionRates/index') }}"><i class="fa fa-th fa-fw"></i> <span class="nav-desc">Konfiguracja stawek</span></a>
                    </li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->can('menu_gorne#zadania#wejscie'))
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-tasks fa-fw"></i> <span class="nav-desc">Zadania</span> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    @if(Auth::user()->can('wykaz_zadan#wejscie'))
                    <li>
                        <a href="{{ url('tasks/list/new') }}"><i class="fa fa-folder-open-o fa-fw"></i> <span class="nav-desc">Wykaz zadań</span></a>
                    </li>
                    @endif
                    @if(Auth::user()->can('raporty#wejscie'))
                    <li>
                        <a href="{{ url('tasks/reports') }}">
                            <i class="fa fa-file-excel-o fa-fw"></i>
                            <span class="nav-desc">Raporty</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('skrzynki_pocztowe#wejscie'))
                    <li>
                        <a href="{{ url('tasks/mailboxes') }}">
                            <i class="fa fa-fw fa-envelope-o"></i>
                            <span class="nav-desc">Skrzynki pocztowe</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('grupy_zadan#wejscie'))
                    <li>
                        <a href="{{ url('tasks/types') }}">
                            <i class="fa fa-fw fa-list-alt"></i>
                            <span class="nav-desc">Grupy zadań</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('przypisania_indywidualne#wejscie'))
                    <li>
                        <a href="{{ url('tasks/assignments') }}">
                            <i class="fa fa-fw fa-users"></i>
                            <span class="nav-desc">Przypisania indywidualne</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('zadania#nieobecnosci_pracownikow#wejscie'))
                    <li>
                        <a href="{{ url('tasks/excludes') }}">
                            <i class="fa fa-fw fa-calendar"></i>
                            <span class="nav-desc">Nieobecności pracowników</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('zadania#czarna_lista#wejscie'))
                    <li>
                        <a href="{{ url('tasks/black-list') }}">
                            <i class="fa fa-fw fa-ban"></i>
                            <span class="nav-desc">Czarna lista</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('zadania#slownik_typow_spraw#wejscie'))
                    <li>
                        <a href="{{ url('tasks/manage-types') }}">
                            <i class="fa fa-list fa-fw"></i>
                            <span class="nav-desc">Słownik typów spraw</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('zadania#ksiazka_adresowa#wejscie'))
                    <li>
                        <a href="{{ url('tasks/address-book') }}">
                            <i class="fa fa-fw fa-book"></i>
                            <span class="nav-desc">Książka adresowa</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

	        <li class="dropdown " >
	            <a class="dropdown-toggle " data-toggle="dropdown" href="#" >
	                <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
	            </a>
	            <ul class="dropdown-menu">
                    <li><span class="user-menu">{{ Auth::user()->name }}</span></li>
                    <li class="divider"></li>
                    <li><a href="{{ URL::to('password') }}" class="text-center"><i class="fa fa-key fa-fw"></i> Zmień hasło</a></li>
	                <li><a href="{{ URL::route('logout') }}" class="text-center"><i class="fa fa-sign-out fa-fw"></i> Wyloguj</a></li>
	            </ul>
	        </li>
	    </ul>
	    <!-- /.navbar-top-links -->
	</nav>
	<!-- /.navbar-static-top -->

	@yield('left-nav')

    <div id="split-container">
        @include('tasks.module.index')
        <div class="split" id="page-wrapper" style="overflow: auto;">
            <div class="row">
                <div class="col-sm-12">
                    @if(Config::get('webconfig.WEBCONFIG_SETTINGS_as') == 0)
                        <span class="label label-warning system-warning pull-left"><i class="fa fa-exclamation-triangle"></i> Wyłączono połączenie z AS</span>
                    @endif
                    @if(Settings::get('as-connection') == 'inactive')
                        <span class="label label-danger system-warning pull-right">
                            <i class="fa fa-exclamation-triangle marg-left"></i> Wykryto błąd połączenia z AS
                            <span class="marg-left marg-right">|</span>
                            <i class="fa fa-refresh tips marg-right pointer" id="refresh-as-connection-status" title="sprawdź status połączenia" data-placement="left" ></i>
                        </span>
                    @endif
                </div>
                <div class="col-sm-12">


                    @include('modules.flash_notification')

                    <h2 class="page-header " style="margin: 20px 0 20px; width: 100%; position: relative; display: inline-block;">
                        @yield('header')
                    </h2>
                    @yield('sub-header')
                    @yield('main')
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('headerJs')
    @parent
    <script>
        $(document).ready(function (){
            $('input[name="task_file"]').change(function() {
                $('form#task-file-form').submit();
            });
        });

    </script>
@endsection