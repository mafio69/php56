@if(Auth::user()->can('kartoteka_szkody#historia'))
    <div class="tab-pane fade in " id="step_history">

        @foreach ($stepHistory as $k => $v)
            {{-- @if($v->history_type_id > 0) --}}
                <p class="clearfix ">
                    <strong>{{substr($v->created_at,0,-3)}} - {{$v->user->name}}:</strong>
                    <em>
                        {{ $v->prevStep?$v->prevStep->name:''}}
                        =>
                        {{ $v->nextStep?$v->nextStep->name:''}}
                    </em>
                    <strong>{{ $v->stepStage?' : '.$v->stepStage->name:''}}</strong>
                <hr class="short"/>
                </p>
        @endforeach

        @if($injury->mobileInjury)
            <p class="clearfix ">
                <strong>{{substr($injury->mobileInjury->created_at,0,-3)}} - system:</strong>
                napłynięcie zgłoszenia online
            <hr class="short"/>
            </p>
        @endif
    </div>
@endif
