@if($injury->if_theft == 1)
    <i class="fa fa-chain-broken tips sm-ico red" title="kradzież pojazdu"></i>
@endif

@if($injury->type_incident_id == 13)
    <i class="fa fa-tag tips sm-ico red" title="przywłaszczenie"></i>
@endif

@if ($injury->if_courtesy_car == 1)
    <span class="ico ico_car tips sm-ico" title="potrzebne auto zastępcze"></span>
@endif

@if ($injury->if_towing == 1)
    <span class="ico ico_holowanie tips sm-ico" title="potrzebne holowanie"></span>
@endif

@if($injury->vehicle->cfm == 1)
    <i class="fa fa-credit-card sm-ico blue tips" title="CFM"></i>
@endif

@if($injury->branch_id > 0 && $injury->branch->company->groups->count() > 0)
    <i class="fa fa-wrench blue tips sm-ico" data-html="true"
       title="<i>Serwis w grupie: {{ implode(',', $injury->branch->company->groups->lists('name')) }}</i><br>{{str_replace('"', "'", $injury->branch->short_name)}}<br>{{$injury->branch->code}} {{$injury->branch->city}}, {{$injury->branch->street}}"></i>
@elseif($injury->branch_id > 0)
    <i class="fa fa-wrench red tips sm-ico" data-html="true"
       title="<i>Serwis poza grupą</i>:<br>{{str_replace('"', "'", $injury->branch->short_name)}}<br>{{$injury->branch->code}} {{$injury->branch->city}}, {{$injury->branch->street}}"></i>
@endif

@foreach($injury->chat as $chat)
    @if($chat->active == 0)
        <i class="fa fa-comments-o blue sm-ico"></i>
        <?php break;?>
    @endif
@endforeach

@if($injury->skip_in_ending_report == 1)
    <i class="fa fa-check-square-o blue"></i>
@endif

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
@if($injury->user->typ() == 3)
    <i class="fa fa-info blue  sm-ico"></i>
@endif

@if($injury->way_of == 3)
    <i class="fa fa-mobile blue sm-ico"></i>
@elseif($injury->way_of == 4)
    <i class="fa fa-laptop blue sm-ico"></i>
@endif

@if($injury->if_vip == 1)
    <i class="fa fa-star text-warning tips" title="klient VIP"></i>
@endif
