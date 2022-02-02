<div class="tab-pane fade in active" id="communicator">
    <div class="panel">
        <div class="panel-body">
            <div class="clearfix">
                <h3 class="media-heading">
                    <div class="pull-right">
                        @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                            <span class="btn btn-warning btn-sm modal-open" target="{{ URL::to('insurances/communicator/create', [$agreement->id]) }}" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-comment-o"></i> Dodaj temat/zadanie
                            </span>
                        @endif
                    </div>
                </h3>
            </div>
            <ul class="timeline">
                <?php $lp = 0;?>
                @foreach($agreement->conversations as $k => $conversation)
                    <?php $status = get_receivers($conversation->status); ?>
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
                                        @if($conversation->active == 0 && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                            <span class="btn btn-primary btn-xs modal-open " target="{{ URL::to('insurances/communicator/close', [$conversation->id]) }}" data-toggle="modal" data-target="#modal">
                                                <i class="fa fa-times-circle-o"></i> Zamknij rozmowę
                                            </span>
                                            @if($conversation->deadline == '')
                                                <span class="btn btn-warning btn-xs modal-open " target="{{ URL::to('insurances/communicator/deadline', [$conversation->id]) }}" data-toggle="modal" data-target="#modal">
                                                    <i class="fa fa-clock-o"></i> Ustaw termin
                                                </span>
                                            @endif
                                            <span class="btn btn-warning btn-xs create-chat modal-open" target="{{ URL::to('insurances/communicator/replay', [$conversation->id]) }}" data-toggle="modal" data-target="#modal">
                                                <i class="fa fa-reply"></i> Odpowiedz
                                            </span>
                                        @endif
                                    </div>
                                    <div class="pull-left">
                                        {{ $conversation->topic }}
                                    </div>

                                    @if($conversation->deadline != '' )
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
                                        @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                            <div class="pull-left deadline-options">
                                                <i class="fa fa-check-square-o modal-open tips" target="{{ URL::to('insurances/communicator/accept', [$conversation->id]) }}" data-toggle="modal" data-target="#modal" title="Potwierdź wykonanie."></i>
                                            </div>
                                            <div class="pull-left deadline-options">
                                                <i class="fa fa-trash-o modal-open tips" target="{{ URL::to('insurances/communicator/remove-deadline', [$conversation->id]) }}" data-toggle="modal" data-target="#modal" title="Usuń zaplanowany termin wykonania."></i>
                                            </div>
                                        @endif
                                    @endif
                                </h4>
                            </div>
                            <div class="timeline-body">
                                <ul class="chat">
                                    @foreach($conversation->messages as $k2 => $message)
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
                                                @if($message->active == 5)
                                                    <s>
                                                        @endif
                                                        <p class="pull-right timeline-timer">
                                                            <small class="text-muted" style="padding-top: 2px;">
                                                                <i class="fa fa-clock-o fa-fw"></i> {{ substr($message->created_at, 0, -3) }}
                                                            </small>
                                                            @if( $message->active == 0 && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                                                <span class="btn btn-danger btn-xs modal-open " target="{{ URL::to('insurances/communicator/delete-message', [$message->id]) }}" data-toggle="modal" data-target="#modal">
                                                                  <i class="fa fa-trash-o fa-trash"></i> Usuń wpis
                                                                </span>
                                                            @endif
                                                        </p>
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
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php $lp++;?>
                @endforeach
            </ul>
        </div>
    </div>
</div>
