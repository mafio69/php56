@extends('layouts.main')


@section('header')
    <span class="pull-left">
Zlecenia (szkody) - zakończone kradzież
</span>
    @include('dos.other_injuries.partials.menu-top')

@stop

@include('dos.other_injuries.partials.nav')

@section('main')

    @include('dos.other_injuries.partials.menu')

    <div id="injuries-container">

        <table class="table  table-hover  table-condensed" id="users-table">
            <thead>
            <Th style="width:30px;">lp.</th>
            <th></th>
            <th>nr sprawy</th>
            <th>rodzaj zdarzenia</th>
            <th>obiekt sprawy</th>
            <th>kategoria</th>
            <th >nr umowy</th>
            <th>właściciel</th>
            <th >typ szkody</th>
            <th>nr szkody</th>
            <th >data zdarzenia</th>
            <th >miejsce zdarzenia</th>
            <Th>zgłaszający</Th>
            <th>prowadzący</th>
            <Th>data zgłoszenia</th>
            <th></th>
            </thead>

            <?php $lp = (($injuries->getCurrentPage()-1)*$injuries->getPerPage()) + 1;?>
            @foreach ($injuries as $k => $injury)
            <tr class="odd gradeX"
            @if(Session::has('last_injury') && $injury->id == Session::get('last_injury'))
                style="background-color: honeydew;"
                <?php Session::forget('last_injury');?>
                    @endif
                    >
                <td>{{$lp++}}.</td>
                <Td>
                    @if($injury->branch_id > 0 && $injury->branch->company->type == 1)
                        <i class="fa fa-wrench blue tips sm-ico" data-html="true" title="<i>Serwis w sieci IL</i>:<br>{{str_replace('"', "'", $injury->branch->short_name)}}<br>{{$injury->branch->code}} {{$injury->branch->city}}, {{$injury->branch->street}}"></i>
                    @elseif($injury->branch_id > 0)
                        <i class="fa fa-wrench red tips sm-ico" data-html="true" title="<i>Serwis zewnętrzny</i>:<br>{{str_replace('"', "'", $injury->branch->short_name)}}<br>{{$injury->branch->code}} {{$injury->branch->city}}, {{$injury->branch->street}}"></i>
                    @endif

                    @foreach($injury->chat as $chat)
                        @if($chat->active == 0)
                            <i class="fa fa-comments-o blue sm-ico"></i>
                            <?php break;?>
                        @endif
                    @endforeach

                    @foreach($injury->chat as $chat)
                        @if($chat->deadline != '')
                            <i class="fa fa-clock-o
                                    @if($chat->deadline == date('Y-m-d'))
                                        green
                                    @elseif(strtotime($chat->deadline) < time())
                                        red
                                    @else
                                    blue
                                @endif
                                    sm-ico"></i>
                        @endif
                    @endforeach

                    @foreach($injury->chat as $chat)
                        @foreach($chat->messages as $message)
                            <?php $status = get_receivers($message->status); ?>
                            @if($status[0] == 1 && $message->dos_read  == '' && get_chat_group() == 1)
                                <i class="fa fa-envelope-o red sm-ico"></i>
                                <?php break 2;?>
                            @elseif($status[1] == 1 && $message->branch_read  == '' && get_chat_group() == 2)
                                <i class="fa fa-envelope-o red sm-ico"></i>
                                <?php break 2;?>
                            @elseif($status[2] == 1 && $message->info_read  == '' && get_chat_group() == 3)
                                <i class="fa fa-envelope-o red sm-ico"></i>
                                <?php break 2;?>
                            @endif
                        @endforeach
                    @endforeach
                    @if($injury->user && $injury->user->typ() == 3)
                        <i class="fa fa-info blue  sm-ico"></i>
                    @endif

                        @if($injury->way_of == 3)
                            <i class="fa fa-mobile blue sm-ico"></i>
                        @elseif($injury->way_of == 4)
                            <i class="fa fa-laptop blue sm-ico"></i>
                        @endif
                </td>
                <Td><a type="button" class="btn btn-link" href="{{ URL::route('dos.other.injuries.info', array($injury->id)) }}" >{{$injury->case_nr}}</a></td>
                <td>{{ $injury->type_incident ? $injury->type_incident->name : '' }}</td>
                <td>{{$injury->object ? $injury->object->description : ''}}</td>
                <td>{{ checkObjectIfNotNull($injury->object->assetType, 'name') }}</td>
                <Td>
                    <a type="button" class="btn btn-link" href="{{ URL::route('dos.other.injuries.info', array($injury->id)) }}" >{{$injury->object->nr_contract}}</a>
                </td>
                <td><span class="tips" title="{{ $injury->object->owner->name }}">{{ $injury->object->owner->short_name }}</span></td>
                <td>{{ $injury->injuries_type ? $injury->injuries_type->name : ''}}</td>
                <td>
                    @if($injury->injury_nr != NULL && $injury->injury_nr != '')
                        {{$injury->injury_nr}}
                    @else
                        ---
                    @endif
                </td>
                <td>{{$injury->date_event}}</td>
                <Td>{{$injury->event_city}}</td>
                <td>
                    {{ $injury->user->name }}
                </td>
                <td>
                    @if($injury->leader)
                        {{ $injury->leader->name }}
                    @endif
                </td>
                <td>{{substr($injury->created_at, 0, -3)}}</td>
                @include('dos.other_injuries.actions.theft-finished_options')
            </tr>
            @endforeach
        </table>
        @include('injuries.legend')
        <div class="pull-right" style="clear:both;">{{ $injuries->links() }}</div>

    </div>



@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modal-sm').on('click', '#set-injury', function(){
                btn = $(this);
                btn.attr('disabled', 'disabled');
                $.ajax({
                    type: "POST",
                    url: $('#dialog-injury-form').prop( 'action' ),
                    data: $('#dialog-injury-form').serialize(),
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        if(data.code == '0') location.reload();
                        else if(data.code == '1') self.location = data.url;
                        else{
                            $('#modal-sm .modal-body').html( data.error);
                            $('#set-injury').attr('disabled',"disabled");
                        }
                    },
                    dataType: 'json'
                });
                return false;
            });



        });

    </script>

@stop

