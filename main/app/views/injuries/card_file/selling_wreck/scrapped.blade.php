<div class="panel-heading">
    Złomowanie pojazdu
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form role="form" id="scrapped_container">
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="cassation_receipt_confirm-group">
                    {{ Form::confirmation(
                            'Kwit kasacyjny',
                            'cassation_receipt',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert', array('cassation_receipt', $injury->wreck->id,'InjuryWreck', 'Dostarczenie kwitu kasacyjnego')) : '',
                            ($injury->wreck) ? URL::route('injuries.info.setCassation_receipt_confirm', array($injury->wreck->id)) : '',
                            ($injury->wreck) ? $injury->wreck : null,
                            '',
                            'potwierdź kwit kasacyjny',
                            'wreck_alert',
                            ($disabled) ? array('disabled' => 'disabled') : array(),
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                    }}
                </div>
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="off_register_vehicle_confirm-group"
                    @if(!$injury->wreck || is_null($injury->wreck->cassation_receipt_confirm) )
                        style="display: none;"
                    @endif
                >
                    {{ Form::confirmation(
                            'Wyrejestrowanie pojazdu',
                            'off_register_vehicle',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert', array('off_register_vehicle', $injury->wreck->id,'InjuryWreck', 'Data wyrejstrowania pojazdu')) : '',
                            ($injury->wreck) ? URL::route('injuries.info.setOff_register_vehicle_confirm', array($injury->wreck->id)) : '',
                            ($injury->wreck) ? $injury->wreck : null,
                            '',
                            'potwierdź wyrejestrowanie pojazdu',
                            'wreck_alert',
                            ($disabled)?array('disabled' => 'disabled'):array(),
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                    }}
                </div>
                <div class="col-sm-12"></div>
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2 marg-top" id="scrapped-group"
                    @if(!$injury->wreck || is_null($injury->wreck->off_register_vehicle_confirm) )
                        style="display: none;"
                    @endif
                >
                    <span class="col-sm-12 col-md-8 col-lg-offset-2 label label-success " style="padding: 5px;">
                        Zakończono złomowanie
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
