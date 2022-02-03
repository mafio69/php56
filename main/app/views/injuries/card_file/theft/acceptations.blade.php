<div class="panel-heading">
    Lista kontrolna dokumentów
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal" role="form" id="acceptations_form">
                    @foreach($theftAcceptation as $acceptation)
                        <?php $injury_acceptation = $injury->theft->acceptations($acceptation->id)->get()->first(); ?>
                    <div class="col-sm-6  marg-btm">
                        <div class="row">
                            <label class="col-sm-6 col-md-5  control-label">{{ $acceptation->name }}:</label>
                            <div class="input-group input-group-sm  col-sm-6 col-md-4   pull-left">
                                @if($acceptation->statuses == 1)
                                    {{ Form::select('status', Config::get('definition.theft_acceptation_satutes'), ($injury_acceptation) ? $injury_acceptation->status_id : null,
                                        [
                                            'class' => 'form-control wreck_alert',
                                            'id' => 'status_'.$acceptation->id,
                                            'hrf' => URL::route('injuries.info.theft.setAcceptationParam', array($injury->theft->id, $acceptation->id, 'status_id')), ($injury_acceptation && $injury_acceptation->date_acceptation != '0000-00-00 00:00:00') ? 'disabled' : ''
                                        ])
                                    }}
                                @endif
                                @if($acceptation->value)
                                    {{ Form::text('value', ($injury_acceptation) ? $injury_acceptation->value : null,
                                        [
                                            'class' => 'form-control wreck_alert required', 'placeholder' => $acceptation->value,
                                            'id'    => 'value_'.$acceptation->id,
                                            'hrf' => URL::route('injuries.info.theft.setAcceptationParam', array($injury->theft->id, $acceptation->id, 'value')), ($injury_acceptation && $injury_acceptation->date_acceptation != '0000-00-00 00:00:00') ? 'disabled' : ''
                                        ])
                                    }}
                                @endif
                                <span class="input-group-btn">
                                    <div class=" tips" data-toggle="buttons">
                                        <label id="l_acceptation_{{$acceptation->id}}"
                                               class="btn btn-confirmation btn-sm
                                                @if($injury->theft && $injury_acceptation && $injury_acceptation->date_acceptation != '0000-00-00 00:00:00')
                                                    active"
                                                    disabled="disabled"
                                                @else
                                                    "
                                                @endif
                                        >
                                            <input type="checkbox" class="set_acceptation @if($acceptation->value) with_required @endif" data-required="{{ 'value_'.$acceptation->id }}" hrf="{{ URL::route('injuries.info.theft.setAcceptation', [$injury->theft->id, $acceptation->id]) }}" >
                                            <i class="fa fa-check "></i>
                                        </label>

                                        <label class="btn btn-danger btn-sm" id="l_rollback_acceptation_{{$acceptation->id}}"
                                        @if($injury->theft && $injury_acceptation && $injury_acceptation->date_acceptation != '0000-00-00 00:00:00')
                                        @else
                                            style="display: none;"
                                        @endif
                                        >
                                            <input type="checkbox" class="rollback_acceptation" hrf="{{ URL::route('injuries.info.theft.rollbackAcceptation', [$injury->theft->id, $acceptation->id]) }}" >
                                            <i class="fa fa-remove "></i>
                                        </label>
                                    </div>
                                </span>
                            </div>

                            <span id="alert_acceptation_{{ $acceptation->id }}" class="label label-info col-sm-6 col-md-3"
                            @if(! $injury_acceptation || $injury_acceptation->date_acceptation == '0000-00-00 00:00:00')
                                style="display: none;"
                            @endif
                            >
                                @if($injury_acceptation && $injury_acceptation->date_acceptation != '0000-00-00 00:00:00')
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