<div class="panel-body">
    @if( $injury->theft->acceptations(5)->get()->isEmpty() )
        <span class="label label-danger pull-right" style="margin: 5px;">oczekiwanie na umorzenie sprawy</span>
    @endif
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal" role="form">
                <div class="form-group form-group-sm">
                {{ Form::confirmation(
                            'Zgłoszenie do ZU',
                            'send_zu',
                            URL::route('injuries.info.setAlert', array('send_zu', $injury->theft->id,'InjuryTheft', 'Termin zgłoszenia do ZU')),
                            URL::route('injuries.info.setSend_zu_confirm', array($injury->theft->id)),
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

                <div class="form-group form-group-sm" id="police_memo-group"
                @if($injury->theft->send_zu_confirm == '0000-00-00' )
                    style="display: none;"
                @endif
                >
                {{ Form::confirmation(
                            'Wystawienie notatki policyjnej',
                            'police_memo',
                            URL::route('injuries.info.setAlert', array('police_memo', $injury->theft->id,'InjuryTheft', 'Termin wystawienia notatki policyjnej')),
                            URL::route('injuries.info.setPolice_memo_confirm', array($injury->theft->id)),
                            $injury->theft,
                            '',
                            'potwierdź wystawienie notatki policyjnej',
                            'wreck_alert',
                            [
                            ],
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                }}
                </div>
            </form>
        </div>
    </div>
</div>