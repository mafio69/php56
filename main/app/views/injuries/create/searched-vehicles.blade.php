<table class="table table-condensed table-contracts">
    <thead>
        <th>#</th>
        <th>Źródło</th>
        <th>Nr rej.</th>
        <th>Nr umowy</th>
        <th>Marka</th>
        <th>Model</th>
        <th>Właściciel</th>
        <th>Status umowy</th>
        <th>Data ważności polisy</th>
        <th>Data ważności umowy</th>
        <th>Nazwa TU</th>
        <th></th>
    </thead>
    @foreach($vehicles as $k => $vehicle)
        <tr>
            <td>
                {{ ++ $k  }}.
            </td>
            <td>
                <span class="label label-info">
                    baza szkód
                </span>
            </td>
            <td>
                {{ $vehicle['registration'] }}
            </td>
            <td>
                {{ $vehicle['nr_contract'] }}
            </td>
            <td>
                {{ $vehicle['brand'] }}
            </td>
            <td>
                {{ $vehicle['model'] }}
            </td>
            <td>
                {{ $vehicle['owner'] }}
            </td>
            <td>
                @if(contractStatus($vehicle['contract_status']) == 0) )
                    <span class="label label-danger"> <i class="fa fa-exclamation-triangle fa-fw"></i>
                @endif
                    {{ $vehicle['contract_status'] }}
                @if(contractStatus($vehicle['contract_status']) == 0) )
                    </span>
                @endif
            </td>
            <td>
                {{ $vehicle['insurance_expire'] }}
            </td>
            <td>
                {{ $vehicle['expire'] }}
            </td>
            <td>
               {{ $vehicle['insurance_company_name'] }}
            </td>
            <td>
                <form action="{{ url('injuries/make/create-new-entity') }}" method="post">
                    {{ Form::token() }}
                    {{ Form::hidden('vehicle_id', $vehicle['id']) }}
                    {{ Form::hidden('as', 1) }}
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
                        <div class="btn btn-xs btn-warning text-center" data-toggle="collapse" data-target="#collapseInjuries{{$vehicle['id']}}" aria-expanded="false" aria-controls="collapseInjuries{{$vehicle['id']}}">
                            <i class="fa fa-arrow-down fa-fw" aria-hidden="true"></i>
                            szkody na przedmiocie <span class="badge">{{ count( isset($vehicle['injuries']) ? $vehicle['injuries'] : [] ) }}</span>
                        </div>

                        <div class="btn btn-xs btn-warning text-center" data-toggle="collapse" data-target="#collapseUnprocessed{{$vehicle['id']}}" aria-expanded="false" aria-controls="collapseUnprocessed{{$vehicle['id']}}">
                            <i class="fa fa-arrow-down fa-fw" aria-hidden="true"></i>
                            szkody nieprzetworzone <span class="badge">{{ count( isset($vehicle['unprocessed']) ? $vehicle['unprocessed'] : [] ) }}</span>
                        </div>

                        <div class="btn btn-xs btn-info text-center" data-toggle="collapse" data-target="#collapseLetters{{$vehicle['id']}}" aria-expanded="false" aria-controls="collapseLetters{{$vehicle['id']}}">
                            <i class="fa fa-arrow-down fa-fw" aria-hidden="true"></i>
                            pisma dopasowane do przedmiotu <span class="badge">{{ count(  isset($vehicle['letters']) ? $vehicle['letters'] : [] ) }}</span>
                        </div>
                    </div>

                    @if(isset($vehicle['injuries']))
                        <div class="col-sm-12 collapse" id="collapseInjuries{{ $vehicle['id'] }}">
                            <div class="panel panel-warning marg-top-min">
                                <table class="table table-hover table-condensed">
                                    <thead class="bg-warning">
                                    <Th style="width:30px;">lp</th>
                                    <th></th>
                                    <th>nr sprawy</th>
                                    <th>samochód</th>
                                    <th>nr umowy</th>
                                    <th>rejestracja</th>
                                    <Th>właściciel</Th>
                                    <Th>nr szkody</th>
                                    <th>data zgłoszenia</th>
                                    <th>data i miejsce zdarzenia</th>
                                    <th>status</th>
                                    <th>upoważnienie</th>
                                    <th>przyjmujący</th>
                                    <th>prowadzący</th>
                                    <th></th>
                                    </thead>
                                    @foreach($vehicle['injuries'] as $k => $injury)
                                        <tr>
                                            <td>{{$k+1}}.</td>
                                            <td>@include('injuries.includes.search-global_statuses')</td>
                                            <td>
                                                @if(Auth::user()->can('kartoteka_szkody#wejscie'))
                                                    <a type="button" class="btn btn-link" target="_blank" href="{{URL::route('injuries-info', array($injury->id))}}" >
                                                        {{$injury->case_nr}}
                                                    </a>
                                                @else
                                                    {{$injury->case_nr}}
                                                @endif
                                            </td>
                                            <td>{{ checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) }} {{ checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model)  }}</td>
                                            <td>
                                                @if($vehicle['nr_contract'] == $injury->vehicle->nr_contract)
                                                    <span class="label-info label">
                                                        <i class="fa fa-check"></i>
                                                    </span>
                                                @endif
                                                {{ $injury->vehicle->nr_contract }}
                                            </td>
                                            <Td>
                                                @if($vehicle['registration'] == $injury->vehicle->registration)
                                                    <span class="label-info label">
                                                        <i class="fa fa-check"></i>
                                                    </span>
                                                @endif

                                                @if(Auth::user()->can('kartoteka_szkody#wejscie'))
                                                    <a type="button" class="btn btn-link" href="{{ URL::route('injuries-info', array($injury->id)) }}" >{{$injury->vehicle->registration}}</a>
                                                @else
                                                    {{$injury->vehicle->registration}}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="tips" title="{{ $injury->vehicle->owner->name }}">{{ $injury->vehicle->owner->short_name }}</span>
                                            </td>
                                            <td @if($injury->dsp_notification) class="bg-danger tips" title="zgłoszenie DSP" data-container="body" @endif>
                                                @if($injury->injury_nr != NULL && $injury->injury_nr != '')
                                                    {{$injury->injury_nr}}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td>
                                                {{substr($injury->created_at, 0, -3)}}
                                            </td>
                                            <td>
                                                {{$injury->event_city.' '.$injury->event_street.'
                                                <br>
                                                '.$injury->date_event}}
                                            </td>
                                            <td>
                                                <b>{{ $injury->status->name }}</b>
                                            </td>
                                            <td>
                                                @if ($injury->task_authorization == 0)
                                                    <i class="fa fa-exclamation md-ico task" orygin="fa-exclamation" task="task_authorization" val="1" id_injury="{{$injury->id}}"></i>
                                                @else
                                                    <i class="fa fa-check md-ico task" orygin="fa-check" task="task_authorization" val="0" id_injury="{{$injury->id}}"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $injury->user->name }}
                                            </td>
                                            <td>
                                                @if($injury->leader)
                                                    {{ $injury->leader->name }}
                                                @endif
                                            </td>
                                            @if(View::exists('injuries.includes.'.Config::get('definition.injuriesStepOptionsIncludes.'.$injury->step).'_options'))
                                                @include('injuries.includes.'.Config::get('definition.injuriesStepOptionsIncludes.'.$injury->step).'_options')
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(isset($vehicle['unprocessed']))
                        <div class="col-sm-12 collapse" id="collapseUnprocessed{{ $vehicle['id'] }}">
                            <div class="panel panel-warning marg-top-min">
                                <table class="table  table-hover  table-condensed">
                                    <thead class="bg-warning">
                                    <Th style="width:30px;">lp.</th>
                                    <th></th>
                                    <th >rejestracja</th>
                                    <th >nr umowy</th>
                                    <th >data zdarzenia</th>
                                    <th >miejsce zdarzenia</th>
                                    <th>typ szkody</th>
                                    <th>zgłaszający</th>
                                    <Th>data zgłoszenia</th>
                                    <th>uszkodzenia</th>
                                    <th>przesłane zdjęcia</th>
                                    <Th></Th>
                                    <th ></th>
                                    </thead>

                                    @foreach ($vehicle['unprocessed'] as $k => $injury)
                                        <tr class="odd gradeX"
                                            @if(Session::has('last_injury') && $injury->id == Session::get('last_injury'))
                                            style="background-color: honeydew;"
                                        <?php Session::forget('last_injury');?>
                                                @endif
                                        >
                                            <td>{{ ++$k }}.</td>
                                            <td>
                                                @if($injury->source == 1)
                                                    <i class="fa fa-laptop "></i>
                                                @else
                                                    <i class="fa fa-mobile font-large"></i>
                                                @endif
                                            </td>
                                            <Td>{{ checkIfEmpty($injury->registration) }}</td>
                                            <td>{{ checkIfEmpty($injury->nr_contract) }}</td>
                                            <td>{{ checkIfEmpty($injury->date_event) }}</td>
                                            <Td>{{ checkIfEmpty($injury->event_city) }}</td>
                                            <td>
                                                @if($injury->source == 0 && $injury->injuries_type()->first())
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
                                                        komunikacyjna AC - Regres
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
                                                @if($injury->damages->count() > 0)
                                                    <a href="#" target="{{ URL::route('injuries-getDamages', array($injury->id)) }}" class="modal-open btn btn-success btn-sm" data-toggle="modal" data-target="#modal"><i class="fa fa-search"></i> pokaż</a>
                                                @else
                                                    ---
                                                @endif
                                            </td>
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
                                            @include('injuries.includes.unprocessed_options')
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>
                    @endif

                    @if(isset($vehicle['letters']))
                        <div class="col-sm-12 col-lg-8 col-lg-offset-2 collapse" id="collapseLetters{{ $vehicle['id'] }}">
                            <div class="panel panel-info marg-top-min">
                                <table class="table table-hover table-condensed">
                                    <thead class="bg-info">
                                    <Th></Th>
                                    <Th></Th>
                                    <Th>typ dokumentu</Th>
                                    <th>nazwa pisma</th>
                                    <th>nr szkody</th>
                                    <th>nr umowy</th>
                                    <th>nr rejestracyjny</th>
                                    </thead>
                                    @foreach($vehicle['letters'] as $letter)
                                        <tr class="vertical-middle">
                                            <td><a href="{{ URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]) }}" target="_blank" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a> </td>
                                            <td>
                                                @if( trim($letter->description) != '')
                                                    <a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="Opis pisma" data-content="{{ $letter->description }}">
                                                        <i class="fa fa-info-circle"></i> opis
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{  $letter->uploadedDocumentType->name }}</td>
                                            <td>{{  $letter->name }}</td>
                                            <td>{{  $letter->injury_nr }}</td>
                                            <td>{{  $letter->nr_contract }}</td>
                                            <td>{{  $letter->registration }}</td>
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
</table>
