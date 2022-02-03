@if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody'))
    <div class="tab-pane fade in" id="settlements">

{{--        <button type="button" class="btn btn-xs btn-warning modal-open-xl" target="{{URL::route('injuries-getEditInvoice)', array($v->id))}}"--}}
    <div class="row" style="display: inline-block; margin-left:0px">
        <div @if(!$injury->injuryCessionAmount()->first()) style="overflow: auto; display: inline-block; padding:0px" data-toggle="tooltip" data-placement="right" title="Wymagane uzupełnienie kwot do cesji" @endif>
        <button type="button" class="btn btn-sm btn-primary modal-open" target="{{ URL::route('injuries-generate-docs-info', array($injury->id, 91)) }}"
                data-toggle="modal" data-target="#modal" {{ $injury->injuryCessionAmount()->first() ? '' : 'disabled' }}><i class="fa fa-file-text-o"></i> wystaw cesję</button>
        </div>
        <div style="overflow: auto; display: inline-block;">
            <button class="btn btn-primary btn-sm modal-open generate_doc"
                   id="doc_112"
                   target="{{ URL::route('injuries-generate-docs-info', array($injury->id,
                   112, 0)) }}"
                   data-toggle="modal" data-target="#modal">
                    <i class="fa fa-file-text-o"></i><span> DYSPOZYCJA ZWROTU IGL </span>
            </button>
        </div>
        <div style="overflow: auto; display: inline-block; padding:0px" @if(!count($injury->forwardedInvoices) > 0) data-toggle="tooltip" data-placement="right" title="Brak przekazanej faktury" @endif>
                <button class="btn btn-primary btn-sm modal-open-lg generate_doc"
                    id="doc_113"
                    target="{{ URL::route('injuries-generate-docs-info', array($injury->id,
                    113, 0)) }}"
                    {{ count($injury->forwardedInvoices) > 0 ? '' : 'disabled' }}
                    data-toggle="modal" data-target="#modal-lg">
                        <i class="fa fa-file-text-o"></i><span> Strona tytułowa – dokumenty do rozliczenia </span>
                </button>
        </div>
    </div>

        <div class="panel-group" id="accordionSettlements" role="tablist" aria-multiselectable="true">
            <div class="panel panel-primary">
                <div class="panel-heading" role="tab" id="headingInvoices">
                    <h3 class="panel-title pointer" data-toggle="collapse" {{--data-parent="#accordionSettlements"--}} href="#collapseInvoices"  aria-controls="collapseInvoices">
                        Faktury <span class="badge counted-agreements">{{ count($invoices) }}</span> <i class="fa fa-arrows-v pull-right"></i>
                    </h3>
                </div>
                <div id="collapseInvoices" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingInvoices">
                    <table class="table table-hover" >
                        <tr>
                            <th>lp.</th>
                            <th></th>
                            <th>status</th>
                            <th>typ</th>
                            <th>rodzaj usługi</th>
                            <th>odbiorca</th>
                            <th>nr faktury</th>
                            <th>data wystawienia</th>
                            <th>termin płatności</th>
                            <th>kwota netto</th>
                            <th>kwota vat</th>
                            <th>wartość brutto</th>
                            @if($injury->branch_id > 0 && $injury->branch->company->groups->count() > 0)
                                <th>do prowizji</th>@endif
                            <th>przekazano</th>
                            {{--<th>wartość prowizji</th>--}}
                            <th></th>
                            <th></th>
                            <Th></Th>
                        </tr>
                        @foreach($invoices as $k => $v)
                            <tr>
                                <td width="10px">{{++$k}}.</td>
                                <td width="50px">
                              <a href="{{ URL::route('injuries-downloadDoc', array($v->injury_files_id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                            </td>
                            <td>
                                @if($v->status)
                                    <div class="label label-default" >
                                        {{ $v->status->name }}
                                    </div>
                                @endif

                                @if($v->note)
                                    <div class="label label-primary" data-toggle="popover" title="Nr notatki {{ $v->note->nrnotatki }}" data-content="<label>data wysłania:</label> {{ $v->note->created_at->format('Y-m-d H:i') }}<br/><label>treść:</label> {{ $v->note->temat }}">
                                        przekazana do SAP
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($v->injury_files)
                                  @if($v->injury_files->category == 4)
                                    {{Config::get('definition.fileCategory.4')}}<br>
                                    @if($v->parent_id != 0)
                                    <i>do F nr:{{$v->parent->invoice_nr}} </i>
                                    @endif
                                  @else
                                    {{Config::get('definition.fileCategory.3')}}
                                  @endif
                                @endif
                            </td>
                            <td>
                                @if($v->serviceType)
                                    {{ $v->serviceType->name }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                            @if($v->invoicereceives_id != 0)
                                {{$v->invoicereceive->name}}
                            @else
                                ---
                            @endif
                            </td>
                            <td>{{ ($v->invoice_nr == '') ? '---' : $v->invoice_nr }}</td>
                            <td>{{ (!$v->invoice_date || $v->invoice_date == '0000-00-00') ? '---' : $v->invoice_date }}</td>
                            <td>
                              @if(!$v->payment_date || $v->payment_date == '0000-00-00')
                                ---
                              @else
                               {{ $v->payment_date }}<br>
                               <i>
                               <?php
                                $dayDiff = \Carbon\Carbon::createFromFormat('Y-m-d',$v->invoice_date )->diffInDays(\Carbon\Carbon::createFromFormat('Y-m-d', $v->payment_date));
                                echo $dayDiff.' dni';
                              ?>
                               </i>
                              @endif
                            </td>
                            <td>
                                {{ ($v->netto == 0) ? '---' : money_format("%.2n", $v->netto).' zł' }}
                                @if($v->injury_files && $v->injury_files->category == 4 && isset($invoicesCorrection[$v->id]))
                                    <p class="text-danger">
                                        <small>{{ money_format("%.2n",$invoicesCorrection[$v->id]['netto']).' zł' }}</small>
                                    </p>
                                @endif
                            </td>
                            <td>
                                {{ ($v->vat == 0) ? '---' : money_format("%.2n",$v->vat).' zł' }}
                                @if($v->injury_files && $v->injury_files->category == 4 && isset($invoicesCorrection[$v->id]))
                                    <p class="text-danger">
                                        <small>{{ money_format("%.2n",$invoicesCorrection[$v->id]['vat']).' zł' }}</small>
                                    </p>
                                @endif
                            </td>
                            <td>
                                {{ (($v->netto+$v->vat) == 0) ? '---' : money_format("%.2n",($v->netto+$v->vat)).' zł' }}
                                @if($v->injury_files && $v->injury_files->category == 4 && isset($invoicesCorrection[$v->id]))
                                    <p class="text-danger">
                                        <small>{{ money_format("%.2n",$invoicesCorrection[$v->id]['netto']+$invoicesCorrection[$v->id]['vat']).' zł' }}</small>
                                    </p>
                                @endif
                            </td>
                                @if($injury->branch_id > 0 && $injury->branch->company->groups->count() > 0)
                            <td>
                              @if($v->commission == 0)
                                <i class="fa fa-minus"></i>
                              @else
                                <i class="fa fa-check"></i> <br>
                                <i>podstawa {{ money_format("%.2n",$v->base_netto).' zł'}}</i>
                              @endif
                            </td>
                                @endif
                            <td>
                                @if($v->forward_date)
                                    {{ substr($v->forward_date, 0, -3) }}
                                @else
                                    <i class="fa fa-minus fa-fw"></i>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                    @if($v->injury_files && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$v->injury_files->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                                        <button type="button" class="btn btn-xs btn-warning modal-open-xl" target="{{ URL::route('injuries-getEditInvoice', array($v->id)) }}"  data-toggle="modal" data-target="#modal-xl"><i class="fa fa-pencil"></i> edytuj</button>
                                    @else
                                        <button type="button" class="btn btn-xs btn-warning modal-open-lg" target="{{ URL::route('injuries-getEditInvoice', array($v->id)) }}"  data-toggle="modal" data-target="#modal-lg"><i class="fa fa-pencil"></i> edytuj</button>
                                    @endif

                                    
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                    @if(! $v->status)
                                        <span class="btn btn-primary btn-xs modal-open" data-toggle="modal" id="invoice-forward-modal" data-target="#modal" target="{{ URL::to('injuries/invoice/forward', [$v->id]) }}">
                                            <i class="fa fa-fw fa-send-o"></i> przekaż
                                        </span>
                                    @elseif($v->status->id == 1 || $v->status->id == 3)
                                        <span class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('injuries/invoice/return', [$v->id]) }}">
                                            <i class="fa fa-fw fa-rotate-left"></i> zwróć
                                        </span>
                                    @elseif($v->status->id == 2)
                                        <span class="btn btn-warning btn-xs modal-open" data-toggle="modal" data-target="#modal" target="{{ URL::to('injuries/invoice/forward-again', [$v->id]) }}">
                                            <i class="fa fa-fw fa-send-o"></i> przekaż ponownie
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                    <button type="button" class="btn btn-xs btn-danger modal-open" target="{{ URL::route('injuries-getDeleteInvoice', array($v->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-trash"></i> usuń</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                        <tr>
                            <td colspan="8"></td>
                            <td style="text-align: right;">Suma:</td>
                            <td class="info">{{ money_format("%.2n", $invoicesSum['sum_net']).' zł' }}</td>
                            <td class="info">{{ money_format("%.2n", $invoicesSum['sum_vat']).' zł' }}</td>
                            <td class="info">{{ money_format("%.2n", $invoicesSum['sum_net']+$invoicesSum['sum_vat']).' zł' }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading pointer" role="tab" id="headingCompensations">
                    <h3 class="panel-title" data-toggle="collapse" {{--data-parent="#accordionSettlements"--}} href="#collapseCompensations"  aria-controls="collapseCompensations">
                        Odszkodowania <span class="badge counted-agreements">{{ count($injury->compensations) }}</span> <i class="fa fa-arrows-v pull-right"></i>
                    </h3>
                </div>
                <div id="collapseCompensations" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingCompensations">
                    <table class="table table-hover" >
                        <tr>
                            <th>lp.</th>
                            <th></th>
                            <th>data decyzji</th>
                            <th>rodzaj decyzji</th>
                            <th>odbiorca odszkodowania</th>
                            <th>kwota</th>
                            <th></th>
                            <th>uwagi</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <?php $sumCompensation = 0; ?>
                        @foreach($injury->compensations as $k => $compensation)
                            <tr>
                                <td width="10px">{{++$k}}.</td>
                                <td width="50px">
                                    @if($compensation->injury_files_id)
                                        <a href="{{ URL::route('injuries-downloadDoc', array($compensation->injury_files_id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                                    @endif
                                    @if($compensation->note)
                                        <div class="label label-primary" data-toggle="popover" title="Nr notatki {{ $compensation->note->nrnotatki }}" data-content="<label>data wysłania:</label> {{ $compensation->note->created_at->format('Y-m-d H:i') }}<br/><label>treść:</label> {{ $compensation->note->temat }}">
                                            przekazana do SAP
                                        </div>
                                    @endif
                                    @if($compensation->premium()->exists())
                                        <div class="label label-danger">
                                            dopłata w SAP
                                        </div>
                                    @endif
                                    @if(! is_null($compensation->mode))
                                        <span class="label label-info">{{ $compensation->mode_name }}</span>
                                    @endif
                                </td>
                                <td>{{ checkIfEmpty($compensation->date_decision)  }}</td>
                                <td>{{ checkIfEmpty('name', $compensation->decisionType()->get())  }}</td>
                                <td>{{ checkIfEmpty('name', $compensation->receive()->get())}}</td>
                                <Td>
                                    @if(!is_null($compensation->compensation))
                                        @if($compensation->injury_compensation_decision_type_id == 7)
                                            <?php  $compensation->compensation = abs($compensation->compensation) * -1; ?>
                                        @endif
                                        <?php $sumCompensation+=$compensation->compensation;?>
                                        {{ number_format(checkIfEmpty($compensation->compensation, null, 0), 2, ",", " ") }} zł

                                    @else
                                        {{ number_format(0, 2, ",", " ") }} zł
                                    @endif

                                </Td>
                                <Td>{{ checkIfEmpty(Config::get('definition.compensationsNetGross.'.$compensation->net_gross))}}</Td>
                                <Td>{{ checkIfEmpty($compensation->remarks) }}</Td>
                                <td>
                                    @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                        @if($compensation->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)) , ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                                            <button type="button" class="btn btn-xs btn-warning modal-open-xl" target="{{ URL::route('injuries-getEditCompensation', array($compensation->id)) }}"  data-toggle="modal" data-target="#modal-xl"><i class="fa fa-pencil"></i> edytuj</button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-warning modal-open" target="{{ URL::route('injuries-getEditCompensation', array($compensation->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i> edytuj</button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                        <button type="button" class="btn btn-xs btn-danger modal-open" target="{{ URL::route('injuries-getDeleteCompensation', array($compensation->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-trash"></i> usuń</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <Tr>
                            <td colspan="4"></td>
                            <td style="text-align: right;">Suma:</td>
                            <td class="info">{{ number_format($sumCompensation, 2, ",", " ") }} zł</td>
                            <Td colspan="4"></Td>
                        </Tr>
                    </table>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading pointer" role="tab" id="headingOrders">
                    <h3 class="panel-title" data-toggle="collapse" {{--data-parent="#accordionSettlements"--}} href="#collapseOrders"  aria-controls="collapseOrders">
                    Zlecenia płatności  <span class="badge counted-agreements">{{count($injury->forwardedInvoices)}}</span> <i class="fa fa-arrows-v pull-right"></i>
                    </h3>
                </div>
                <div id="collapseOrders" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOrders">
                    <table class="table table-hover" >
                        <tr>
                            <th>lp.</th>
                            <th>Numer faktury</th>
                            <th>data przekazania</th>
                            <th>data przekazania ponownie</th>
                            <th>Suma</th>
                            <th>Akcje</th>
                            <th></th>
                            <th></th>
                        </tr>
                        @foreach($injury->forwardedInvoices as $k => $invoice)
                            <?php $compensationsSum = 0;
                                foreach($invoice->compensations as $compensation) {
                                    if($compensation->injury_compensation_decision_type_id == 7) {
                                            $compensation->compensation = abs($compensation->compensation) * -1;
                                    }
                                    $compensationsSum += $compensation->compensation;
                                }
                                $compensationsSum = number_format(checkIfEmpty($compensationsSum, null, 0), 2, ",", " ");
                            ?>

                                <tr>
                                    <td width="10px">{{++$k}}.</td>
                                    <td>{{$invoice->invoice_nr}}</td>
                                    <td>{{$invoice->forward_date}}</td>
                                    <td>{{$invoice->forward_again_date}}</td>
                                    <td>
                                        {{$compensationsSum}} zł
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-xs modal-open"
                                            target="{{ URL::route('injuries-getGenerateVDeskTextView', array($invoice->id)) }}"
                                            data-backdrop="false"
                                            data-toggle="modal" data-target="#modal">
                                            <i class="fa fa-file-text-o"></i><span> Tekst do V-Desk </span>
                                        </button>
                                    </td>
                                </tr>
                                
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading pointer" role="tab" id="headingEstimates">
                    <h3 class="panel-title" data-toggle="collapse" {{--data-parent="#accordionSettlements"--}} href="#collapseEstimates"  aria-controls="collapseEstimates">
                        Kosztorysy <span class="badge counted-agreements">{{ count($injury->estimates) }}</span> <i class="fa fa-arrows-v pull-right"></i>
                    </h3>
                </div>
                <div id="collapseEstimates" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingEstimates">
                    <table class="table table-hover" >
                        <tr>
                            <th>lp.</th>
                            <th></th>
                            <th>data wprowadzenia</th>
                            <th>kwota netto</th>
                            <th>kwota brutto</th>
                            <th>uwzględnij w raporcie</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <?php $sumEstimateNet = 0; $sumEstimateGross=0;?>
                        @foreach($injury->estimates as $k => $estimate)
                            <tr>
                                <td width="10px">{{++$k}}.</td>
                                <td width="50px">
                                    @if($estimate->injury_file_id)
                                        <a href="{{ URL::route('injuries-downloadDoc', array($estimate->injury_file_id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                                    @endif
                                </td>
                                <td>{{ checkIfEmpty($estimate->created_at)  }}</td>
                                <Td>
                                    @if(!is_null($estimate->net))
                                        <?php $sumEstimateNet+=$estimate->net;?>
                                        {{ number_format(checkIfEmpty($estimate->net, null, 0), 2, ",", " ") }} zł
                                    @else
                                        {{ number_format(0, 2, ",", " ") }} zł
                                    @endif

                                </Td>
                                <Td>
                                    @if(!is_null($estimate->gross))
                                        <?php $sumEstimateGross+=$estimate->gross;?>
                                        {{ number_format(checkIfEmpty($estimate->gross, null, 0), 2, ",", " ") }} zł
                                    @else
                                        {{ number_format(0, 2, ",", " ") }} zł
                                    @endif

                                </Td>
                                <td>
                                  @if($estimate->report)
                                    <i class="fa fa-check"></i>
                                  @else
                                    <i class="fa fa-minus"></i>
                                  @endif
                                <td>
                                    @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                        @if($estimate->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$estimate->injury_file->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                                            <button type="button" class="btn btn-xs btn-warning modal-open-xl" target="{{ URL::route('injuries-getEditEstimate', array($estimate->id)) }}"  data-toggle="modal" data-target="#modal-xl"><i class="fa fa-pencil"></i> edytuj</button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-warning modal-open" target="{{ URL::route('injuries-getEditEstimate', array($estimate->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i> edytuj</button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                        <button type="button" class="btn btn-xs btn-danger modal-open" target="{{ URL::route('injuries-getDeleteEstimate', array($estimate->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-trash"></i> usuń</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <?php /*<Tr>
                            <td colspan="2"></td>
                            <td style="text-align: right;">Suma:</td>
                            <td class="info">{{ number_format($sumEstimateNet, 2, ",", " ") }} zł</td>
                            <td class="info">{{ number_format($sumEstimateGross, 2, ",", " ") }} zł</td>
                            <Td colspan="3"></Td>
                        </Tr>*/ ?>
                    </table>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading pointer" role="tab" id="headingEstimates">
                    <h3 class="panel-title" data-toggle="collapse" {{--data-parent="#accordionSettlements"--}} href="#collapseCession"  aria-controls="collapseCession">
                        Kwoty do cesji <i class="fa fa-arrows-v pull-right"></i>
                    </h3>
                </div>
                <div id="collapseCession" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingEstimates">
                    <table class="table table-hover" >
                        <tr>
                            <th>kwota wypłaconego odszkodowania</th>
                            <th></th>
                            <th>kwota z FV</th>
                            <th>różnica</th>
                            <th></th>
                        </tr>
                            <tr>
                                <Td>
                                    @if($cessionAmount && !is_null($cessionAmount->paid_amount))
                                        {{number_format($cessionAmount->paid_amount, 2, ",", " ")}} zł
                                        @else
                                        {{ number_format(0, 2, ",", " ") }} zł
                                    @endif
                                </Td>
                                <td>
                                    @if($cessionAmount && !is_null($cessionAmount->net_gross))
                                        {{checkIfEmpty(Config::get('definition.compensationsNetGross.'.$cessionAmount->net_gross))}}
                                    @else

                                    @endif
                                </td>
                                <Td>
                                    @if($cessionAmount && !is_null($cessionAmount->fv_amount))
                                        {{ number_format($cessionAmount->fv_amount, 2, ",", " ") }} zł
                                    @else
                                        {{ number_format(0, 2, ",", " ") }} zł
                                    @endif
                                </Td>
                                <td>
                                    @if($cessionAmount && !is_null($cessionAmount->fv_amount))
                                        {{ number_format($cessionAmount->fv_amount - $cessionAmount->paid_amount, 2, ",", " ") }} zł
                                    @else
                                        {{ number_format(0, 2, ",", " ") }} zł
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->can('kartoteka_szkody#rozliczenia_szkody#zarzadzaj'))
                                        @if($cessionAmount)
                                            <button type="button" class="btn btn-xs btn-warning modal-open-lg" target="{{ URL::route('injuries-editCessionAmounts', array($cessionAmount->id))}}"  data-toggle="modal" data-target="#modal-lg"><i class="fa fa-pencil"></i> edytuj</button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-primary modal-open-lg" target="{{ URL::route('injuries-createCessionAmounts', array($injury->id)) }}"  data-toggle="modal" data-target="#modal-lg"><i class="fa fa-pencil"></i> przypisz kwoty</button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        <?php /*<Tr>
                            <td colspan="2"></td>
                            <td style="text-align: right;">Suma:</td>
                            <td class="info">{{ number_format($sumEstimateNet, 2, ",", " ") }} zł</td>
                            <td class="info">{{ number_format($sumEstimateGross, 2, ",", " ") }} zł</td>
                            <Td colspan="3"></Td>
                        </Tr>*/ ?>
                    </table>
                </div>
            </div>

        </div>
      </div>
@endif
