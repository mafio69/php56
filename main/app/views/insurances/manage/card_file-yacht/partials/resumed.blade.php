@foreach($insurances = $agreement->insurances()->whereNull('refunded_insurance_id')->whereNotNull('resumed_insurance_id')->active()->with('insuranceType', 'insuranceCompany', 'leasingAgreementPaymentWay', 'refundInsurance', 'coverages', 'coverages.type', 'payments', 'resumingInsurance')
                    ->orderBy('id','desc')->get() as $k => $insurance)
        <div class="col-sm-12 col-md-8 col-md-offset-2 item-m marg-btm">
            <div class="panel panel-default panel-primary small" style="margin-bottom: 0px;">
                <div class="panel-heading overflow ">
                    <h4 class="panel-title">
                        {{ $insurance->insurance_number }}

                        @if($insurance->if_foreign_policy == 1)
                            <span class="label label-warning">Polisa obca</span>
                        @endif

                        @if(is_null($agreement->archive) && $insurance->active == 1 && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                            <a href="{{ URL::to('insurances/info-insurances/edit-yacht',[$insurance->id]) }}" title="edytuj" class="pull-right tips" style="font-size: 17px;cursor: pointer;"    >
                                <i class="fa fa-pencil-square-o "  ></i>
                            </a>
                        @endif
                        @if(is_null($agreement->archive) && $insurance->active == 1 && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                            <span class="btn btn-warning btn-xs marg-right pull-right modal-open" data-toggle="modal" data-target="#modal"
                                  target="{{ URL::to('insurances/info-dialog/rollback-insurance-yacht', [$insurance->id]) }}" >
                                        <span class="fa fa-undo"></span> wycofaj polisę
                                    </span>
                        @endif
                        <small class="pull-right marg-right">{{ substr($insurance->created_at,0,-3) }}</small>
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="col-sm-12 col-md-6">
                        <table class="table table-hover table-condensed">
                            <tr>
                                <td><label>Nr polisy:</label></td>
                                <Td>{{ $insurance->insurance_number }}</td>
                            </tr>
                            <tr>
                                <td><label>Nr zgłoszenia:</label></td>
                                <Td>{{ $insurance->notification_number }}</td>
                            </tr>
                            <tr>
                                <td><label>Liczba miesięcy:</label></td>
                                <Td>{{ $insurance->months }}</td>
                            </tr>

                            <tr>
                                <td><label>Data polisy:</label></td>
                                <Td>
                                    @if($insurance->insurance_date == '0000-00-00')
                                        ---
                                    @else
                                        {{ $insurance->insurance_date }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Polisa od:</label></td>
                                <Td
                                        @if($insurance->date_from == '0000-00-00')
                                        class="red"
                                        @endif
                                >
                                    {{ $insurance->date_from }}
                                </td>
                            </tr>
                            <tr>
                                <td><label>Polisa do:</label></td>
                                <Td
                                        @if($insurance->date_to == '0000-00-00')
                                        class="red"
                                        @endif
                                >
                                    {{ $insurance->date_to }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <table class="table table-hover table-condensed">
                            <tr>
                                <td><label>Ubezpieczyciel:</label></td>
                                <Td>
                                    @if($insurance->insuranceCompany)
                                        {{ $insurance->insuranceCompany->name }}
                                    @else
                                        ---
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Składka leasingobiorcy:</label></td>
                                <Td>{{ number_format($insurance->contribution_lessor,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
                            </tr>
                            <tr>
                                <td><label>Zwrot składki:</label></td>
                                <Td>
                                    @if($insurance->if_refund_contribution == '0')
                                        NIE
                                    @else
                                        {{ number_format($insurance->refund,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label>Typ płatności:</label></td>
                                <Td>
                                    @if($insurance->leasingAgreementPaymentWay)
                                        {{ $insurance->leasingAgreementPaymentWay->name }}
                                    @else
                                        ---
                                    @endif
                                </td>
                            </tr>
                            @if($insurance->leasing_agreement_payment_ways_id == 2 && $insurance->installments)
                                <tr>
                                    <td><label>Liczba rat:</label></td>
                                    <Td>
                                        {{ $insurance->installments->installments }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><label>Wysokość prowizji:</label></td>
                                <td>{{ number_format($insurance->commission_value,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
                            </tr>
                            <tr>
                                <td><label>Data prowizji:</label></td>
                                <td>{{ $insurance->commission_date }}</td>
                            </tr>
                            <tr>
                                <td><label>Zwrot prowizji:</label></td>
                                <td>{{ number_format($insurance->commission_refund_value,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-12">
                        <h4 class="inline-header" style="margin-top: 0px;"><span>Zakres ubezpieczenia:</span></h4>
                    </div>
                    @if($insurance->coverages->count() > 0)
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <table class="panel-body table table-condensed table-hover">
                                    @foreach($insurance->coverages as $coverage)
                                        <tr class="vertical-middle">
                                            <Td>
                                                <label class="control-label marg-right">{{ $coverage->type->name }}:</label>
                                                <i class="fa fa-check"></i>
                                            </Td>
                                            <td>
                                                @if($coverage->amount)
                                                    <label class="control-label marg-right">Suma ubezpieczenia:</label>
                                                    {{ $coverage->amount }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($coverage->currency_id)
                                                    <label class="control-label marg-right">Waluta:</label>
                                                    {{ Config::get('definition.currencies')[$coverage->currency_id] }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($coverage->net_gross)
                                                    <label class="control-label marg-right">Netto/brutto:</label>
                                                    {{ Config::get('definition.net_gross')[$coverage->net_gross] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-12">
                        <h4 class="inline-header" style="margin-top: 0px;"><span>Terminy płatności:</span></h4>
                    </div>
                    @foreach($insurance->payments as $k => $payment)
                        <div class="col-sm-12 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h4 class="inline-header" style="margin-top: 0px;"><span>Rata {{ ++$k }}:</span></h4>
                                    <form class="form-horizontal form-info">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Termin płatności:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-static">{{ $payment->deadline }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Kwota raty:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-static">{{ $payment->amount }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group col-sm-10 col-sm-offset-1">
                                                @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                                    <input @if($payment->date_of_payment) disabled @endif id="payment_{{ $payment->id }}" type="text" class="form-control datepicker input-sm" value="{{ $payment->date_of_payment }}" placeholder="Termin napłynięcia płatnośći">
                                                    <span class="input-group-btn">
                                                        <div class="btn-group" data-toggle="buttons">
                                                            <label class="btn btn-confirmation btn-sm  @if($payment->date_of_payment) active disabled @endif" id="label_check_alert_repurchase">
                                                                <input  type="checkbox" class="payment-confirmation" data-payment-id="{{ $payment->id }}" hrf="{{ URL::to('insurances/info-insurances/set-payment') }}"> <i class="fa fa-check "></i>
                                                            </label>
                                                        </div>
                                                    </span>
                                                @else
                                                    <p class="form-control-static">
                                                        {{ $payment->date_of_payment }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="panel panel-primary small">
                <div class="panel-heading" role="tab" id="collapsePanelGroupHeading{{ $insurance->id }}">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapsePanelGroup{{ $insurance->id }}" aria-expanded="false" aria-controls="collapsePanelGroup{{ $insurance->id }}">
                            Polisy wznawiane
                            <i class="fa fa-sort-desc pull-right"></i>
                        </a>
                    </h4>
                </div>
                <div id="collapsePanelGroup{{ $insurance->id }}" class="panel-collapse panel-body collapse" role="tabpanel" aria-labelledby="collapsePanelGroupHeading{{ $insurance->id }}" aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">
                    @while($insurance->resumedInsurance)
                        <?php $insurance = $insurance->resumedInsurance;?>
                        <div class="panel panel-default">
                            <div class="panel-heading text-right">{{ substr($insurance->created_at,0,-3) }}</div>
                            <div class="panel-body">
                                <div class="col-sm-12 col-md-6">
                                    <table class="table table-hover table-condensed">
                                        <tr>
                                            <td><label>Nr polisy:</label></td>
                                            <Td>{{ $insurance->insurance_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Nr zgłoszenia:</label></td>
                                            <Td>{{ $insurance->notification_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Liczba miesięcy:</label></td>
                                            <Td>{{ $insurance->months }}</td>
                                        </tr>

                                        <tr>
                                            <td><label>Data polisy:</label></td>
                                            <Td>
                                                @if($insurance->insurance_date == '0000-00-00')
                                                    ---
                                                @else
                                                    {{ $insurance->insurance_date }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Polisa od:</label></td>
                                            <Td
                                                    @if($insurance->date_from == '0000-00-00')
                                                    class="red"
                                                    @endif
                                            >
                                                {{ $insurance->date_from }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Polisa do:</label></td>
                                            <Td
                                                    @if($insurance->date_to == '0000-00-00')
                                                    class="red"
                                                    @endif
                                            >
                                                {{ $insurance->date_to }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <table class="table table-hover table-condensed">
                                        <tr>
                                            <td><label>Ubezpieczyciel:</label></td>
                                            <Td>
                                                @if($insurance->insuranceCompany)
                                                    {{ $insurance->insuranceCompany->name }}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Składka leasingobiorcy:</label></td>
                                            <Td>{{ number_format($insurance->contribution_lessor,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Zwrot składki:</label></td>
                                            <Td>
                                                @if($insurance->if_refund_contribution == '0')
                                                    NIE
                                                @else
                                                    {{ number_format($insurance->refund,2,"."," ") }} zł
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Typ płatności:</label></td>
                                            <Td>
                                                @if($insurance->leasingAgreementPaymentWay)
                                                    {{ $insurance->leasingAgreementPaymentWay->name }}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                        </tr>
                                        @if($insurance->leasing_agreement_payment_ways_id == 2 && $insurance->installments)
                                            <tr>
                                                <td><label>Liczba rat:</label></td>
                                                <Td>
                                                    {{ $insurance->installments->installments }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><label>Wysokość prowizji:</label></td>
                                            <td>{{ number_format($insurance->commission_value,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Data prowizji:</label></td>
                                            <td>{{ $insurance->commission_date }}</td>
                                        </tr>
                                        <tr>
                                            <td><label>Zwrot prowizji:</label></td>
                                            <td>{{ number_format($insurance->commission_refund_value,2,"."," ") }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <h4 class="inline-header" style="margin-top: 0px;"><span>Zakres ubezpieczenia:</span></h4>
                                </div>
                                @if($insurance->coverages->count() > 0)
                                    <div class="col-sm-12">
                                        <div class="panel panel-default">
                                            <table class="panel-body table table-condensed table-hover">
                                                @foreach($insurance->coverages as $coverage)
                                                    <tr class="vertical-middle">
                                                        <Td>
                                                            <label class="control-label marg-right">{{ $coverage->type->name }}:</label>
                                                            <i class="fa fa-check"></i>
                                                        </Td>
                                                        <td>
                                                            @if($coverage->amount)
                                                                <label class="control-label marg-right">Suma ubezpieczenia:</label>
                                                                {{ $coverage->amount }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($coverage->currency_id)
                                                                <label class="control-label marg-right">Waluta:</label>
                                                                {{ Config::get('definition.currencies')[$coverage->currency_id] }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($coverage->net_gross)
                                                                <label class="control-label marg-right">Netto/brutto:</label>
                                                                {{ Config::get('definition.net_gross')[$coverage->net_gross] }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-12">
                                    <h4 class="inline-header" style="margin-top: 0px;"><span>Terminy płatności:</span></h4>
                                </div>
                                @foreach($insurance->payments as $k => $payment)
                                    <div class="col-sm-12 col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <h4 class="inline-header" style="margin-top: 0px;"><span>Rata {{ ++$k }}:</span></h4>
                                                <form class="form-horizontal form-info">
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">Termin płatności:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-static">{{ $payment->deadline }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-4 control-label">Kwota raty:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-static">{{ $payment->amount }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="input-group col-sm-10 col-sm-offset-1">
                                                            @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                                                <input @if($payment->date_of_payment) disabled @endif id="payment_{{ $payment->id }}" type="text" class="form-control datepicker input-sm" value="{{ $payment->date_of_payment }}" placeholder="Termin napłynięcia płatnośći">
                                                                <span class="input-group-btn">
                                                                    <div class="btn-group" data-toggle="buttons">
                                                                        <label class="btn btn-confirmation btn-sm  @if($payment->date_of_payment) active disabled @endif" id="label_check_alert_repurchase">
                                                                            <input  type="checkbox" class="payment-confirmation" data-payment-id="{{ $payment->id }}" hrf="{{ URL::to('insurances/info-insurances/set-payment') }}"> <i class="fa fa-check "></i>
                                                                        </label>
                                                                    </div>
                                                                </span>
                                                            @else
                                                                <p class="form-control-static">
                                                                    {{ $payment->date_of_payment }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @endwhile
                    </div>
                </div>
            </div>
        </div>
@endforeach
