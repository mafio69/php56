<div class="panel-heading">
    Księgowość
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form role="form" id="pro_forma_container"
            @if($injury->wreck && $injury->wreck->alert_buyer_confirm == '0000-00-00' && $injury->wreck->buyer == 2)
                style="display: none;"
            @endif
            >
                <div class="form-group form-group-sm text-center col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2" id="pro_forma_request-group">
                    @if($injury->wreck->pro_forma_request != '0000-00-00')
                        <span class="small text-danger">
                            wysłane: {{ $injury->wreck->pro_forma_request }}
                        </span>
                    @endif

                    <div type="button" class="btn btn-sm btn-primary btn-block tips modal-open"
                    @if(

                            !$injury->wreck
                            ||
                            ( $injury->wreck->pro_forma_request != '0000-00-00' && (!in_array(Auth::user()->login, ['przem_k', 'justynan']) || $injury->wreck->invoice_request != '0000-00-00' ))
                            ||
                            $disabled
                        )
                        disabled="disabled"
                    @else
                        data-toggle="modal" data-target="#modal"
                        target="{{ URL::route('injuries.info.getProFormaRequest', array($injury->wreck->id)) }}"
                    @endif
                    >
                        wyślij prośbę o fakturę pro forma
                    </div>
                </div>

                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="pro_forma_request_confirm-group"
                @if(!$injury->wreck || $injury->wreck->pro_forma_request == '0000-00-00' )
                    style="display: none;"
                @endif
                >
                    {{ Form::confirmation(
                            'Termin dostarczenia faktury pro forma',
                            'pro_forma_request',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert', array('pro_forma_request', $injury->wreck->id,'InjuryWreck', 'Termin dostarczenia faktury pro forma')) : '',
                            ($injury->wreck) ? URL::route('injuries.info.setPro_forma_request_confirm', array($injury->wreck->id)) : '',
                            ($injury->wreck) ? $injury->wreck : null,
                            '',
                            'potwierdź dostarczenie faktury pro forma',
                            'wreck_alert',
                            ($disabled || $injury->wreck->invoice_request!='0000-00-00')?array('disabled' => 'disabled'):array(),
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                    }}
                </div>
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="pro_forma_info-group"
                     @if(!$injury->wreck || $injury->wreck->pro_forma_request_confirm == '0000-00-00' )
                     style="display: none;"
                        @endif
                >
                    {{
                        Form::autosaveInput(
                            'pro_forma_number',
                            'Numer FV proforma',
                            'InjuryWreck',
                            ($injury->wreck) ? $injury->wreck : null,
                            'focusout-input',
                            [
                                'right_space' => '25',
                                'disabled'   => ($disabled)?'disabled':''
                            ],
                            ['col-sm-12']
                        )
                    }}
                    {{
                        Form::autosaveInput(
                            'contractor_code',
                            'Kod kontrahenta',
                            'InjuryWreck',
                            ($injury->wreck) ? $injury->wreck : null,
                            'focusout-input',
                            [
                                'right_space' => '25',
                                'disabled'   => ($disabled)?'disabled':''
                            ],
                            ['col-sm-12']
                        )
                    }}
                    {{
                        Form::autosaveInput(
                            'pro_forma_value',
                            'Kwota brutto z FV',
                            'InjuryWreck',
                            ($injury->wreck) ? $injury->wreck : null,
                            'focusout-input currency_input number',
                            [
                                'validation'=> 'numeric',
                                'right_space' => '25',
                                'disabled'   => ($disabled)?'disabled':''
                            ],
                            ['col-sm-12']
                        )
                    }}
                </div>
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="payment_confirm-group"
                @if(!$injury->wreck || $injury->wreck->pro_forma_request_confirm == '0000-00-00' )
                    style="display: none;"
                @endif
                >
                    {{ Form::confirmation(
                            'Data dokonania płatności',
                            'payment',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert', array('payment', $injury->wreck->id,'InjuryWreck', 'Data dokonania płatności')) : '',
                            ($injury->wreck) ? URL::route('injuries.info.setPayment_confirm', array($injury->wreck->id)) : '',
                            ($injury->wreck) ? $injury->wreck : null,
                            '',
                            'potwierdź dokonanie płatności',
                            'wreck_alert',
                            ($disabled || $injury->wreck->invoice_request!='0000-00-00')?array('disabled' => 'disabled'):array(),
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                    }}
                </div>
                <div class="col-sm-12"></div>
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2" id="invoice_request-group"
                @if($injury->wreck && $injury->wreck->payment_confirm == '0000-00-00')
                    style="display: none;"
                @endif
                >
                    <div type="button" class="btn btn-sm btn-primary btn-block tips modal-open"
                    @if(!$injury->wreck || $injury->wreck->invoice_request != '0000-00-00' || $disabled)
                        disabled="disabled"
                    @else
                        data-toggle="modal" data-target="#modal"
                        target="{{ URL::route('injuries.info.getInvoiceRequest', array($injury->wreck->id)) }}"
                    @endif
                    >
                        wyślij prośbę o fakturę
                    </div>
                </div>

                <div class="form-group form-group-sm col-sm-12 col-md-10 col-md-offset-1 form-horizontal" id="invoice_request_confirm-group"
                @if(!$injury->wreck || $injury->wreck->invoice_request == '0000-00-00' )
                    style="display: none;"
                @endif
                >
                    {{ Form::confirmation(
                            'Potwierdzenie dostarczenia faktury',
                            'invoice_request',
                            ($injury->wreck) ? URL::route('injuries.info.setAlert', array('payment', $injury->wreck->id,'InjuryWreck', 'Potwierdzenie dostarczenia faktury')) : '',
                            ($injury->wreck) ? URL::route('injuries.info.setInvoice_request_confirm', array($injury->wreck->id)) : '',
                            ($injury->wreck) ? $injury->wreck : null,
                            '',
                            'potwierdź dostarczenie faktury',
                            'wreck_alert',
                            ($disabled)?array('disabled' => 'disabled'):array(),
                            array('col-sm-5','col-sm-4'),
                            true
                        )
                    }}
                </div>
                <div class="form-group form-group-sm col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2 marg-top" id="wreck_success-group"
                    @if(!$injury->wreck || $injury->wreck->invoice_request_confirm == '0000-00-00')
                        style="display: none;"
                    @endif
                >
                    <span class="col-sm-12 col-md-8 col-lg-offset-2 label label-success " style="padding: 5px;">
                        Zakończono sprzedaż wraka
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
