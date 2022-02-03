@if(in_array($injury->step, [11, 14, 21, 22]) )
    @if($injury->is_cas_case == 1)
        <span class="label label-success">CAS</span>
    @else
        <span class="label label-info">CAS</span>
    @endif
@endif

@if ($injury->if_courtesy_car == 1)
    <span class="ico ico_car tips" title="potrzebne auto zastępcze"></span>
@endif

@if ($injury->if_towing == 1)
    <span class="ico ico_holowanie tips" title="potrzebne holowanie"></span>
@endif

@if($injury->if_theft == 1)
    <i class="fa fa-chain-broken tips sm-ico red" title="kradzież pojazdu"></i>
@endif

@if($injury->type_incident_id == 13)
    <i class="fa fa-tag tips sm-ico red" title="przywłaszczenie"></i>
@endif

@if($injury->vehicle->cfm == 1)
    <i class="fa fa-credit-card sm-ico blue tips" title="CFM"></i>
@endif

@foreach($injury->chat as $chat)
    @if($chat->active == 0)
        <i class="fa fa-comments-o blue font-large"></i>
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
                font-large"></i>
    @endif
@endforeach

@foreach($injury->chat as $chat)

    @foreach($chat->messages as $message)
        <?php $status = get_receivers($message->status); ?>
        @if($status[0] == 1 && $message->dos_read  == '' && get_chat_group() == 1)
            <i class="fa fa-envelope-o red font-large"></i>
            <?php break 2;?>
        @elseif($status[1] == 1 && $message->branch_read  == '' && get_chat_group() == 2)
            <i class="fa fa-envelope-o red font-large"></i>
            <?php break 2;?>
        @elseif($status[2] == 1 && $message->info_read  == '' && get_chat_group() == 3)
            <i class="fa fa-envelope-o red font-large"></i>
            <?php break 2;?>
        @endif
    @endforeach
@endforeach

@if($injury->user->typ() == 3)
    <i class="fa fa-info blue font-large"></i>
@endif

@if($injury->branch_id > 0 && $injury->branch && $injury->branch->company && $injury->branch->company->groups->count() > 0)
    <i class="fa fa-wrench blue tips sm-ico" data-html="true"
       title="<i>Serwis w grupie: {{ implode(',', $injury->branch->company->groups->lists('name')) }}</i><br>{{str_replace('"', "'", $injury->branch->short_name)}}<br>{{$injury->branch->code}} {{$injury->branch->city}}, {{$injury->branch->street}}"></i>
@elseif($injury->branch_id > 0 && $injury->branch)
    <i class="fa fa-wrench red tips sm-ico" data-html="true"
       title="<i>Serwis poza grupą</i>:<br>{{str_replace('"', "'", $injury->branch->short_name)}}<br>{{$injury->branch->code}} {{$injury->branch->city}}, {{$injury->branch->street}}"></i>
@else
    <i class="fa fa-ban tips sm-ico" title="procedowane bez serwisu"></i>
@endif

@if($injury->if_vip == 1)
    <i class="fa fa-star text-warning tips" title="klient VIP"></i>
@endif
