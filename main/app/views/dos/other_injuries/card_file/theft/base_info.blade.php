<div class="panel-body">
    @if( $injury->theft->acceptations(5)->get()->isEmpty() && in_array( $injury->type_incident_id, array(1,2,3))  )
        <span id="label_theft_warning" class="label label-danger pull-right" style="margin: 5px;">oczekiwanie na umorzenie sprawy</span>
    @endif
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal" role="form">
                <div class="form-group form-group-sm">
                {{ Form::confirmation(
                            'Zgłoszenie do ZU',
                            'send_zu',
                            URL::route('dos.other.injuries.info.setAlert', array('send_zu', $injury->theft->id,'DosOtherInjuryTheft', 'Termin zgłoszenia do ZU')),
                            URL::route('dos.other.injuries.theft', array('setSend_zu_confirm', $injury->theft->id)),
                            $injury->theft,
                            '',
                            'potwierdź zgłoszenie do ZU',
                            'wreck_alert',
                            ($disabled_theft)?array('disabled' => 'disabled'):array(),
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                }}
                </div>
            </form>
        </div>
    </div>
</div>