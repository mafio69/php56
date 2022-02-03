<?php $disableBalanceWreck = ( $injury->wreck->dok_transfer != '0000-00-00' || $injury->totalRepair)?true:false;?>
<div class="tab-pane fade in " id="balance_wreck">
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
            <div class="panel panel-primary">
                @if($vehicle->gap == 0)
                    <span class="label label-danger pull-right" style="margin: 5px;">niesprawdzono ustawień GAP</span>
                @endif
                <form class="form-horizontal btm-space-primary marg-btm" role="form">
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-md-offset-2 ">
                            <div class="form-group ">
                                <label class="col-sm-7 col-md-6 control-label">Wartość pojazdu nieuszkodzonego:</label>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <p class="form-control-static" id="balance_value_undamaged">{{ $injury->wreck->value_undamaged }} zł</p>
                                </div>
                            </div>
                        </div>
                        @if($injury->vehicle->gap == 1)
                        <div class="form-group has-feedback" id="value_invoice-group">
                            <label for="value_invoice" class="col-sm-7 col-md-6 control-label">Wartość z faktury
                            @if($injury->vehicle->netto_brutto == 1)
                            [netto]
                            @elseif($injury->vehicle->netto_brutto == 2)
                            [brutto]
                            @else
                            [netto/brutto]
                            @endif
                            :</label>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <input class="form-control focusout-input" value="{{ $injury->wreck->value_invoice }}" name="value_invoice" id="value_invoice" right_space="25"
                                    hrf="{{ URL::route('injuries.info.setValue', array($injury->wreck->id, 'value_invoice', 'InjuryWreck','Wartość z faktury')) }}"  placeholder="podaj wartość"
                                    @if($disableBalanceWreck)
                                        disabled
                                    @endif
                                >
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="col-sm-7 col-md-6 control-label">Wartość wraku:</label>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <p class="form-control-static" id="balance_repurchase_price">{{ $injury->wreck->repurchase_price }} zł</p>
                            </div>
                        </div>
                        <div class="form-group has-feedback" id="value_compensation-group">
                            <label for="value_compensation" class="col-sm-7 col-md-6 control-label">Wartość odszkodowania:</label>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <input class="form-control focusout-input" value="{{ $injury->wreck->value_compensation }}" name="value_compensation" id="value_compensation" right_space="25"
                                    hrf="{{ URL::route('injuries.info.setValue', array($injury->wreck->id, 'value_compensation', 'InjuryWreck','Wartość odszkodowania')) }}" placeholder="podaj wartość"
                                    @if($disableBalanceWreck)
                                        disabled
                                    @endif
                                >
                            </div>
                        </div>
                        <div class="form-group has-feedback" id="extra_charge_ic-group">
                            <label for="extra_charge_ic" class="col-sm-7 col-md-6 control-label">Dopłata zakładu ubezpieczeń:</label>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <input class="form-control focusout-input" value="{{ $injury->wreck->extra_charge_ic }}" name="extra_charge_ic" id="extra_charge_ic" right_space="25"
                                    hrf="{{ URL::route('injuries.info.setValue', array($injury->wreck->id, 'extra_charge_ic', 'InjuryWreck','Dopłata zakładu ubezpieczeń')) }}" placeholder="podaj wartość"
                                    @if($disableBalanceWreck)
                                        disabled
                                    @endif
                                >
                            </div>
                        </div>
                        @if($injury->vehicle->gap == 1)
                        <div class="form-group has-feedback" id="value_gap-group">
                            <label for="value_gap" class="col-sm-7 col-md-6 control-label">GAP
                            @if($injury->vehicle->netto_brutto == 1)
                            [netto]
                            @elseif($injury->vehicle->netto_brutto == 2)
                            [brutto]
                            @else
                            [netto/brutto]
                            @endif
                            :</label>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <input class="form-control focusout-input" value="{{ $injury->wreck->value_gap }}" name="value_gap" id="value_gap" right_space="25"
                                    hrf="{{ URL::route('injuries.info.setValue', array($injury->wreck->id, 'value_gap', 'InjuryWreck','GAP')) }}" placeholder="podaj wartość"
                                    @if($disableBalanceWreck)
                                        disabled
                                    @endif
                                >
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
                <form class="form-horizontal " role="form">
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-md-offset-2 ">
                            <div class="form-group ">
                                <label class="col-sm-7 col-md-6 control-label">Wynik bilansu:</label>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <p class="form-control-static">
                                        <span id="balance_result" class="label label-success balance-label">{{ $injury->wreck->value_undamaged }} zł</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                {{ Form::open(array('url' => URL::route('injuries.info.wreck.dok_transfer', array($injury->id)), 'method' => 'post')) }}
                    <div class="row marg-btm">
                        <div class="col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2" id="dok_transfer-group"
                        @if($injury->wreck->invoice_request_confirm == '0000-00-00')
                            style="display: none;"
                        @endif
                        >
                            <button type="submit" class="btn btn-primary btn-sm btn-block let_disable"
                                @if($injury->wreck->dok_transfer != '0000-00-00')
                                        disabled="disabled"
                                @endif
                            >
                                przekaż do DOK
                            </button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>

    </div>
</div>
@section('headerJs')
  @parent

@stop