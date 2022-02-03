<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal" role="form">
                <div class="form-group form-group-sm" id="redemption_investigation-group">
                    <label class="control-label col-sm-5 alert_label" for="redemption_investigation">Postanowienie o umorzeniu dochodzenia:</label>
                    <div class="input-group input-group-sm  col-sm-4  pull-left">
                        <span class="input-group-btn">
                            <div class="btn-group tips" data-toggle="buttons" @if($injury->theft->punishable) title="potwierdzono {{ $injury->theft->punishable }}" @else title="potwierdź brak znamion" @endif>
                                <label class="btn btn-confirmation btn-sm @if($injury->theft->punishable) active @endif " @if(($injury->theft->punishable && (!Auth::user()->can('kartoteka_szkody#kradziez#edycja'))) || $injury->theft->redemption_investigation_confirm != '0000-00-00') disabled="disabled" @endif id="label_check_punishable">
                                    <input type="checkbox" class="alert_confirmation" id="check_punishable" wreck_id="{{ $injury->theft->id }}" hrf="{{ URL::route('injuries.info.setPunishable', array($injury->theft->id)) }}" @if($injury->theft->punishable || $injury->theft->redemption_investigation_confirm != '0000-00-00') disabled="disabled" @endif>
                                    <i class="fa fa-check "></i>
                                    brak znamion czynu karalnego
                                </label>
                            </div>
                        </span>
                        <input name="redemption_investigation" id="redemption_investigation" type="text"
                               class="form-control datepicker input-sm tips wreck_alert "
                               value="{{ $injury->theft->redemption_investigation }}"
                               wreck_id="{{ $injury->theft->id }}"
                               hrf="{{URL::route('injuries.info.setAlert', array('redemption_investigation', $injury->theft->id,'InjuryTheft', 'Termin wydania postanowienia o umorzeniu dochodzenia.'))}}" placeholder="Postanowienie o umorzeniu dochodzenia"
                                @if($injury->theft->redemption_investigation_confirm != '0000-00-00' || $injury->theft->punishable)
                                disabled="disabled"
                               @endif
                        >
                        <span class="input-group-btn">
                            <div class="btn-group tips" data-toggle="buttons" @if($injury->theft->redemption_investigation_confirm != '0000-00-00') title="potwierdzono {{ $injury->theft->redemption_investigation_confirm }}" @else title="potwierdź wydanie postanowienia" @endif>
                                <label class="btn btn-confirmation btn-sm @if($injury->theft->redemption_investigation_confirm != '0000-00-00') active @endif " @if(($injury->theft->redemption_investigation_confirm != '0000-00-00' && !Auth::user()->can('kartoteka_szkody#kradziez#edycja')) || $injury->theft->punishable) disabled="disabled" @endif id="label_check_redemption_investigation">
                                    <input type="checkbox" class="alert_confirmation" wreck_id="{{ $injury->theft->id }}"
                                           @if($injury->theft->redemption_investigation_confirm != '0000-00-00' || $injury->theft->punishable) disabled="disabled" @endif
                                           hrf="{{ URL::route('injuries.info.setRedemption_investigation_confirm', array($injury->theft->id)) }}">
                                    <i class="fa fa-check "></i>
                                </label>
                            </div>
                        </span>
                    </div>
                </div>
                @if(! $injury->theft->punishable)
                    <div class="form-group form-group-sm" id="deregistration_vehicle-group"
                    @if($injury->theft->redemption_investigation_confirm == '0000-00-00' )
                        style="display: none;"
                    @endif
                    >
                    {{ Form::confirmation(
                                'Wyrejestrowanie pojazdu',
                                'deregistration_vehicle',
                                URL::route('injuries.info.setAlert', array('deregistration_vehicle', $injury->theft->id,'InjuryTheft', 'Termin wyrejestrowania pojazdu.')),
                                URL::route('injuries.info.setDeregistration_vehicle_confirm', array($injury->theft->id)),
                                $injury->theft,
                                '',
                                'potwierdź wyrejestrowanie pojazdu',
                                'wreck_alert',
                                [
                                ],
                                array('col-sm-5','col-sm-4'),
                                true
                            )
                    }}
                    </div>
                    <div class="form-group form-group-sm" id="compensation_payment-group"
                    @if($injury->theft->deregistration_vehicle_confirm == '0000-00-00' )
                        style="display: none;"
                    @endif
                    >
                        {{--
                        <label class="control-label col-sm-5 alert_label" for="compensation_payment">Wypłata odszkodowania:</label>
                        <div class="input-group input-group-sm  col-sm-4  pull-left">
                            <input name="compensation_payment" value="{{ $injury->theft->compensation_payment }}" id="compensation_payment" type="text" class="form-control datepicker input-sm tips wreck_alert hasDatepicker" placeholder="Wypłata odszkodowania" data-original-title="" title="" disabled="disabled">
                            <span class="input-group-btn">
                                <div class="btn-group tips" data-toggle="buttons" title="" data-original-title="<p>potwierdź wypłatę odszkodowania</p>">
                                    <label class="btn btn-confirmation btn-sm   active" id="label_check_compensation_payment">
                                        <input type="checkbox" class="alert_confirmation" wreck_id="{{ $injury->theft->id }}" hrf="http://idea.app:8000/injuries/info/theft/compensation_payment_confirm/{{ $injury->theft->id }}" disabled="disabled">
                                        <i class="fa fa-check "></i>
                                    </label>
                                </div>
                            </span>
                        </div>
                        --}}

                        <label class="control-label col-sm-5 col-md-4 alert_label" for="compensation_payment">Odszkodowanie wypłata/odmowa:</label>
                        <div class="col-sm-6 col-md-2">
                            <input class="form-control focusout-input tips" value="{{ $injury->theft->compensation_payment_value }}"
                                   name="compensation_payment_value"
                                   id="compensation_payment_value"
                                   hrf="{{ URL::route('injuries.info.setValue', array($injury->theft->id, 'compensation_payment_value', 'InjuryTheft','Wartość odszkodowania')) }}"
                                   placeholder="podaj wartość odszkodowania"
                                   title="wartość odszkodowania"
                                   @if($injury->theft->compensation_payment_confirm != '0000-00-00' || $injury->theft->compensation_payment_deny)
                                   disabled="disabled"
                                    @endif
                            >
                        </div>
                        <div class="input-group input-group-sm  col-sm-4 col-md-3 pull-left">
                            <input name="compensation_payment" id="compensation_payment" type="text"
                                   class="form-control datepicker input-sm tips wreck_alert "
                                   value="{{ $injury->theft->compensation_payment }}"
                                   wreck_id="{{ $injury->theft->id }}"
                                   hrf="{{URL::route('injuries.info.setAlert', array('compensation_payment', $injury->theft->id,'InjuryTheft', 'Wypłata odszkodowania.'))}}"
                                   placeholder="Wypłata odszkodowania"
                                   title="wypłata odszkodowania"
                                   @if($injury->theft->compensation_payment_confirm != '0000-00-00' || $injury->theft->compensation_payment_deny)
                                    disabled="disabled"
                                   @endif
                            >
                            <span class="input-group-btn">
                                <div class="btn-group tips" data-toggle="buttons" @if($injury->theft->compensation_payment_deny ) title="potwierdzono {{ $injury->theft->compensation_payment_deny }}" @else title="potwierdź odmowę wypłaty odszkodowania" @endif>
                                    <label class="btn btn-confirmation btn-sm @if($injury->theft->compensation_payment_deny) active @endif " @if(($injury->theft->compensation_payment_deny && !Auth::user()->can('kartoteka_szkody#kradziez#edycja')) || $injury->theft->compensation_payment_confirm != '0000-00-00') disabled="disabled" @endif id="label_deny_compensation_payment">
                                        <input type="checkbox" class="alert_confirmation" wreck_id="{{ $injury->theft->id }}"
                                               @if($injury->theft->compensation_payment_deny || $injury->theft->compensation_payment_confirm != '0000-00-00') disabled="disabled" @endif
                                               hrf="{{ URL::route('injuries.info.setCompensation_payment_deny', array($injury->theft->id)) }}">
                                        <i class="fa fa-minus"></i>
                                    </label>
                                </div>
                            </span>
                            <span class="input-group-btn">
                                <div class="btn-group tips" data-toggle="buttons" @if($injury->theft->compensation_payment_confirm != '0000-00-00') title="potwierdzono {{ $injury->theft->compensation_payment_confirm }}" @else title="potwierdź wypłatę odszkodowania" @endif>
                                    <label class="btn btn-confirmation btn-sm @if($injury->theft->compensation_payment_confirm != '0000-00-00') active @endif " @if(($injury->theft->compensation_payment_confirm != '0000-00-00' && !Auth::user()->can('kartoteka_szkody#kradziez#edycja')) || $injury->theft->compensation_payment_deny) disabled="disabled" @endif id="label_check_compensation_payment">
                                        <input type="checkbox" class="alert_confirmation" wreck_id="{{ $injury->theft->id }}"
                                               @if($injury->theft->compensation_payment_confirm != '0000-00-00' || $injury->theft->compensation_payment_deny) disabled="disabled" @endif
                                               hrf="{{ URL::route('injuries.info.setCompensation_payment_confirm', array($injury->theft->id)) }}">
                                        <i class="fa fa-check "></i>
                                    </label>
                                </div>
                            </span>
                        </div>

                    </div>
                    <div class="form-group form-group-sm" id="gap-group"
                {{-- @if( ($injury->theft->compensation_payment_confirm == '0000-00-00' &&  ! $injury->theft->compensation_payment_deny) || $injury->vehicle->gap != 1 ) --}}
                @if( ($injury->theft->compensation_payment_confirm == '0000-00-00' &&  ! $injury->theft->compensation_payment_deny) || $injury->injuryPolicy->gap != 1 )
                    style="display: none;"
                @endif
                >
                {{ Form::confirmation(
                            'Wypłata GAP',
                            'gap',
                            URL::route('injuries.info.setAlert', array('gap', $injury->theft->id,'InjuryTheft', 'GAP.')),
                            URL::route('injuries.info.setGap_confirm', array($injury->theft->id)),
                            $injury->theft,
                            '',
                            'potwierdź GAP',
                            'wreck_alert',
                            [
                            ],
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                }}
                </div>
                @endif

            </form>
        </div>
    </div>
</div>