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
                  <span class="btn btn-warning btn-sm create-chat modal-open" target="{{ URL::route('dos.other.chat.create', array($injury->id)) }}" data-toggle="modal" data-target="#modal">
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
                          <h4 class="timeline-title">
                            <div class="pull-right">
                              @if($conversation->active == 0)
                              <span class="btn btn-warning btn-xs create-chat modal-open" target="{{ URL::route('dos.other.chat.replay', array($conversation->id)) }}" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-reply"></i> Odpowiedz
                              </span>
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