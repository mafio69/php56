<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal" role="form">
                <div class="form-group form-group-sm" id="compensation_payment-group">
                {{ Form::confirmation(
                            'Wypłata odszkodowania',
                            'compensation_payment',
                            URL::route('dos.other.injuries.info.setAlert', array('compensation_payment', $injury->theft->id,'DosOtherInjuryTheft', 'Wypłata odszkodowania.')),
                            URL::route('dos.other.injuries.theft', array('setCompensation_payment_confirm', $injury->theft->id)),
                            $injury->theft,
                            '',
                            'potwierdź wypłatę odszkodowania',
                            'wreck_alert',
                            [],
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                }}
                </div>
                <div class="form-group form-group-sm" id="gap-group"
                @if($injury->theft->compensation_payment_confirm == '0000-00-00' || $injury->object->gap != 1 )
                    style="display: none;"
                @endif
                >
                {{ Form::confirmation(
                            'Wypłata GAP',
                            'gap',
                            URL::route('dos.other.injuries.info.setAlert', array('gap', $injury->theft->id,'DosOtherInjuryTheft', 'GAP.')),
                            URL::route('dos.other.injuries.theft', array('setGap_confirm', $injury->theft->id)),
                            $injury->theft,
                            '',
                            'potwierdź GAP',
                            'wreck_alert',
                            [],
                            array('col-sm-5','col-sm-4')
                        )
                }}
                </div>

            </form>
        </div>
    </div>
</div>