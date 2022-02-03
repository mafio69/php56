@foreach($contracts as $contract)
    @foreach($contract->vehicles as $vehicle)
        <tr>
            <td>
                {{ ++ $lp  }}.
            </td>
            <td>
                <span class="label label-primary">
                    Syjon
                </span>
            </td>
            <td>
                <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/contract/card-file-external/info/'.$contract->id }}" class="btn btn-xs btn-info" off-disable>
                    <i class="fa fa-search"></i>
                </a>
                {{ $contract->contract_number }}
            </td>
            <td>
                <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/object/card/info/'.$vehicle->id }}" class="btn btn-xs btn-info" off-disable>
                    <i class="fa fa-search"></i>
                </a>
                {{ $vehicle->description }}
            </td>
            <td>
                {{ $vehicle->object_type }}
            </td>
            <td>
                <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/contractor/card-file/show/'.$contract->owner->contractor_id }}" class="btn btn-xs btn-info" off-disable>
                    <i class="fa fa-search"></i>
                </a>
                {{ $contract->owner->contractor_name }}
            </td>
            <td>
                {{ $contract->contract_status }}
            </td>
            <td>
                {{ $contract->contract_planned_ending_date }}
            </td>
            <td>
                @if(isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                    {{ $vehicle->contract_internal_agreements[0]->policies[0]->policy_insurance_company }}
                @endif
            </td>
            <td>
                <form action="{{ url('dos/other/injuries/make/create-new-entity') }}" method="post">
                    {{ Form::token() }}
                    {{ Form::hidden('contract_id', $contract->id) }}
                    {{ Form::hidden('object_id', $vehicle->id) }}
                    {{ Form::hidden('contract_internal_agreement_id', isset($vehicle->contract_internal_agreements[0]) ? $vehicle->contract_internal_agreements[0]->id : null) }}
                    @if(isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                        {{ Form::hidden('policy_id', $vehicle->contract_internal_agreements[0]->policies[0]->policy_id) }}
                    @endif
                    <button type="submit" class="btn btn-primary btn-xs">
                        PRZYJMIJ SZKODĘ
                    </button>
                </form>
            </td>
        </tr>
        <tr>
            <td colspan="11">
                <div class="row">
                   <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3 text-center">
                       @if(isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                       <div class="btn btn-xs btn-primary text-center" data-toggle="collapse" data-target="#collapsePolicies{{$vehicle->id}}" aria-expanded="false" aria-controls="collapsePolicies{{$vehicle->id}}">
                           <i class="fa fa-arrow-down fa-fw" aria-hidden="true"></i>
                           polisy do umowy <span class="badge">{{ count((array) $vehicle->contract_internal_agreements[0]->policies) }}</span>
                       </div>
                       @endif

                       <div class="btn btn-xs btn-warning text-center" data-toggle="collapse" data-target="#collapseInjuries{{$vehicle->id}}" aria-expanded="false" aria-controls="collapseInjuries{{$vehicle->id}}">
                           <i class="fa fa-arrow-down fa-fw" aria-hidden="true"></i>
                           szkody na przedmiocie <span class="badge">{{ count( isset($vehicle->injuries) ? $vehicle->injuries : [] ) }}</span>
                       </div>

                       <div class="btn btn-xs btn-warning text-center" data-toggle="collapse" data-target="#collapseUnprocessed{{$vehicle->id}}" aria-expanded="false" aria-controls="collapseUnprocessed{{$vehicle->id}}">
                           <i class="fa fa-arrow-down fa-fw" aria-hidden="true"></i>
                           szkody nieprzetworzone <span class="badge">{{ count( isset($vehicle->unprocessed) ? $vehicle->unprocessed : [] ) }}</span>
                       </div>
                   </div>
                    @if(isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                    <div class="col-sm-12 col-lg-8 col-lg-offset-2 collapse" id="collapsePolicies{{$vehicle->id}}">
                       <div class="panel panel-primary marg-top-min">
                           <table class="table table-condensed table-hover">
                               <thead class="bg-primary">
                                    <th>#</th>
                                   <th>WŁASNA/OBCA</th>
                                   <th>NR POLISY</th>
                                   <th>NAZWA TU</th>
                                   <th>OKRES WAŻNOŚCI</th>
                                   <th>ZAKRES</th>
                                   <th>SUMA UBEZPIECZENIA</th>
                               </thead>
                               @foreach($vehicle->contract_internal_agreements[0]->policies as $k => $policy)
                                   <tr>
                                       <td>{{ ++$k }}.</td>
                                       <td>
                                            {{ $policy->policy_type }}
                                       </td>
                                       <td>
                                           <a target="_blank" href="{{ Config::get('webconfig.SYJON_URL').'/policy/card-file/info/'.$policy->policy_id }}" class="btn btn-xs btn-info" off-disable>
                                               <i class="fa fa-search"></i>
                                           </a>
                                           {{ $policy->policy_number }}
                                       </td>
                                       <td>
                                           {{ $policy->policy_insurance_company }}
                                       </td>
                                       <td>
                                            {{ $policy->policy_date_from }} - {{ $policy->policy_date_to }}
                                       </td>
                                       <td>
                                            {{ $policy->policy_risks }}
                                       </td>
                                       <td>
                                           {{ $policy->policy_insurance_amount }}
                                       </td>
                                   </tr>
                               @endforeach
                           </table>
                       </div>
                   </div>
                    @endif

                    @if(isset($vehicle->injuries))
                    <div class="col-sm-12 collapse" id="collapseInjuries{{ $vehicle->id }}">
                        <div class="panel panel-warning marg-top-min">
                            <table class="table table-hover table-condensed">
                                <thead class="bg-warning">
                                <Th>lp</th>
                                <th></th>
                                <th>nr sprawy</th>
                                <th>obiekt sprawy</th>
                                <th>kategoria</th>
                                <th >nr umowy</th>
                                <th>właściciel</th>
                                <th >typ szkody</th>
                                <Th>nr szkody</th>
                                <th >data zdarzenia</th>
                                <th >miejsce zdarzenia</th>
                                <th>status</th>
                                <th></th>
                                </thead>
                                @foreach($vehicle->injuries as $k => $injury)
                                    <tr>
                                        <td>{{$k+1}}.</td>
                                        <td>

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

                                            @foreach($injury->chat as $chat)
                                                @if($chat->active == 0)
                                                    <i class="fa fa-comments-o blue font-large"></i>
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

                                            @if($injury->user && $injury->user->typ() == 3)
                                                <i class="fa fa-info blue font-large"></i>
                                            @endif
                                        </td>
                                        <Td>
                                            <a type="button" class="btn btn-link" href="{{ URL::route('dos.other.injuries.info', array($injury->id)) }}" >{{$injury->case_nr}}</a>
                                        </td>
                                        <td>{{$injury->object ? $injury->object->description : ''}}</td>
                                        <td>{{ checkObjectIfNotNull($injury->object->assetType, 'name') }}</td>
                                        <Td>
                                            <a type="button" class="btn btn-link" href="{{ URL::route('dos.other.injuries.info', array($injury->id)) }}" >{{$injury->object->nr_contract}}</a>
                                        </td>
                                        <td><span class="tips" title="{{ $injury->object->owner->name }}">{{ $injury->object->owner->short_name }}</span></td>
                                        <td>{{$injury->injuries_type ? $injury->injuries_type->name : ''}}</td>
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
                                            <b>
                                                {{ $injury->status->name }}
                                            </b>
                                        </td>

                                        @include('dos.other_injuries.actions.'.Config::get('definition.dosInjuriesStepOptionsIncludes.'.$injury->step).'_options')
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    @endif

                    @if(isset($vehicle->unprocessed))
                        <div class="col-sm-12 collapse" id="collapseUnprocessed{{ $vehicle->id }}">
                            <div class="panel panel-warning marg-top-min">
                                <table class="table  table-hover  table-condensed">
                                    <thead class="bg-warning">
                                    <Th style="width:30px;">lp.</th>
                                    <th style="min-width:20px;"></th>
                                    <th >nr umowy</th>
                                    <th >data zdarzenia</th>
                                    <th >miejsce zdarzenia</th>
                                    <th>typ szkody</th>
                                    <th>zgłaszający</th>
                                    <Th>data zgłoszenia</th>
                                    <th>przesłane zdjęcia</th>
                                    <th ></th>
                                    </thead>
                                    @foreach ($vehicle->unprocessed as $k => $injury)
                                        <tr class="odd gradeX"
                                            @if(Session::has('last_injury') && $injury->id == Session::get('last_injury'))
                                            style="background-color: honeydew;"
                                        <?php Session::forget('last_injury');?>
                                                @endif
                                        >
                                            <td>{{$lp++}}.</td>
                                            <td>
                                                @if($injury->source == 1)
                                                    <i class="fa fa-laptop "></i>
                                                @elseif($injury->source == 0)
                                                    <i class="fa fa-mobile font-large"></i>
                                                @else
                                                    <i class="fa fa-file-excel-o "></i>
                                                @endif
                                            </td>
                                            <td>{{ checkIfEmpty($injury->nr_contract) }}</td>
                                            <td>{{ checkIfEmpty($injury->date_event) }}</td>
                                            <Td>{{ checkIfEmpty($injury->event_city) }}</td>
                                            <td>
                                                @if( ($injury->source == 0 || $injury->source == 3)  && $injury->injuries_type()->first())
                                                    {{ $injury->injuries_type()->first()->name }}
                                                @else
                                                    @if($injury->injuries_type == 2)
                                                        komunikacyjna OC
                                                    @elseif($injury->injuries_type == 1)
                                                        komunikacyjna AC
                                                    @elseif($injury->injuries_type == 3)
                                                        komunikacyjna kradzież
                                                    @elseif($injury->injuries_type == 4)
                                                        majątkowa
                                                    @elseif($injury->injuries_type == 5)
                                                        majątkowa kradzież
                                                    @elseif($injury->injuries_type == 6)
                                                        komunikacyjna AC - regres
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $injury->notifier_email }}
                                                <br>
                                                {{ $injury->notifier_surname }} {{ $injury->notifier_name }} {{ $injury->notifier_phone }}
                                            </td>
                                            <td>{{substr($injury->created_at, 0, -3)}}</td>
                                            <td>
                                                @if($injury->files->count() > 0)
                                                    <a href="#" target="{{ URL::route('injuries-getUploadesPictures', array($injury->id)) }}" class="modal-open btn btn-success btn-sm" data-toggle="modal" data-target="#modal"><i class="fa fa-search"></i> pokaż</a>
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ URL::route('injuries.unprocessed.print', array($injury->id)) }}" target="_blank" class="btn btn-primary btn-sm tips" title="drukuj zgłoszenie"><i class="fa fa-print"></i></a>
                                            </td>
                                            @include('dos.other_injuries.actions.unprocessed_options')
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@endforeach

