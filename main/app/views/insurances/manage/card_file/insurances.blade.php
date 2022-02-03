<div class="tab-pane fade in" id="insurances-data">
    <div class="row">
        @foreach($insurances = $agreement->insurances()->with('insuranceType', 'insuranceCompany', 'leasingAgreementPaymentWay')->orderBy('id','desc')->get() as $k => $insurance)
            <div class="col-sm-12 col-md-8 col-md-offset-2 item-m">
                <div class="panel panel-default
                    @if($insurance->detectProblem())
                        panel-danger
                    @endif
                    small">
                    <div class="panel-heading overflow ">
                        <h4 class="panel-title">{{$insurance->id}}
                            {{ $insurance->insurance_number }}
                            
                            @if(in_array($insurance->insurance_company_id, [3, 38, 107, 320]) && Auth::user()->can('kartoteka_polisy#certyfikat') )
                                <div class="btn-group" role="group">
                                    <span target="{{ URL::to('insurances/manage/generate-hestia-certificate', [$insurance->id]) }}" class="btn btn-info btn-xs modal-open" data-toggle="modal" data-target="#modal" style="color: white;" >
                                        <i class="fa fa-file-pdf-o fa-fw"></i> certyfikat
                                    </span>
                                    <span target="{{ URL::to('insurances/manage/generate-hestia-certificate-no-client', [$insurance->id]) }}" class="btn btn-default btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-file-pdf-o fa-fw"></i> certyfikat bez Korzystającego
                                    </span>
                                </div>
                            @endif

                            @if($insurance->if_foreign_policy == 1)
                                <span class="label label-warning">Polisa obca</span>
                            @endif

                            @if($insurance->if_cession)
                                <span class="label label-info marg-left">cesja</span>
                            @endif

                            @if(is_null($agreement->archive) && $insurance == reset($insurances)[0])
                                @if($k == 0)
                                    @if($insurance->if_cession && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                    <span class="btn btn-warning btn-xs marg-left pull-right modal-open" data-toggle="modal" data-target="#modal"
                                          target="{{ URL::to('insurances/info-dialog/rollback-cession', [$agreement->id]) }}" >
                                        <span class="fa fa-undo"></span> wycofaj cesję
                                    </span>
                                    @endif
                                @endif

                                @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                    <span class="btn btn-warning btn-xs marg-left pull-right modal-open" data-toggle="modal" data-target="#modal"
                                          target="{{ URL::to('insurances/info-dialog/rollback-insurance', [$insurance->id]) }}" >
                                            <span class="fa fa-undo"></span> wycofaj polisę
                                    </span>

                                    <a class="btn btn-primary btn-xs marg-left pull-right" style="color: white;" href="{{ URL::to('insurances/info-insurances/cession', [$agreement->id]) }}">
                                        <span class="fa fa-random"></span> wykonaj cesję
                                    </a>

                                    <a href="{{ URL::to('insurances/info-insurances/edit', [$insurance->id]) }}" title="edytuj" class="pull-right tips" style="font-size: 17px;cursor: pointer;">
                                        <i class="fa fa-pencil-square-o "  ></i>
                                    </a>
                                @endif

                            @elseif(is_null($agreement->archive) && Auth::user()->can('kartoteka_polisy#zarzadzaj') )
                                <span class="btn btn-warning btn-xs marg-left pull-right modal-open" data-toggle="modal" data-target="#modal"
                                      target="{{ URL::to('insurances/info-dialog/rollback-insurance', [$insurance->id]) }}" >
                                        <span class="fa fa-undo"></span> wycofaj polisę
                                </span>
                            @endif
                            <small class="pull-right marg-right">{{ substr($insurance->created_at,0,-3) }}</small>

                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-12 col-sm-6">
                            <table class="table table-hover table-condensed">
                                <tr>
                                    <td><label>Nr polisy:</label></td>
                                    <Td>{{ $insurance->insurance_number }}</td>
                                </tr>
                                <tr class="{{ $insurance->mark() }}">
                                    <td><label>Nr zgłoszenia:</label></td>
                                    <Td>{{ $insurance->notification_number }}</td>
                                </tr>
                                <tr>
                                    <td><label>Typ polisy:</label></td>
                                    <Td>
                                        @if($insurance->insuranceType)
                                            {{ $insurance->insuranceType->name }}
                                        @else
                                            ---
                                        @endif
                                    </td>
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
                                <tr>
                                    <td><label>Typ płatności:</label></td>
                                    <Td>
                                        @if($insurance->leasingAgreementPaymentWay)
                                            @if($insurance->if_continuation)
                                                {{ $insurance->leasingAgreementPaymentWay->name }}
                                            @elseif($insurance->leasingAgreementPaymentWay->id == 2 && $agreement->if_reportable == 0)
                                                Wielolatka
                                            @else
                                                {{ $insurance->leasingAgreementPaymentWay->name }}
                                            @endif
                                        @else
                                            ---
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-12 col-sm-6">
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
                                @if($insurance->if_foreign_policy == 0)
                                <tr>
                                    <td><label>Składka leasingodawcy:</label></td>
                                    <Td>{{ number_format($insurance->contribution,2,"."," ") }} zł</td>
                                </tr>
                                <tr>
                                    <td><label>Prowizja:</label></td>
                                    <Td>{{ number_format($insurance->commission,2,"."," ") }} %</td>
                                </tr>
                                <tr>
                                    <td><label>Wartość prowizji:</label></td>
                                    <Td>{{ number_format($insurance->contribution_commission,2,"."," ") }} zł</td>
                                </tr>
                                <tr>
                                    <td><label>Stawka leasingodawcy:</label></td>
                                    <Td>{{ number_format($insurance->rate,2,"."," ") }} %</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><label>Stawka leasingobiorcy:</label></td>
                                    <Td>{{ number_format($insurance->rate_lessor,2,"."," ") }} %</td>
                                </tr>
                                <tr>
                                    <td><label>Składka leasingobiorcy:</label></td>
                                    <Td>{{ number_format($insurance->contribution_lessor,2,"."," ") }} zł</td>
                                </tr>
                                @if($insurance->last_year_lessor_contribution != '0.00' && ! is_null($insurance->last_year_lessor_contribution))
                                    <tr>
                                        <td><label>Składka leasingobiorcy w ostatnim roku:</label></td>
                                        <Td>{{ number_format($insurance->last_year_lessor_contribution,2,"."," ") }} zł</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><label>Stawka vbl:</label></td>
                                    <Td>{{ number_format($insurance->rate_vbl,2,"."," ") }} %</td>
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
                                    <td><label>Czy kontynuacja:</label></td>
                                    <Td>
                                        @if($insurance->if_continuation == '0')
                                            NIE
                                        @else
                                            TAK
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>Decyzja obciążenia:</label></td>
                                    <Td>
                                        @if($insurance->if_load_decision == '0')
                                            NIE
                                        @else
                                            TAK
                                        @endif
                                    </td>
                                </tr>
                                @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                    <tr class="form-check">
                                        <td><label>Składka opłacona:</label></td>
                                        <Td>
                                            <input type="checkbox"
                                                   {{ $insurance->if_contribution_paid ? 'checked="checked" ' : '' }} class="btn btn-warning btn-sm btn-block modal-open"
                                                   target="{{ URL::to('insurances/info-insurances/mark-contribution-as-paid', [$insurance->id]) }}"
                                                   data-toggle="modal" data-target="#modal">
                                            </input>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
