@if(Auth::user()->can('kartoteka_szkody#komunikator'))
    <div class="tab-pane fade in active" id="communicator">
        <div class="row">
            @if(in_array($injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,47]))
                <div class=" col-sm-2">
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Status
                        </div>
                        <div class="panel-body text-center">
                            <span class="label label-border-info status-btn tips"
                                  style="width:100%; padding:5px; font-size:12px;"
                                  title="{{ $injury->status->name }}">{{ $injury->status->name }}</span>
                        </div>
                    </div>
                </div>
                <div class=" col-sm-2">
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Etap
                        </div>
                        <div class="panel-body text-center">
                            <span class="label label-border-info status-btn tips"
                                  style="width:90%; padding:5px; font-size:12px;"
                                  @if($injury->totalStatus && in_array($injury->step, [30,31,32,33,34,35,36,37]))
                                  title="{{ $injury->totalStatus->name }}">{{ ucfirst($injury->totalStatus->name) }}
                                @elseif($injury->theftStatus && in_array($injury->step, [40,41,42,43,44,45,46,47]))
                                    title="{{ $injury->theftStatus->name }}">{{ ucfirst($injury->theftStatus->name) }}
                                @endif

                            </span>
                            @if(Auth::user()->can('kartoteka_szkody#komunikator#zarzadzanie_etapem'))
                            <i class="fa fa-pencil-square-o pull-right tips modal-open"
                               style="font-size:15px; margin-top:10px; cursor: pointer;"
                               target="{{ URL::route('injuries-getChangeInjuryStep', array($injury->id)) }}"
                               data-toggle="modal" data-target="#modal" title="edytuj"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class=" col-sm-4">
                <div class="panel panel-default small">
                    <div class="panel-heading">
                        Prowadzący sprawę
                    </div>
                    <div class="panel-body text-center">
                        @if($injury->leader)
                            <p>
                                <strong>{{ $injury->leader->name }} -
                                    przypisany {{ substr($injury->leader_assign_date, 0, -3) }}</strong>
                            </p>
                            @if(Auth::user()->can('kartoteka_szkody#komunikator#przypisz_prowadzacego'))
                                <span class="btn btn-primary btn-sm modal-open"
                                      target="{{ URL::to('injuries/manage/assign-leader', array($injury->id)) }}"
                                      data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-male fa-fw"></i> zmień prowadzącego
                                </span>
                            @endif
                            @if(Auth::user()->can('kartoteka_szkody#komunikator#usun_prowadzacego'))
                                <span class="btn btn-danger btn-sm modal-open"
                                      target="{{ url('injuries/manage/remove-leader', [$injury->id]) }}"
                                      data-toggle="modal" data-target="#modal"
                                >
                                    <i class="fa fa-remove fa-fw"></i> usuń prowadzącego
                                </span>
                            @endif
                        @else
                            @if(Auth::user()->can('kartoteka_szkody#komunikator#przypisz_prowadzacego'))
                                <span class="btn btn-primary btn-sm btn-block modal-open"
                                      target="{{ URL::to('injuries/manage/assign-leader', array($injury->id)) }}"
                                      data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-male fa-fw"></i> przypisz prowadzącego
                                </span>
                            @endif

                            @if(Auth::user()->can('kartoteka_szkody#komunikator#wez_sprawe'))
                                <span class="btn btn-primary btn-sm btn-block modal-open"
                                      target="{{ URL::to('injuries/manage/mark-as-leader', array($injury->id)) }}"
                                      data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-male fa-fw"></i> weź sprawę
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @if(in_array($injury->step, ['0', '10', '13', '15', '16', '17', '18', '19', '20']) && ( $injury->getDocument(3,49)->count() > 0 ||  $injury->getDocument(3,52)->count() || $injury->getDocument(3,60)->count() ))
                <div class="col-sm-4">
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Prowadzący rozliczenie szkody
                        </div>
                        <div class="panel-body text-center">
                            @if($injury->settlementsLeader)
                                <p>
                                    <strong>{{ $injury->settlementsLeader->name }} -
                                        przypisany {{ substr($injury->settlements_leader_assign_date, 0, -3) }}</strong>
                                </p>
                                @if(Auth::user()->can('kartoteka_szkody#komunikator#przypisz_prowadzacego'))
                                    <span class="btn btn-success btn-sm btn-block modal-open"
                                          target="{{ URL::to('injuries/manage/assign-settlements-leader', array($injury->id)) }}"
                                          data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-male fa-fw"></i> zmień prowadzącego
                                    </span>
                                @endif
                            @else
                                @if(Auth::user()->can('kartoteka_szkody#komunikator#przypisz_prowadzacego'))
                                    <span class="btn btn-success btn-sm btn-block modal-open"
                                          target="{{ URL::to('injuries/manage/assign-settlements-leader', array($injury->id)) }}"
                                          data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-male fa-fw"></i> przypisz prowadzącego
                                    </span>
                                @endif
                                <span class="btn btn-success btn-sm btn-block modal-open"
                                      target="{{ URL::to('injuries/manage/mark-as-settlements-leader', array($injury->id)) }}"
                                      data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-male fa-fw"></i> weź sprawę
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if(in_array($injury->injuries_type_id,[1,2,3,4,7]))
                <div class="col-sm-2">
                    <div class="panel panel-default small">
                        <div class="panel-heading">
                            Polisa GAP
                        </div>
                        <div class="panel-body text-center">
                                <span class="label @if($injury->injuryPolicy->gap==0) label-default @else label-primary @endif label-full">
                              {{ Config::get('definition.insurance_options_definition.'.$injury->injuryPolicy->gap) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <div class="panel">
            <div class="panel-body">
                <div class="clearfix">
                    <h3 class="media-heading">
                        <small>
                            <span class="label label-primary">DOS</span>
                            <span class="label label-success">Infolinia</span>
                            <span class="label label-info">Warsztat</span>
                        </small>
                        @if(Auth::user()->can('kartoteka_szkody#komunikator#dodaj_wpis'))
                            <div class="pull-right">
                                    <span class="btn btn-warning btn-sm create-chat modal-open"
                                          target="{{ URL::route('chat.create', array($injury->id)) }}" data-toggle="modal"
                                          data-target="#modal">
                                <i class="fa fa-comment-o"></i> Dodaj temat/zadanie
                              </span>
                            </div>
                        @endif
                    </h3>


                </div>
                <ul class="timeline">
                    <?php $lp = 0;?>
                    @foreach($chat as $k => $conversation)
                        <?php $status = get_receivers($conversation->status); ?>
                        @if(isset($status[get_chat_group()-1]) && $status[get_chat_group()-1] == 1)
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
                                        <h4 class="timeline-title">
                                            <div class="pull-right">
                                                @if(Auth::user()->can('kartoteka_szkody#komunikator#zarzadzaj_wpisem'))
                                                    @if(get_chat_group() == 1 && $conversation->active == 0)
                                                        <span class="btn btn-primary btn-xs modal-open "
                                                              target="{{ URL::route('chat.close', array($conversation->id)) }}"
                                                              data-toggle="modal" data-target="#modal">
                                                              <i class="fa fa-times-circle-o"></i> Zamknij rozmowę
                                                        </span>
                                                        @if($conversation->deadline == '')
                                                            <span class="btn btn-warning btn-xs modal-open "
                                                                      target="{{ URL::route('chat.deadline', array($conversation->id)) }}"
                                                                      data-toggle="modal" data-target="#modal">
                                                              <i class="fa fa-clock-o"></i> Ustaw termin
                                                            </span>
                                                        @endif
                                                    @endif
                                                    @if($conversation->active == 0)
                                                        <span class="btn btn-warning btn-xs create-chat modal-open"
                                                              target="{{ URL::route('chat.replay', array($conversation->id)) }}"
                                                              data-toggle="modal" data-target="#modal">
                                                                <i class="fa fa-reply"></i> Odpowiedz
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <div class="pull-left">
                                                {{ $conversation->topic }}
                                            </div>

                                            @if($conversation->deadline != '')
                                                <div class="pull-left deadline
                                                      @if($conversation->deadline == date('Y-m-d'))
                                                        green
                                                      @elseif(strtotime($conversation->deadline) < time())
                                                        red
                                                      @else
                                                        blue
                                                      @endif
                                                        ">
                                                    <i class="fa fa-clock-o"></i> {{$conversation->deadline}}
                                                </div>
                                                @if(Auth::user()->can('kartoteka_szkody#komunikator#zarzadzaj_wpisem'))
                                                    <div class="pull-left deadline-options">
                                                        <i class="fa fa-check-square-o modal-open tips"
                                                           target="{{ URL::route('chat.accept', array($conversation->id)) }}"
                                                           data-toggle="modal" data-target="#modal"
                                                           title="Potwierdź wykonanie."></i>
                                                    </div>
                                                    <div class="pull-left deadline-options">
                                                        <i class="fa fa-trash-o modal-open tips"
                                                           target="{{ URL::route('chat.removeDeadline', array($conversation->id)) }}"
                                                           data-toggle="modal" data-target="#modal"
                                                           title="Usuń zaplanowany termin wykonania."></i>
                                                    </div>
                                                @endif
                                            @endif


                                        </h4>
                                    </div>
                                    <div class="timeline-body">
                                        <ul class="chat">
                                            @foreach($conversation->messages as $k2 => $message)
                                                <?php $status = get_receivers($message->status); ?>
                                                @if($status[get_chat_group()-1] == 1)
                                                    <li class="left clearfix
                                  @if( ($status[0] == 1 && $message->dos_read  == '' && get_chat_group() == 1) || ($status[1] == 1 && $message->branch_read  == '' && get_chat_group() == 2) || ($status[2] == 1 && $message->info_read  == '' && get_chat_group() == 3))
                                                            lightBlue
                                                          @endif
                                                            ">
                                                        <div class="pull-left">
                                                            <div class="chat-message
                                        @if($message->user->typ() == 2 )
                                                                    message-info
                                                                  @elseif($message->user->typ() == 1 )
                                                                    message-primary
                                                                  @elseif($message->user->typ() == 3 )
                                                                    message-success
                                                                  @endif
                                                                    ">
                                                                <i class="fa fa-comment-o "></i>
                                                            </div>

                                                        </div>
                                                        <div class="chat-body clearfix">
                                                            @if($message->active == 5)
                                                                <s>
                                                                    @endif
                                                                    <div class="pull-right timeline-timer">
                                                                        <small class="text-muted"
                                                                               style="padding-top: 2px;">
                                                                            <i class="fa fa-clock-o fa-fw"></i> {{ substr($message->created_at, 0, -3) }}
                                                                        </small>

                                                                        @if($status[0] == 1 && $message->user->typ() != 1 && get_chat_group() != 1)
                                                                            <span class="time-container">
                                                                                <span class="label label-primary  legend-label">
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
                                                                        @if(  $message->active == 0 && Auth::user()->can('kartoteka_szkody#komunikator#usun_wpis'))
                                                                            <span class="btn btn-danger btn-xs modal-open "
                                                                                  target="{{ URL::route('chat.deleteMessage', array($message->id)) }}"
                                                                                  data-toggle="modal"
                                                                                  data-target="#modal">
                                                                              <i class="fa fa-trash-o fa-trash"></i> Usuń wpis
                                                                            </span>
                                                                        @endif
                                                                        @if(! $message->note && $injury->sap)
                                                                            <form action="{{  URL::route('chat.sendToSap', array($message->id)) }}"  method="post">
                                                                                {{ Form::token() }}
                                                                                <button class="btn btn-xs btn-info marg-top-min" type="submit"><i class="fa fa-fw fa-send-o"></i> przekaż do SAP  </button>
                                                                            </form>
                                                                        @elseif($message->note)
                                                                            <div class="label label-primary" data-toggle="popover" title="Nr notatki {{ $message->note->nrnotatki }}" data-content="<label>data wysłania:</label> {{ $message->note->created_at->format('Y-m-d H:i') }}<br/> <label>treść:</label> {{ $message->note->temat }}">
                                                                                przekazana do SAP
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <p>
                                                                        {{ nl2br($message->content) }}
                                                                    </p>
                                                                    <div class="footer">
                                                                        <small class="text-muted">
                                                                            {{ $message->user->name }}
                                                                        </small>
                                                                    </div>
                                                                    @if($message->active == 5)
                                                                </s>
                                                            @endif
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
    @section('headerJs')
        @parent
        <script type="text/javascript">
            $(document).ready(function () {
                readCommunicator();
            });
        </script>
    @stop
@endif
