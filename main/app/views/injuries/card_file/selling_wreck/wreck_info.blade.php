<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 ">
            <form role="form" class="btm-space-primary" style="display: inline-block;">
                {{
                    Form::autosaveInput(
                        'value_undamaged',
                        'Wartość pojazdu w stanie nieuszkodzonym',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input currency_input number',
                        [
                            'validation'=> 'numeric',
                            'right_space' => '25',
                            'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-4']
                    )
                }}
                {{
                    Form::autosaveSelect(
                        'value_undamaged_net_gross',
                        Config::get('definition.net_gross'),
                        'netto - brutto',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}
                {{
                    Form::autosaveSelect(
                        'value_undamaged_currency',
                        Config::get('definition.currencies'),
                        'waluta',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}
                {{
                    Form::autosaveInput(
                        'nr_auction',
                        'Numer aukcyjny ubezpieczalni',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input ',
                        [
                            'validation'=> '',
                            'right_space' => '25',
                            'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-4']
                    )
                }}

                {{
                   Form::autosaveInput(
                       'value_repurchase',
                       'Wartość pojazdu do odkupu przez LB',
                       'InjuryWreck',
                       ($injury->wreck) ? $injury->wreck : null,
                       'focusout-input currency_input number',
                       ($injury->wreck) ?
                           [
                               'validation'=> 'numeric',
                               'right_space' => '25',
                               'disabled'   => ($disabled)?'disabled':''
                           ]
                       : array(),
                       ['col-sm-12 col-md-6 col-lg-4 col-lg-offset-2']
                   )
                }}
                {{
                    Form::autosaveSelect(
                        'value_repurchase_net_gross',
                        Config::get('definition.net_gross'),
                        'netto - brutto',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}
                {{
                    Form::autosaveSelect(
                        'value_repurchase_currency',
                        Config::get('definition.currencies'),
                        'waluta',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2 ']
                    )
                }}

                {{
                   Form::autosaveInput(
                       'value_compensation',
                       'Wartość odszkodowania',
                       'InjuryWreck',
                       ($injury->wreck) ? $injury->wreck : null,
                       'focusout-input currency_input number',
                       ($injury->wreck) ?
                           [
                               'validation'=> 'numeric',
                               'right_space' => '25',
                               'disabled'   => ($disabled)?'disabled':''
                           ]
                       : array(),
                       ['col-sm-12 col-md-6 col-lg-4 col-lg-offset-2']
                   )
                }}
                {{
                    Form::autosaveSelect(
                        'value_compensation_net_gross',
                        Config::get('definition.net_gross'),
                        'netto - brutto',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}
                {{
                    Form::autosaveSelect(
                        'value_compensation_currency',
                        Config::get('definition.currencies'),
                        'waluta',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}

                {{
                    Form::autosaveInput(
                        'value_tenderer',
                        'Cena od oferenta aukcyjnego',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input currency_input number',
                        [
                           'validation'=> 'numeric',
                           'right_space' => '25',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-4 col-lg-offset-2']
                    )
                }}
                {{
                    Form::autosaveSelect(
                        'value_tenderer_net_gross',
                        Config::get('definition.net_gross'),
                        'netto - brutto',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}
                {{
                    Form::autosaveSelect(
                        'value_tenderer_currency',
                        Config::get('definition.currencies'),
                        'waluta',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'right_space' => '35',
                           'disabled'   => ($disabled)?'disabled':''
                        ],
                        ['col-sm-12 col-md-6 col-lg-2']
                    )
                }}

                {{
                    Form::autosaveInput(
                        'expire_tenderer',
                        'Data ważności oferty oferenta aukcyjnego',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input datepicker',
                        [
                            'validation'=> 'date',
                            'right_space' => '25',
                            'disabled'   => ($disabled)?'disabled':''
                        ],
                        array('col-sm-12 col-md-6 col-lg-offset-2')
                    )
                }}

                <div class="form-group has-feedback col-sm-12 col-md-6 col-lg-2">
                    <label class="control-label" for="label_check_if_tenderer">Oferent:</label>
                    @if(Auth::user()->can('kartoteka_szkody#sprzedaz_wraku#zarzadzaj'))
                    <div class="input-group input-group-sm">
                        <span class="input-group-btn">
                            <div class="btn-group tips" data-toggle="buttons" title="potwierdź czy jest oferent">
                                <label class="btn btn-confirmation btn-sm @if($injury->wreck && $injury->wreck->if_tenderer) active @endif" id="label_check_if_tenderer">
                                    <input type="checkbox" class="alert_confirmation" @if($injury->wreck) wreck_id="{{ $injury->wreck->id }}" hrf="{{ URL::to('injuries/info/wreck/if_tenderer_confirm', $injury->wreck->id) }}" @endif>
                                    <i class="fa fa-check "></i>
                                </label>
                            </div>
                        </span>
                    </div>
                    @else
                        <br>
                        @if($injury->wreck && $injury->wreck->if_tenderer)
                            <span class="label label-success">
                            <i class="fa fa-check"></i>
                            </span>
                        @else
                            <span class="label label-warning">
                            <i class="fa fa-minus"></i>
                            </span>
                        @endif
                    @endif
                </div>
            </form>
        </div>
        <div class="col-sm-12 ">
            <form class="form-horizontal btm-space-primary" role="form">
                <div class="form-group form-group-sm">

                    {{ Form::confirmation(
                            'Termin odesłenia deklaracji przez LB',
                            'alert_repurchase',
                            ($injury->wreck)?URL::route('injuries.info.setAlert', array('alert_repurchase', $injury->wreck->id,'InjuryWreck','Termin odesłenia deklaracji przez LB')):'',
                            ($injury->wreck)?URL::route('injuries.info.setAlert_repurchase_confirm', array($injury->wreck->id)):'',
                            ($injury->wreck)?$injury->wreck:null,
                            'nie wygerowano deklaracji odkupu wraku przez leasingobiorcę',
                            'potwierdź odesłanie deklaracji',
                            'wreck_alert',
                            /*($disabled || ($injury->wreck && $injury->wreck->not_applicable) || ($injury->wreck &&  ($injury->wreck->alert_repurchase == '0000-00-00' || $injury->total_status_id == 5 || $injury->wreck->invoice_request!='0000-00-00'))) ? array('disabled' => 'disabled') : array()*/
                            array(),
                            array('col-sm-4','col-sm-4'),
                            true
                        )
                    }}
                    @if($injury->wreck)
                    <div class="col-sm-4">
                        @if(Auth::user()->can('kartoteka_szkody#sprzedaz_wraku#zarzadzaj'))
                            <div class="btn-group tips" data-toggle="buttons" title="proceduj bez odesłania deklaracji">
                                <label class="btn btn-confirmation btn-sm @if($injury->wreck && $injury->wreck->not_applicable) active @endif" id="label_check_not_applicable"
                                   @if($injury->wreck->scrapped || $injury->wreck->alert_repurchase_confirm != '0000-00-00' || $injury->total_status_id == 5 || $injury->wreck->invoice_request!='0000-00-00') disabled = "disabled" @endif>
                                        <input type="checkbox" class="alert_confirmation"
                                               wreck_id="{{ $injury->wreck->id }}"
                                               hrf="{{ URL::route('injuries.info.setNotApplicable', array($injury->wreck->id)) }}">
                                    <i class="fa fa-times fa-fw"></i> nie dotyczy
                                </label>
                            </div>
                            <div class="btn-group tips" data-toggle="buttons" title="złomowanie pojazdu">
                                <label class="btn btn-confirmation btn-sm @if($injury->wreck && $injury->wreck->scrapped) active @endif" id="label_check_scrapped"
                                       @if($injury->wreck && ($injury->wreck->alert_repurchase_confirm != '0000-00-00' ||$injury->total_status_id == 5 || $injury->wreck->invoice_request!='0000-00-00')) disabled = "disabled" @endif>
                                        <input type="checkbox" class="alert_confirmation"
                                               wreck_id="{{ $injury->wreck->id }}"
                                               hrf="{{ URL::route('injuries.info.setScrapped', array($injury->wreck->id)) }}">
                                    <i class="fa fa-times fa-fw"></i> złomowanie pojazdu
                                </label>
                            </div>
                        @else
                            @if($injury->wreck && $injury->wreck->not_applicable)
                                <span class="label label-success">nie dotyczy</span>
                            @endif
                            @if($injury->wreck && $injury->wreck->scrapped)
                                <span class="label label-success">złomowanie pojazdu</span>
                            @endif
                        @endif
                    </div>
                    @endif
                </div>
            </form>
            <form role="form" id="wreck_data"
                @if(!$injury->wreck || ($injury->wreck->alert_repurchase_confirm == '0000-00-00' && ! $injury->wreck->not_applicable) || $injury->wreck->scrapped)
                    style="display: none;"
                @endif
            >
                {{
                    Form::autosaveSelect(
                        'buyer',
                        Config::get('definition.wreck_buyers'),
                        'Nabywca',
                        'InjuryWreck',
                        ($injury->wreck) ? $injury->wreck : null,
                        'focusout-input',
                        [
                           'validation'=> 'numeric',
                           'right_space' => '35',
                           'disabled'   => ($disabled || ($injury->wreck->pro_forma_request != '0000-00-00' && !in_array(Auth::user()->login, ['przem_k', 'justynan']) ) || $injury->wreck->invoice_request != '0000-00-00') ? 'disabled' : ''
                        ],
                        ['col-sm-12']
                    )
                }}
            </form>
        </div>
    </div>
</div>
