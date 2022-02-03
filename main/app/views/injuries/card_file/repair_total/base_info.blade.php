<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form class="form-horizontal btm-space-primary" role="form">
                <div class="form-group form-group-sm">
                {{ Form::confirmation(
                            'Termin napłynięcia wniosku o naprawę',
                            'alert_receive',
                            ($injury->totalRepair) ? URL::route('injuries.info.setAlert', array('alert_receive', $injury->totalRepair->id,'InjuryTotalRepair', 'Termin napłynięcia wniosku o naprawę')):'',
                            ($injury->totalRepair) ? URL::route('injuries.info.setAlert_receive_confirm', array($injury->totalRepair->id)):'',
                            ($injury->totalRepair) ? $injury->totalRepair:null,
                            '',
                            'potwierdź napłynięcie wniosku',
                            'wreck_alert',
                            array(),
                            array('col-sm-5','col-sm-4')
                        )
                }}
                </div>
            </form>
            <form role="form" id="repair_confirmation_data"
            @if(!$injury->totalRepair || $injury->totalRepair->alert_receive_confirm == '0000-00-00')
                style="display: none;"
            @endif
            >
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2" id="repair_agreement_date-group">
                    <div type="button" class="btn btn-primary btn-sm btn-block modal-open generate_doc"
                        @if(!$injury->totalRepair || $injury->totalRepair->repair_agreement_date != '0000-00-00')
                                disabled="disabled"
                        @endif
                        id="doc_17"
                        target="{{ URL::route('injuries-generate-docs-info', array($injury->id, 17)) }}" data-toggle="modal" data-target="#modal" >
                        wygeneruj i wyślij zgodę na naprawę szkody całkowitej
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>