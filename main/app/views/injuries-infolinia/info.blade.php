@extends('layouts.main')

@section('header')

Kartoteka szkody nr {{$injury->case_nr}}

<div class="pull-right">
  <a href="/injuries/search" class="btn btn-default">Powrót</a>      
</div>
@stop

@section('main')
  {{Form::token()}}
  <?php $vehicle = $injury->vehicle()->first();?>
  <?php $owner = $injury->vehicle()->first()->owner()->first();?>
  <?php $driver = $injury->driver()->first();?>
  <?php $branch = $injury->branch()->first();?>
  <ul class="nav nav-tabs" id="info_tabs">
    <li class="active"><a href="#communicator" data-toggle="tab">Komunikator</a></li>
    <li ><a href="#injury-data" data-toggle="tab">Dane szkody i pojazdu</a></li>
    <li><a href="#documentation" data-toggle="tab">Dokumentacja</a></li>
    <li><a href="#history" data-toggle="tab">Historia</a></li>
  </ul>  
  <div class="tab-content">
      <div class="tab-pane fade in " id="injury-data">
        <div class="row">

            <div class="col-sm-6 col-md-4 col-lg-3 item-m">
                <!-- status umowy -->
                <div class="panel panel-default small">
                    <div class="panel-heading
                    @if(
                        str_contains(mb_strtoupper($vehicle->contract_status, 'UTF-8'), 'AKTYWNA')
                    )
                    bg-success
                    @else
                    bg-danger
                    @endif
                    ">Status umowy</div>
                    <table class="table">
                        <tr>
                            <td><label>Status:</label></td>
                            <Td>{{ $vehicle->contract_status }}</td>
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
                    <div class="panel-heading ">Dane szkody:</div>
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
                              @if( $injury->if_courtesy_car != 1 )
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
                    </table>
                </div>

                <div class="panel panel-default small">
                    <!-- dane polisy -->
                    <div class="panel-heading ">Dane polisy leasingowej</div>
                    @if($injury->injuries_type_id == 1)
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
                            <td>{{ $vehicle->end_leasing }}</td>
                        </tr>
                        <tr>
                            <td><label>Nr polisy:</label></td>
                            <td>{{ $vehicle->nr_policy }}</td>
                        </tr>
                        <tr>
                            <td><label>Suma ubezpieczenia [zł]:</label></td>
                            <td>{{ $vehicle->insurance }}</td>
                        </tr>
                        <tr>
                            <td><label>Wkład własny [zł]:</label></td>
                            <td>
                                {{ $vehicle->contribution }}
                            </td>
                        </tr>
                        <tr>
                            <td><label>[netto/brutto]:</label></td>
                            <td>
                                {{ Config::get('definition.compensationsNetGross')[$vehicle->netto_brutto] }}
                            </td>
                        </tr>
                        <tr>
                            <td><label>Assistance:</label></td>
                            <td>
                                @if($vehicle->assistance == 1)
                                    {{ $vehicle->assistance_name}}
                                @else
                                    nie
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>GAP:</label></td>
                            <td>
                                @if($vehicle->gap == 0)
                                    <i class="red">
                                @endif
                                {{ Config::get('definition.insurance_options_definition.'.$vehicle->gap) }}
                                @if($vehicle->gap == 0)
                                    </i>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><label>Ochrona prawna:</label></td>
                            <td>
                                @if($vehicle->legal_protection == 0)
                                    <i class="red">
                                @endif
                                {{ Config::get('definition.insurance_options_definition.'.$vehicle->legal_protection) }}
                                @if($vehicle->legal_protection == 0)
                                    </i>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>

            <div class="col-sm-6 col-md-4  col-lg-3 item-m">
                <!-- dane pojazdu -->
                <div class="panel panel-default small">
                    <div class="panel-heading ">Dane pojazdu</div>
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
                            <td><label>VIN:</label></td>
                            <td>{{ $vehicle->VIN }}</td>
                        </tr>
                        <tr>
                            <td><label>Marka i model:</label></td>
                            <td>{{ checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand) }} {{ checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model) }}</td>
                        </tr>
                        <tr>
                            <td><label>Silnik:</label></td>
                            <td>{{ $vehicle->engine }}</td>
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
                            <td><label>Przebieg:</label></td>
                            <td>{{ $vehicle->mileage }}</td>
                        </tr>
                    </table>
                </div>

                <!-- dane zgłaszajacego -->
                <div class="panel panel-default small">
                    <div class="panel-heading overflow">
                        <span class="pull-left">Dane zgłaszającego</span>
                        @if($injury->contact_person == 2)
                        <i class="fa fa-phone blue pull-left "></i>
                        @elseif($injury->contact_person == 1)
                        <span class="ico-md phone_empty "  ></span>
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
                        <i class="fa fa-phone blue pull-left "></i>
                        @elseif($injury->contact_person == 2)
                        <span class="ico-md phone_empty " ></span>
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
                            <td><label>Miato:</label></td>
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
                    <div class="panel-heading ">Dane sprawcy</div>
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

                <!-- przypisany serwis -->
                <?php if($injury->branch_id != 0 && $injury->branch_id != NULL){?>
                    <div class="panel panel-default small">
                        <div class="panel-heading ">Przypisany serwis</div>
                        @if($injury->branch_id != '-1')
                            <table class="table">
                                <tr>
                                    <td><label>Skrócona nazwa:</label></td>
                                    <Td>{{ $branch->short_name }}</td>
                                </tr>
                                <tr>
                                    <td><label>Adres:</label></td>
                                    <td>{{ $branch->post }} {{$branch->city}}, {{$branch->street}}</td>
                                </tr>
                                <tr>
                                    <td><label>Telefon:</label></td>
                                    <td>{{ $branch->phone }}</td>
                                </tr>
                                <tr>
                                    <td><label>Email:</label></td>
                                    <td>{{ $branch->email }}</td>
                                </tr>
                            </table>
                        @else
                            <h4 class="text-center"><i class="red">Szkoda procedowana bez serwisu</i></h4>
                        @endif
                    </div>
                <?php }?>
            </div>

        </div>

        <div class="row">
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
     
      <div class="tab-pane fade in " id="documentation">
        
        <div class="row">
          <div class="col-sm-8 col-lg-6 col-sm-offset-2 col-lg-offset-3">
          <table class="table table-hover" >
            @foreach($documents as $k => $v)
              <tr>
                <td width="10px">{{++$k}}.</td>
                
                <td>
                  @if($v->type == 2)                  
                  {{ $v->uploadedDocumentType->name }}<br>
                  <i>{{ $v->name }}</i>
                  @else
                  {{ $v->uploadedDocumentType->name }}
                  @endif
                </td>
                <Td>
                  {{ $v->user->name }}
                </td>
                <Td>
                  {{substr($v->created_at, 0, -3)}}
                </td>
                
              </tr>
            @endforeach
          </table>
          </div>
        </div>
      </div>

      <div class="tab-pane fade in active" id="communicator">
        
        <div class="panel">
          <div class="panel-body">
            <div class="clearfix">
              <h3 class="media-heading"><small>
                <span class="label label-primary">DOS</span>
                <span class="label label-success">Infolinia</span>
                <span class="label label-info">Warsztat</span>
                </small>
                <div class="pull-right">
                  <span class="btn btn-warning btn-sm create-chat modal-open" target="{{ URL::route('chat.create', array($injury->id)) }}" data-toggle="modal" data-target="#modal">
                    <i class="fa fa-comment-o"></i> Dodaj temat/zadanie
                  </span>
                </div>
              </h3>

              
            </div>          
            <ul class="timeline">
              <?php $lp = 0;?>
              @foreach($chat as $k => $conversation)
                <?php $status = get_receivers($conversation->status); ?>
                @if($status[get_chat_group()-1] == 1)
                  
                  <li
                  @if($lp % 2 == 1)
                    class="timeline-inverted"
                  @endif
                  >
                    <div class="timeline-badge 
                    @if($conversation->user->typ() == 2 )
                      info
                    @elseif($conversation->user->typ() == 1 )
                      primary
                    @elseif($conversation->user->typ() == 3 )
                      success
                    @endif
                    "><i class="fa fa-comments-o"></i>
                    </div>
                    <div class="timeline-panel">
                      <div class="timeline-heading">
                          <h4 class="timeline-title">{{ $conversation->topic }}
                            <div class="pull-right">
                              @if($conversation->active == 0)
                              <span class="btn btn-warning btn-xs create-chat modal-open" target="{{ URL::route('chat.replay', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-reply"></i> Odpowiedz
                              </span>
                              @endif
                            </div>
                          </h4>
                      </div>
                      <div class="timeline-body">
                        <ul class="chat">
                          @foreach($conversation->messages as $k2 => $message)
                            <?php $status = get_receivers($message->status); ?>
                            @if($status[get_chat_group()-1] == 1)
                              <li class="left clearfix">
                                <div class="pull-left">
                                  <div class="chat-message
                                    @if($message->user->typ() == 2 )
                                      message-info
                                    @elseif($message->user->typ() == 1 )
                                      message-primary
                                    @elseif($message->user->typ() == 3 )
                                      message-success
                                    @endif
                                    " >
                                    <i class="fa fa-comment-o "></i>
                                  </div>

                                </div>
                                <div class="chat-body clearfix">
                                  <p class="pull-right timeline-timer">
                                      <small class="text-muted" style="padding-top: 2px;">                                  
                                        <i class="fa fa-clock-o fa-fw"></i> {{ substr($message->created_at, 0, -3) }}
                                      </small>

                                      @if($status[0] == 1 && $message->user->typ() != 1 && get_chat_group() != 1)
                                      <span class="time-container">
                                        <span class="label label-primary  legend-label" >
                                          @if($message->dos_read == '')
                                            <i class="fa fa-thumbs-o-down"></i>
                                          @else
                                            <i class="fa fa-thumbs-o-up"></i>
                                          @endif
                                        </span>
                                        @if($message->dos_read == '')
                                          <small class="text-muted ">...</small>
                                        @else
                                          <small class="text-muted ">{{ substr($message->dos_read, 0, -3) }}</small>
                                        @endif
                                      </span>
                                      @endif

                                      @if($status[2] == 1 && $message->user->typ() != 3 && get_chat_group() != 3)
                                      <span class="time-container">
                                        <span class="label label-success  legend-label">
                                            @if($message->info_read == '')
                                              <i class="fa fa-thumbs-o-down"></i>
                                            @else
                                              <i class="fa fa-thumbs-o-up"></i>
                                            @endif                                       
                                        </span>
                                        @if($message->info_read == '')
                                          <small class="text-muted ">...</small>
                                        @else
                                          <small class="text-muted ">{{ substr($message->info_read, 0, -3) }}</small>
                                        @endif  
                                      </span>
                                      @endif

                                      @if($status[1] == 1 && $message->user->typ() != 2 && get_chat_group() != 2)
                                     <span class="time-container">
                                        <span class="label label-info  legend-label">
                                          @if($message->branch_read == '')
                                              <i class="fa fa-thumbs-o-down"></i>
                                            @else
                                              <i class="fa fa-thumbs-o-up"></i>
                                            @endif
                                        </span>
                                        @if($message->branch_read == '')
                                          <small class="text-muted ">...</small>
                                        @else
                                          <small class="text-muted ">{{ substr($message->branch_read, 0, -3) }}</small>
                                        @endif
                                      </span>
                                      @endif

                                  </p>

                                  <p >
                                    {{ nl2br($message->content) }}
                                  </p>

                                  <div class="footer">
                                    
                                      <small class="text-muted">
                                        {{ $message->user->name }}
                                      </small>
                                    
                                  </div>
                                </div>
                              </li>
                            @endif
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  </li>
                  <?php $lp++;?>
                @endif
              @endforeach
              </ul>
          </div>
        </div>

      </div>
      <div class="tab-pane fade in " id="history">
        
        <?php foreach ($history as $k => $v) {?>
          <p class="clearfix ">
            <strong>{{substr($v->created_at,0,-3)}} - {{$v->user->name}}:</strong>
            {{ $v->history_type->content}}
            <em>
            @if($v->value == '-1')
              {{$v->injury_history_content->content}}              
            @else
              {{ strip_tags($v->value, '<i><em>')}}
            @endif
            </em>
            <hr class="short" />
          </p>
        <?php }?>
      </div>

  </div>

<!-- normal modal -->
<div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<!-- small modal -->
<div class="modal fade bs-example-modal-sm" id="modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      
    </div>
  </div>
</div>
<a href="" target="_blank" id="blank_a"></a>
@stop

@section('headerJs')
  @parent
    <script type="text/javascript">
        
        function readCommunicator(){
          $.ajax({
            url: "<?php echo  URL::route('routes.post', ['chat', 'checkConversation']);?>",
            data: {
              injury_id: "<?php echo $injury->id;?>",
              _token: $('input[name="_token"]').val()
            },
            dataType: "json",
            type: "POST"
          });
        }


        $(document).ready(function() {
          lat = "<?php echo $injury->lat; ?>";
          lng = "<?php echo $injury->lng; ?>";

          $("form").validate();

          var hash = window.location.hash;
          $('#info_tabs a[href="' + hash + '"]').tab('show');

          readCommunicator();

          if(hash == '#communicator') readCommunicator();   
          else if(hash == '#localization'){
            setTimeout(function(){
              initialize(lat, lng);
            }, 300);
          }

          $('.nav-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
            window.location.replace(e.target.hash);
            if(e.target.hash == '#communicator') readCommunicator();
            else if(e.target.hash == '#localization'){
              setTimeout(function(){
                initialize(lat, lng);
              }, 300);
            }
            
          });


          $('.modal-open-sm').on('click', function(){
            hrf=$(this).attr('target');         
            $.get( hrf, function( data ) {
              $('#modal-sm .modal-content').html(data);
            });
          });
          $('.modal-open').on('click', function(){
            hrf=$(this).attr('target');         
            $.get( hrf, function( data ) {
              $('#modal .modal-content').html(data);
            });
          });

          

          

          $("form").submit(function(e) {
               var self = this;
               e.preventDefault();

               if($("form").valid()){
                self.submit();
               }           
               return false; //is superfluous, but I put it here as a fallback
          });

          $('#modal').on('click', '#set-injury', function(){
            $(".btn-group").find(".btn.active input").attr('checked', 'checked');
            $.post(
                $('#dialog-injury-form').prop( 'action' ),
                $('#dialog-injury-form').serialize(),
                function( data ) {
                    if(data == '0') location.reload();
                    else{
                      $('#modal .modal-body').html( data);
                      $('#set-injury').attr('disabled',"disabled");
                    }
                },
                'json'
                );
            return false;
         }); 

          $('#modal').on('keypress', function(e){
              if(e.which == 13){  //Enter is key 13
                  
              }
          });

        });
        
    </script>

@stop      