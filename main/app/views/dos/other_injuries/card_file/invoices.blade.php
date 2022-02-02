<div class="tab-pane fade in" id="settlements">
    <div class="panel-group" id="accordionSettlements" role="tablist" aria-multiselectable="true">
        <div class="panel panel-primary">
            <div class="panel-heading" role="tab" id="headingInvoices">
                <h3 class="panel-title pointer" data-toggle="collapse" data-parent="#accordionSettlements" href="#collapseInvoices"  aria-controls="collapseInvoices">
                    Faktury <span class="badge counted-agreements">{{ count($invoices) }}</span> <i class="fa fa-arrows-v pull-right"></i>
                </h3>
            </div>
            <div id="collapseInvoices" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingInvoices">
                <table class="table table-hover" >
                  <tr>
                    <th>lp.</th>
                    <th></th>
                    <th>typ</th>
                    <th>odbiorca</th>
                    <th>nr faktury</th>
                    <th>data wystawienia</th>
                    <th>termin płatności</th>
                    <th>kwota netto</th>
                    <th>kwota vat</th>
                    <th>wartość brutto</th>
                    <th>do prowizji</th>
                    <th></th>
                  </tr>
                @foreach($invoices as $k => $v)
                  <tr>
                    <td width="10px">{{++$k}}.</td>
                    <td width="50px">
                      <a href="{{ URL::route('dos.other.injuries.downloadDoc', array($v->injury_files_id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
                    </td>
                    <td>
                      @if($v->injury_files->category == 4)
                        {{Config::get('definition.fileCategory.4')}}<br>
                        @if($v->parent_id != 0)
                        <i>do F nr:{{$v->parent->invoice_nr}} </i>
                        @endif
                      @else
                        {{Config::get('definition.fileCategory.3')}}
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
                    <td>{{ ($v->invoice_date == '0000-00-00') ? '---' : $v->invoice_date }}</td>
                    <td>
                      @if($v->payment_date == '0000-00-00')
                        ---
                      @else
                       {{ $v->payment_date }}<br>
                       <i>
                       <?php
                        $invoice_date = strtotime($v->invoice_date);
                        $expireDay = strtotime($v->payment_date);
                        $timeToPay = ($expireDay - $invoice_date)/ 86400;
                        echo floor($timeToPay).' dni';
                      ?>
                       </i>
                      @endif
                    </td>
                    <td>{{ ($v->netto == 0) ? '---' : money_format("%.2n", $v->netto).' zł' }}</td>
                    <td>{{ ($v->vat == 0) ? '---' : money_format("%.2n",$v->vat).' zł' }}</td>
                    <td>{{ (($v->netto+$v->vat) == 0) ? '---' : money_format("%.2n",($v->netto+$v->vat)).' zł' }}</td>
                    <td>
                      @if($v->commission == 0)
                        <i class="fa fa-minus"></i>
                      @else
                        <i class="fa fa-check"></i> <br>
                        <i>podstawa {{ money_format("%.2n",$v->base_netto).' zł'}}</i>
                      @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('zlecenia#zarzadzaj'))
                            <button type="button" class="btn btn-xs btn-warning modal-open" target="{{ URL::route('dos.other.injuries.getEditInvoice', array($v->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i> edytuj</button>
                        @endif
                    </td>
                  </tr>
                @endforeach
                </table>
            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading pointer" role="tab" id="headingCompensations">
                <h3 class="panel-title" data-toggle="collapse" data-parent="#accordionSettlements" href="#collapseCompensations"  aria-controls="collapseCompensations">
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
                                    <a href="{{ URL::route('dos.other.injuries.downloadDoc', array($compensation->injury_files_id)) }}" target="_blank" class="fa fa-floppy-o blue pointer md-ico"></a>
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
                                @if(Auth::user()->can('zlecenia#zarzadzaj'))
                                    @if($compensation->injury_file && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$compensation->injury_file->file)) , ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                                        <button type="button" class="btn btn-xs btn-warning modal-open-xl" target="{{ URL::route('dos.other.injuries.get', array('getEditCompensation', $compensation->id)) }}"  data-toggle="modal" data-target="#modal-xl"><i class="fa fa-pencil"></i> edytuj</button>
                                    @else
                                        <button type="button" class="btn btn-xs btn-warning modal-open" target="{{  URL::route('dos.other.injuries.get', array('getEditCompensation', $compensation->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-pencil"></i> edytuj</button>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('zlecenia#zarzadzaj'))
                                    <button type="button" class="btn btn-xs btn-danger modal-open" target="{{ URL::route('dos.other.injuries.get', array('getDeleteCompensation', $compensation->id)) }}"  data-toggle="modal" data-target="#modal"><i class="fa fa-trash"></i> usuń</button>
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
    </div>
</div>
