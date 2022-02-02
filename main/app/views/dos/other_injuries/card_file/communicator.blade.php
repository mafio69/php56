<div class="tab-pane fade in active" id="communicator">
    <div class="row">
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
                        @if(Auth::user()->can('zlecenia#zarzadzaj'))
                            <span class="btn btn-primary btn-sm modal-open"
                                  target="{{ URL::route('dos.other.injuries.get', ['getAssignLeader', $injury->id]) }}"
                                  data-toggle="modal" data-target="#modal">
                                    <i class="fa fa-male fa-fw"></i> zmień prowadzącego
                                </span>
                            <span class="btn btn-danger btn-sm modal-open"
                                  target="{{ URL::route('dos.other.injuries.get', ['getRemoveLeader', $injury->id]) }}"
                                  data-toggle="modal" data-target="#modal"
                            >
                                <i class="fa fa-remove fa-fw"></i> usuń prowadzącego
                            </span>
                        @endif
                    @else
                        @if(Auth::user()->can('zlecenia#zarzadzaj'))
                            <span class="btn btn-primary btn-sm btn-block modal-open"
                                  target="{{ URL::route('dos.other.injuries.get', ['getAssignLeader', $injury->id]) }}"
                                  data-toggle="modal" data-target="#modal">
                                <i class="fa fa-male fa-fw"></i> przypisz prowadzącego
                            </span>
                            <span class="btn btn-primary btn-sm btn-block modal-open"
                                  target="{{ URL::route('dos.other.injuries.get', ['getMarkAsLeader', $injury->id]) }}"
                                  data-toggle="modal" data-target="#modal">
                                <i class="fa fa-male fa-fw"></i> weź sprawę
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
        <div class="panel">
          <div class="panel-body">
            <div class="clearfix">
              <h3 class="media-heading"><small>
                <span class="label label-primary">DOS</span>
                <span class="label label-success">Infolinia</span>
                <span class="label label-info">Warsztat</span>
                </small>
                @if(Auth::user()->can('zlecenia#zarzadzaj'))
                    <div class="pull-right">
                      <span class="btn btn-warning btn-sm create-chat modal-open" target="{{ URL::route('dos.other.chat.create', array($injury->id)) }}" data-toggle="modal" data-target="#modal">
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
                          <h4 class="timeline-title">
                              @if(Auth::user()->can('zlecenia#zarzadzaj'))
                                    <div class="pull-right">

                                      @if(get_chat_group() == 1 && $conversation->active == 0)
                                        <span class="btn btn-primary btn-xs modal-open " target="{{ URL::route('dos.other.chat.close', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                          <i class="fa fa-times-circle-o"></i> Zamknij rozmowę
                                        </span>
                                        @if($conversation->deadline == '')
                                        <span class="btn btn-warning btn-xs modal-open " target="{{ URL::route('dos.other.chat.deadline', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                          <i class="fa fa-clock-o"></i> Ustaw termin
                                        </span>
                                        @endif
                                      @endif
                                      @if($conversation->active == 0)
                                      <span class="btn btn-warning btn-xs create-chat modal-open" target="{{ URL::route('dos.other.chat.replay', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-reply"></i> Odpowiedz
                                      </span>
                                      @endif
                                    </div>
                            @endif

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
                              @if(Auth::user()->can('zlecenia#zarzadzaj'))
                                  <div class="pull-left deadline-options">
                                    <i class="fa fa-check-square-o modal-open tips" target="{{ URL::route('dos.other.chat.accept', array($conversation->id)) }}" data-toggle="modal" data-target="#modal" title="Potwierdź wykonanie."></i>
                                  </div>
                                  <div class="pull-left deadline-options">
                                      <i class="fa fa-trash-o modal-open tips" target="{{ URL::route('dos.other.chat.removeDeadline', array($conversation->id)) }}" data-toggle="modal" data-target="#modal" title="Usuń zaplanowany termin wykonania."></i>
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
