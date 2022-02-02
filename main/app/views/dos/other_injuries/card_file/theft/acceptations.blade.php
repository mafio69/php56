<div class="panel-heading">
    Lista kontrolna dokumentów
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal" role="form">
                    @foreach($theftAcceptation as $acceptation)
                    <div class="col-sm-6  marg-btm">
                        <div class="row">
                            <label class="col-sm-10 col-lg-8  control-label">{{ $acceptation->name }}:</label>
                            <div class="input-group input-group-sm  col-sm-2 col-lg-1   pull-left">
                                <span class="input-group-btn">
                                    <div class=" tips" data-toggle="buttons">
                                        <label id="l_acceptation_{{$acceptation->id}}" class="btn btn-confirmation btn-sm
                                            @if($injury->theft && !$injury->theft->acceptations($acceptation->id)->get()->isEmpty() && $injury_acceptation = $injury->theft->acceptations($acceptation->id)->get()->first() )
                                                active"
                                                disabled="disabled"
                                            @else
                                            "
                                            @endif
                                        >
                                            <input type="checkbox" class="set_acceptation" hrf="{{ URL::route('dos.other.injuries.theft', ['setAcceptation', $injury->theft->id, $acceptation->id]) }}" >
                                            <i class="fa fa-check "></i>
                                        </label>
                                    </div>
                                </span>
                            </div>

                            <span id="alert_acceptation_{{ $acceptation->id }}" class="label label-info col-sm-6 col-lg-3"
                            @if($injury->theft->acceptations($acceptation->id)->get()->isEmpty() )
                                style="display: none;"
                            @endif
                            >
                                @if(!$injury->theft->acceptations($acceptation->id)->get()->isEmpty() )
                                    {{ substr($injury_acceptation->date_acceptation,0,-3).'<br>'.$injury_acceptation->user->name }}
                                @endif
                            </span>

                        </div>
                    </div>
                    @endforeach
            </form>
        </div>
    </div>
</div>