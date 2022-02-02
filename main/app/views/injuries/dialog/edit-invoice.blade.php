<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Edycja faktury</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <div class="row">
      <div class="@if($invoice->injury_files && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp'])) col-sm-6 @else col-sm-12 @endif">
          @if($invoice->injury->branch && ($invoice->injury->branch->company->groups->contains(1) || $invoice->injury->branch->company->groups->contains(5)))
          <div class="panel panel-info">
              <div class="panel-body">
                  <dl class="dl-horizontal marg-btm @if($invoice->injury->branch->company->is_active_vat == 1) bg-success @else bg-danger @endif">
                    <h5 style="color: grey; margin: 10px">Warsztat przypisany do szkody</h5>
                      <dt>Skrócona nazwa:</dt>
                      <dd>{{ $invoice->injury->branch->short_name }}</dd>
                      <dt>NIP:</dt>
                      <dd>{{ $invoice->injury->branch->company->nip }}</dd>
                      <dt>Adres:</dt>
                      <dd>{{ $invoice->injury->branch->address }}</dd>
                      <dt>Status VAT:</dt>
                      <dd>{{ $invoice->injury->branch->company->companyVatCheck->status }}</dd>
                      <dt>Data sprawdzenia:</dt>
                      <dd>{{ $invoice->injury->branch->company->companyVatCheck->created_at->format('Y-m-d H:i') }}</dd>
                  </dl>
                  @if($invoice->initialCompanyVatCheck)
                      <hr class="">
                      <dl class="dl-horizontal marg-btm @if($invoice->initialCompanyVatCheck->status_code == 'C') bg-success @else bg-danger @endif">
                        <h5 style="color: grey; margin: 10px">Pierwotnie przypisany serwis</h5>
                        <dl class="dl-horizontal marg-btm @if($invoice->initialCompanyVatCheck->company->is_active_vat == 1) bg-success @else bg-danger @endif">
                            <dt>Skrócona nazwa:</dt>
                            <dd>{{ $invoice->initialCompanyVatCheck->company->name }}</dd>
                            <dt>NIP:</dt>
                            <dd>{{ $invoice->initialCompanyVatCheck->company->nip }}</dd>
                            <dt>Adres:</dt>
                            <dd>{{ $invoice->initialCompanyVatCheck->company->address }}</dd>
                        </dl>

                          <dt>Status VAT podczas <br>dodawania faktury:</dt>
                          <dd>{{ $invoice->initialCompanyVatCheck->status }}</dd>
                          <dt>Data sprawdzenia:</dt>
                          <dd>{{ $invoice->initialCompanyVatCheck->created_at->format('Y-m-d H:i') }}</dd>
                      </dl>
                  @endif
                  @if($invoice->companyVatCheck)
                      <hr class="">
                      <dl class="dl-horizontal marg-btm @if($invoice->companyVatCheck->status_code == 'C') bg-success @else bg-danger @endif">
                        <h5 style="color: grey; margin: 10px">Przypisany serwis wg. faktury</h5>
                        <dl class="dl-horizontal marg-btm @if($invoice->companyVatCheck->company->is_active_vat == 1) bg-success @else bg-danger @endif">
                            <dt>Skrócona nazwa:</dt>
                            <dd>{{ $invoice->companyVatCheck->company->name }}</dd>
                            <dt>NIP:</dt>
                            <dd>{{ $invoice->companyVatCheck->company->nip }}</dd>
                            <dt>Adres:</dt>
                            <dd>{{ $invoice->companyVatCheck->company->address }}</dd>
                        </dl>


                          <dt>Status VAT podczas <br>przekazania faktury:</dt>
                          <dd>{{ $invoice->companyVatCheck->status }}</dd>
                          <dt>Data sprawdzenia:</dt>
                          <dd>{{ $invoice->companyVatCheck->created_at->format('Y-m-d H:i') }}</dd>
                          <dt>Data przekazania:</dt>
                          <dd>{{ substr($invoice->forward_date, 0, -3) }}</dd>
                      </dl>
                  @endif
              </div>
          </div>
          @endif
              <form action="{{ URL::route('injuries-setInvoice', array($id)) }}" method="post"  id="dialog-injury-form">
                  {{Form::token()}}
                  <div class="form-group">
                      @if($invoice->injury_files && $invoice->injury_files->category == 4)
                          <div class="row">
                              <div class="col-sm-12 marg-btm">
                                  <label >Faktura korygowana:</label>
                                  <select name="parent_id" class="form-control">
                                      <option value="0">wybierz</option>
                                      @foreach($invoices as $k => $v)
                                          <option value="{{$v['id']}}"
                                                  @if($v['id'] == $invoice->parent_id)
                                                  selected
                                                  @endif
                                                  data-netto="{{ $v['netto'] }}"
                                                  data-vat="{{ $v['vat'] }}"
                                          >
                                              {{$v['invoice_nr']}}
                                              - {{ $v['netto'] }} zł netto
                                              @if($v['category'] == 4)
                                                  (korekta do {{ $v['parent'] }} )
                                              @endif
                                          </option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      @endif
                      <div class="row">
                          <div class="col-sm-12 marg-btm">
                              <label >Odbiorca faktury:</label>
                              <select name="invoicereceives_id" class="form-control">
                                  @foreach(Invoicereceives::get() as $k => $v)
                                      <option value="{{$v->id}}"
                                              @if($v->id == $invoice->invoicereceives_id)
                                              selected
                                              @endif
                                      >
                                          {{$v->name}}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-12 marg-btm">
                              <label>Rodzaj usługi</label>
                              {{ Form::select('injury_invoice_service_type_id', $serviceTypes, $invoice->injury_invoice_service_type_id, ['class' => 'form-control']) }}
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-12 marg-btm">
                              <label >Nr faktury:</label>
                              {{ Form::text('invoice_nr', $invoice->invoice_nr, array('class' => 'form-control  ',  'placeholder' => 'nr faktury')) }}
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-12 marg-btm">
                              <label >Data wystawienia:</label>
                              {{ Form::text('invoice_date', (!$invoice->invoice_date || $invoice->invoice_date == '0000-00-00') ? '' : $invoice->invoice_date , array('class' => 'form-control  ',  'placeholder' => 'data wystawienia')) }}
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-12 marg-btm">
                              <div class="clearfix">
                                  <label >Termin płatności:</label>
                              </div>
                              <div class="col-sm-6">
                                  {{ Form::text('payment_date', (!$invoice->payment_date || $invoice->payment_date == '0000-00-00') ? '' : $invoice->payment_date , array('class' => 'form-control  tips',  'placeholder' => 'termin płatności - data', 'title' => 'termin płatności - data', (!$invoice->invoice_date  || $invoice->invoice_date == '0000-00-00')?'disabled':'')) }}
                              </div>
                              <div class="col-sm-6">
                                  <?php
                                  if($invoice->payment_date && $invoice->payment_date != '0000-00-00')
                                      $timeToPay = \Carbon\Carbon::createFromFormat('Y-m-d',$invoice->invoice_date )->diffInDays(\Carbon\Carbon::createFromFormat('Y-m-d', $invoice->payment_date));
                                  ?>
                                  {{ Form::text('payment_date_days', (!$invoice->payment_date || $invoice->payment_date == '0000-00-00') ? '' : $timeToPay , array('class' => 'form-control tips',  'placeholder' => 'termin płatności w dniach', 'title' => 'termin płatności w dniach', (!$invoice->invoice_date  || $invoice->invoice_date == '0000-00-00')?'disabled':'')) }}
                              </div>
                          </div>
                      </div>
                      @if($invoice->injury_files && $invoice->injury_files->category == 4)
                          {{ Form::hidden('base_invoice_netto', $base_invoice->netto) }}
                          {{ Form::hidden('base_invoice_vat', $base_invoice->vat) }}
                          <div class="row">
                              <div class="col-sm-12 marg-btm">
                                  <div class="col-sm-4">
                                      <label >Kwota netto po korekcie:</label>
                                      {{ Form::text('netto', money_format("%.2n",$invoice->netto), array('class' => 'form-control currency_input number tips check_disable', 'title' => 'kwota netto po korekcie',  'placeholder' => 'kwota netto po korekcie')) }}
                                  </div>
                                  <div class="col-sm-4">
                                      <label >Korekta netto:</label>
                                      {{ Form::text('netto_correction', money_format("%.2n",$invoice->netto - $base_invoice->netto), array('class' => 'form-control currency_input number tips check_disable', 'title' => 'korekta netto' , 'placeholder' => 'korekta netto')) }}
                                  </div>
                                  <div class="col-sm-4">
                                      <label >Stawka vat:</label>
                                      {{ Form::select('vat_rate_id', $vat_rates, $invoice->vat_rate_id ? $invoice->vat_rate_id : 1, array('class' => 'form-control')) }}
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-sm-12 marg-btm">
                                  <div class="clearfix">
                                      <label >Kwota VAT po korekcie:</label>
                                  </div>
                                  <div class="col-sm-6">
                                      {{ Form::text('vat', money_format("%.2n",$invoice->vat), array('class' => 'form-control currency_input number tips check_disable', 'title' => 'kwota vat po korekcie',  'placeholder' => 'kwota vat po korekcie')) }}
                                  </div>
                                  <div class="col-sm-6">
                                      {{ Form::text('vat_correction', money_format("%.2n",$invoice->vat - $base_invoice->vat), array('class' => 'form-control currency_input number tips check_disable', 'title' => 'korekta vat' , 'placeholder' => 'korekta vat')) }}
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-sm-12 marg-btm">
                                  <div class="clearfix">
                                      <label >Wartość brutto po korekcie:</label>
                                  </div>
                                  <div class="col-sm-6">
                                      {{ Form::text('brutto', money_format("%.2n",$invoice->vat+$invoice->netto), array('class' => 'form-control tips', 'title' => 'wartość brutto po korekcie',  'disabled' => 'disabled',  'placeholder' => 'wartość brutto po korekcie')) }}
                                  </div>
                                  <div class="col-sm-6">
                                      {{ Form::text('brutto_correction', money_format("%.2n",$invoice->vat+$invoice->netto - $base_invoice->vat - $base_invoice->netto), array('class' => 'form-control tips', 'title' => 'korekta brutto' , 'disabled' => 'disabled',  'placeholder' => 'korekta brutto')) }}
                                  </div>
                              </div>
                          </div>
                      @else
                          {{ Form::hidden('base_invoice_netto', 0) }}
                          {{ Form::hidden('base_invoice_vat', 0) }}
                          <div class="row">
                              <div class="col-sm-8 marg-btm">
                                  <label >Kwota netto:</label>
                                  {{ Form::text('netto', money_format("%.2n",$invoice->netto), array('class' => 'form-control currency_input number',  'placeholder' => 'kwota netto')) }}
                              </div>
                              <div class="col-sm-4 marg-btm">
                                  <label >Stawka vat:</label>
                                  {{ Form::select('vat_rate_id', $vat_rates, $invoice->vat_rate_id ? $invoice->vat_rate_id : 1, array('class' => 'form-control')) }}
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-sm-12 marg-btm">
                                  <label >Kwota VAT:</label>
                                  {{ Form::text('vat', money_format("%.2n",$invoice->vat), array('class' => 'form-control currency_input number',  'placeholder' => 'kwota vat')) }}
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-sm-12 marg-btm">
                                  <label >Wartość brutto:</label>
                                  {{ Form::text('brutto', money_format("%.2n",$invoice->vat+$invoice->netto), array('class' => 'form-control ', 'disabled' => 'disabled',  'placeholder' => 'wartość brutto')) }}
                              </div>
                          </div>
                      @endif
                          @if($invoice->injury->branch_id > 0 && $invoice->injury->branch->company->groups->count() > 0)
                      <div class="row">
                          <div class="col-sm-12 marg-btm">
                              <div class="checkbox ">
                                  <label>
                                      <input type="checkbox" name="commission" data-commissionable="{{ $commissionable }}" value="1"
                                             @if($invoice->commission == 1)
                                             checked="checked"
                                              @endif
                                      >
                                      Licz do prowizji
                                  </label>
                              </div>
                          </div>
                      </div>
                          @endif
                      <div class="row" id="base_netto_content"
                           @if($invoice->commission == 0)
                           style="display:none;"
                              @endif
                      >
                          <div class="col-sm-12 marg-btm">
                              <label >Podstawa netto:</label>
                              {{ Form::text('base_netto', ($invoice->base_netto == 0) ? money_format("%.2n",$invoice->netto) : money_format("%.2n",$invoice->base_netto), array('class' => 'form-control number',  'placeholder' => 'kwota podstawy netto')) }}
                          </div>
                    </div>
                    <div class="col-sm-12">
                        <div style="padding: 10px;">
                            <h5>Przypisanie serwisu według faktury</h5>
                            <label>NIP:</label>
                            {{ Form::text('branch_nip', !is_null($invoice->branch)?
                            (!is_null($invoice->branch->nip)?$invoice->branch->nip:$invoice->branch->company->nip):null
                            , array('class' => 'form-control',  'placeholder' => 'Numer NIP','id'=>'branchNip', )) }}
                            <input type="hidden" id="branch_id" name="branch_id">
                        </div>
                        <div class="col-sm-7">
                            <span>Dopasowane oddziały</span>
                            <div class="panel panel-default">
                                <select name="companies_matched" id="companies_matched" class="form-control" style="width:100%;"></select>
                                <ul class="list-group">
                                    <li class="list-group-item" id="searched_com_name">Nazwa:</li>
                                    <li class="list-group-item" id="searched_com_address">Adres:</li>
                                    <li class="list-group-item" id="searched_com_tel">Telefon:</li>
                                    <li class="list-group-item" id="searched_com_email">E-mail:</li>
                                </ul>
                            </div>
                            <dl class="dl-horizontal marg-btm"  style="display: none;" id="vat_check_info">
                                  <dt>Status VAT:</dt>
                                  <dd id="vat_check_status"></dd>
                                  <dt>Data sprawdzenia:</dt>
                                  <dd id="vat_check_date"></dd>
                              </dl>
                        </div>
                        <div class="col-sm-5">
                            <div class="panel panel-default">
                                <div class="panel-heading">Numery rachunków serwisu:
                                </div>
                                <table id="bank_accounts_table"class="table table-striped table-fixed mb-0" cellspacing="0" width="100%" style="text-align: center; position: relative;">
                                    <tbody id="bank_number_accounts" style="display: grid; min-height: 165px; max-height: 300px; overflow-y: scroll" width="100%">
                                    <thead class="custom-control custom-checkbox" name="bank_account_numbers">
                                    </thead>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    </div>
                </div>
            </form>
            @if($invoice->injury_files && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-primary" data-loading-text="trwa zapisywanie..."
                            id="set-injury"><i class="fa fa-floppy-o fa-fw"></i> Zapisz
                    </button>
                </div>
            @endif
        </div>
        @if($invoice->injury_files && in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
            <div class="col-sm-6">
                @if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['jpeg', 'jpg', 'png' ,'gif',  'bmp']))
                    <img src="{{ url('injuries/preview-doc', [$invoice->injury_files->id]) }}" class="img-rounded"
                         style="max-width: 100%;">
                @elseif(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['tiff', 'tif']))
                    <div class="image-body" style="height: 80vh; overflow: auto;">

                    </div>
                @else
                    <iframe style="width:100%; border: none; height:50vw;"
                            src="{{ url('injuries/preview-doc', [$invoice->injury_files->id]) }}"></iframe>
                @endif
            </div>
        @endif
    </div>
</div>
@if(!$invoice->injury_files || !in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['pdf', 'jpeg', 'jpg', 'png' ,'gif', 'tiff', 'tif', 'bmp']))
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
        <button type="button" class="btn btn-primary" id="set-injury">Zapisz</button>
    </div>
@endif
<script type="text/javascript">
    var base_invoice_netto = $('input[name="base_invoice_netto"]').val();
    var base_invoice_vat = $('input[name="base_invoice_vat"]').val();
    var matched_companies = null;
    var choosed_bank_accounts = [];
    var available_bank_accounts = [];
    var choosed_company = null;
    var prev_choosed_company = null;

    var branches = [];

    function recalculate() {
        if ($('input[name="netto"]').val() != '')
            var netto = parseFloat($('input[name="netto"]').val());
        else
            var netto = parseFloat(0);

        if ($('input[name="vat"]').val() != '')
            var vat = parseFloat($('input[name="vat"]').val());
        else
            var vat = parseFloat(0);

        $('input[name="brutto"]').val((netto + vat).toFixed(2));

        var correction_netto = parseFloat(netto - base_invoice_netto).toFixed(2);
        var correction_vat = parseFloat(vat - base_invoice_vat).toFixed(2);
        var brutto_correction = parseFloat(netto + vat - base_invoice_netto - base_invoice_vat).toFixed(2);

        $('input[name="netto_correction"]').val(correction_netto);
        $('input[name="vat_correction"]').val(correction_vat);
        $('input[name="brutto_correction"]').val(brutto_correction);

        if ($('input[name="commission"]').is(':checked')) {
            if ($('[name="netto_correction"]').length) {
                $('input[name="base_netto"]').val($('input[name="netto_correction"]').val());
            } else {
                $('input[name="base_netto"]').val($('input[name="netto"]').val());
            }
        }
    }

    $(document).ready(function () {

        searchCompanies('#branchNip');

        if (base_invoice_netto == '0') {
            $('.check_disable').attr('disabled', 'disabled');
        }

        $('input[name="commission"]').on('click', function () {
            if ($(this).is(':checked')) {
                $('#base_netto_content').show();
                if ($('[name="netto_correction"]').length) {
                    $('input[name="base_netto"]').val($('input[name="netto_correction"]').val());
                } else {
                    $('input[name="base_netto"]').val($('input[name="netto"]').val());
                }
            } else
                $('#base_netto_content').hide();
        });

        $('input[name="netto"]').on('keyup', function () {
            var vat_rate = parseFloat($('[name="vat_rate_id"] option:selected').first().text());
            var netto = parseFloat($(this).val());
            var vat = netto * (vat_rate / 100);
            $('input[name="vat"]').val(parseFloat(vat).toFixed(2));
            recalculate();
        });

        $('input[name="vat"]').on('keyup', function () {
            if ($(this).val() != '')
                var vat = parseFloat($(this).val());
            else
                var vat = parseFloat(0);

            var netto = parseFloat($('input[name="netto"]').val());

            var vat_correction = vat - parseFloat(base_invoice_vat);

            var netto_correction = parseFloat($('input[name="netto_correction"]').val());

            var brutto_correction = parseFloat(parseFloat(netto_correction) + parseFloat(vat_correction)).toFixed(2);

            $('input[name="vat_correction"]').val(vat_correction.toFixed(2));
            $('input[name="brutto_correction"]').val(brutto_correction);
            $('input[name="brutto"]').val(parseFloat(parseFloat(netto) + parseFloat(vat)).toFixed(2));
        });

        $('[name="vat_rate_id"]').on('change', function () {
            var vat_rate = parseFloat($('[name="vat_rate_id"] option:selected').first().text());
            var netto = parseFloat($('input[name="netto"]').val());
            var vat = netto * (vat_rate / 100);

            $('input[name="vat"]').val(parseFloat(vat).toFixed(2));
            recalculate();
        });

        $('input[name="netto_correction"]').on('keyup', function () {
            if ($('input[name="netto_correction"]').val() != '')
                var netto_correction = parseFloat($('input[name="netto_correction"]').val());
            else
                var netto_correction = parseFloat(0);

            var vat_rate = parseFloat($('[name="vat_rate_id"] option:selected').first().text());

            var netto = parseFloat(parseFloat(base_invoice_netto) + parseFloat(netto_correction)).toFixed(2);
            var vat = netto * (vat_rate / 100);
            var vat_correction = parseFloat(vat - base_invoice_vat).toFixed(2);
            var brutto_correction = parseFloat(parseFloat(netto_correction) + parseFloat(vat_correction)).toFixed(2);

            $('input[name="netto"]').val(netto);
            $('input[name="vat"]').val(vat.toFixed(2));
            $('input[name="brutto_correction"]').val(brutto_correction);
            $('input[name="brutto"]').val(parseFloat(parseFloat(netto) + parseFloat(vat)).toFixed(2));
            $('input[name="vat_correction"]').val(vat_correction);

            if ($('input[name="commission"]').is(':checked')) {
                if ($('[name="netto_correction"]').length) {
                    $('input[name="base_netto"]').val($('input[name="netto_correction"]').val());
                } else {
                    $('input[name="base_netto"]').val($('input[name="netto"]').val());
                }
            }
        });

        $('input[name="vat_correction"]').on('keyup', function () {
            if ($('input[name="vat_correction"]').val() != '')
                var vat_correction = parseFloat($('input[name="vat_correction"]').val());
            else
                var vat_correction = parseFloat(0);

            var netto = parseFloat(base_invoice_netto);
            var vat = parseFloat(base_invoice_vat) + vat_correction;

            var netto_correction = parseFloat($('input[name="netto_correction"]').val());

            var brutto_correction = parseFloat(parseFloat(netto_correction) + parseFloat(vat_correction)).toFixed(2);
            $('input[name="vat"]').val(vat.toFixed(2));
            $('input[name="brutto_correction"]').val(brutto_correction);
            $('input[name="brutto"]').val(parseFloat(parseFloat(netto) + parseFloat(vat)).toFixed(2));

            if ($('input[name="commission"]').is(':checked')) {
                console.log('aa');
                if ($('[name="netto_correction"]').length) {
                    $('input[name="base_netto"]').val($('input[name="netto_correction"]').val());
                } else {
                    $('input[name="base_netto"]').val($('input[name="netto"]').val());
                }
                return $(this).is(':checked');
            }
        });

        $('input[name="invoice_date"]').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            'changeMonth': true,
            maxDate: '0',
            onSelect: function () {
                $('input[name="payment_date"], input[name="payment_date_days"]').removeAttr('disabled');
                $(this).change();
            }
        });

        $('input[name="payment_date"]').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            'changeMonth': true,
            onSelect: function () {
                $('input[name="payment_date"], input[name="payment_date_days"]').removeAttr('disabled');
                $(this).change();
            }
        });

        $('input[name="payment_date"]').on('change', function () {
            var end_date = parseDateToIe($(this).val() + ' 00:00:00');
            var start_date = parseDateToIe($('input[name="invoice_date"]').val() + ' 00:00:00');
            var diff = Math.abs(end_date - start_date);
            var one_day = 1000 * 60 * 60 * 24;
            diff = Math.round(diff / one_day);
            $('input[name="payment_date_days"]').val(diff);
        });

        $('input[name="payment_date_days"]').on('keyup', function () {
            if ($(this).val() != '') {
                var start_date = parseDateToIe($('input[name="invoice_date"]').val() + ' 00:00:00');
                var diff = parseInt($(this).val());
                start_date.setDate(start_date.getDate() + diff);

                var dd = start_date.getDate();
                var mm = start_date.getMonth() + 1;
                var y = start_date.getFullYear();

                $('input[name="payment_date"]').val(y + '-' + mm + '-' + dd);
            }
        });

        $('input[name="base_netto"]').on('keyup', function () {
            $('#base_error').remove();
            if ($(this).val() == '')
                $('<label for="base_netto" id="base_error" class="error">Pole wymagene.</label>').insertAfter($(this));
            else {
                var base = parseFloat($('input[name="netto"]').val());
                var base_netto = parseFloat($(this).val());
                if (base < base_netto)
                    $('<label for="base_netto" id="base_error" class="error">Podstawa netto nie może być większa od wartości netto faktury.</label>').insertAfter($(this));
            }
        });

        $('select[name="parent_id"]').on('change', function () {
            var parent_id = $(this).val();

            if (parent_id == '0') {
                $('.check_disable').attr('disabled', 'disabled');
            } else {
                var element = $(this).find('option:selected');
                base_invoice_netto = element.data('netto');
                base_invoice_vat = element.data('vat');
                $('[name="netto"]').val(base_invoice_netto).keyup();
                recalculate();
                $('.check_disable').removeAttr('disabled');
            }
        });

        $(document).on('click', '.bt_com_search', function () {
            $('.bt_com_search').removeClass('active').removeClass('green');
            $(this).addClass('active').addClass('green');
            $('#branch_id').val($(this).attr('id'));
        });

        $('.number').on('blur', function () {
            $('#value_commission').val('Obliczanie...');
            $.post("{{ URL::route('injuries-getInvoiceCommission', array($id)) }}", $('#dialog-injury-form').serialize(), function (result) {
                result = JSON.parse(result)
                if (result.code == 0) {
                    $('#value_commission').val(result.value.toFixed(2))
                }
            })
        });

        $('[name="injury_invoice_service_type_id"]').on('change', function () {
            var selected_service_type = $('[name="injury_invoice_service_type_id"] option:selected').val();
            var commissionable = $('[name="commission"]').attr('data-commissionable');

            if (selected_service_type == '6' || selected_service_type == '1' || selected_service_type == '2') {
                if (!$('[name="commission"]').prop('checked') && commissionable == '1')
                    $('[name="commission"]').click();
            } else {
                if ($('[name="commission"]').prop('checked'))
                    $('[name="commission"]').click();
            }
        });

        $('#branchNip').on('keyup', function () {
            searchCompanies(this);
        });

        $('#companies_matched').on('change', function () {
            refreshDisplayedData(this.value);
        });

        function searchCompanies(branchNip) {
            term = $(branchNip).val();
            current_branch_id = "<?php echo  $invoice->branch ? $invoice->branch->id : null;?>";
            if (term.length > 3) {
                $.ajax({
                    url: "<?php echo URL::route('injuries-assignBranchesNameList', array($invoice->injury->id, 1, 0, $invoice->id)); ?>",
                    data: {
                        "term": term,
                        "_token": $('input[name="_token"]').val()
                    },
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                        markers = new Array(data.length);
                        companies = new Array(data.length);
                        matched_companies = data;
                        if (data.length != 0) {

                            branches = data;
                            id_warsztat = $('#id_warsztat').val();
                            $("#companies_matched").empty();
                            for (i = 0; i < data.length; i++) {
                                var o = new Option(data[i].nazwa, data[i].id, 0, data[i].id == current_branch_id);
                                $(o).html(data[i].nazwa);
                                $("#companies_matched").append(o);
                            }
                            refreshDisplayedData(branches[0].id);
                        } else {
                            $("#companies_matched").empty();
                            $('#searched_com_name').text('Nazwa: ');
                            $('#searched_com_address').text('Adres: ');
                            $('#searched_com_tel').text('Telefon: ');
                            $('#searched_com_email').text('E-mail: ');
                            $('.bt_com_search').removeClass('active').removeClass('green');
                            $('#branch_id').val("");
                            refreshDisplayedData(null);
                        }
                    }
                });
            }
        }

        function selectAccountNumbers(branch) {
            if(branch != null) $.each(branch.bank_accounts, function (index) {
                no = branch.bank_accounts[index];
                var o = "<tr id=\"checkboxDiv" + index + "\"><td class=\"col-sm-9\" + 'style=\"margin: 0px\"><div style=\"padding: 0px; margin:0px\" class=\"alert " + (no.if_user_insert==1?'alert-warning':'') + "\"><label for=\"checkbox" + index + "\">" + no.account_number + "</label></div></td>" +
                    "<td class=\"col-sm-3\"><input data-assigned=" + no.assigned + " data-id=" + no.id + " type=\"checkbox\" class=\"custom-control acc_checkbox\" id=\"checkbox" + index + "\" value='" + no.id + "' name=\"assigned[]\""
                if (no.assigned == true) o = o.concat(" checked");
                o = o.concat("></input></td>\n</tr>");
                $('#bank_number_accounts').append(o)
            });
        }

        $('table').unbind().on('click', 'input[type=checkbox]', function () {
            var id = $(this).data("id");
            $(this).removeAttr('checked'); //remove this line to assign mutliple bank accounts
            $.each(choosed_company.bank_accounts, function (index) {
                if (choosed_company.bank_accounts[index].id == id) choosed_company.bank_accounts[index].assigned = !choosed_company.bank_accounts[index].assigned;
                else choosed_company.bank_accounts[index].assigned = false; //remove this line to assign mutliple bank accounts
            })
            refreshDisplayedData(choosed_company.id);
        });

        function refreshDisplayedData(branch_id) {
            if (choosed_company != null) {
                $.each(choosed_company.bank_accounts, function (index) {
                    $('#checkboxDiv' + index).remove();
                })
            }
            choosed_company = null;
            if (matched_companies != null && matched_companies != 'undefined') {
                choosed_company = null;
                for (i = 0; i < matched_companies.length; i++) {
                    if (matched_companies[i].id == branch_id) choosed_company = matched_companies[i];
                }
            }
            if (choosed_company != null && choosed_company != 'undefined') {
                $('.list-group-item').css({'background-color': choosed_company.company_vat_check_info['is_active'] ? '#dff0d8' : '#f2dede'});
                if(choosed_company.company_vat_check_info['is_active'] == true) {
                    $('#vat_check_date').html((choosed_company.company_vat_check_info['vat_check_date'].date).substring(0, 16));
                    $('#vat_check_status').html(choosed_company.company_vat_check_info['status']);
                    $('#vat_check_info').show();
                } else {
                    $('#vat_check_info').hide();
                }
                $('#searched_com_name').text('Nazwa: ' + choosed_company.nazwa);
                $('#searched_com_address').text('Adres: ' + choosed_company.kod + " " + choosed_company.miasto + " " + choosed_company.ulica);
                $('#searched_com_tel').text('Telefon: ' + choosed_company.telefon);
                $('#searched_com_email').text('E-mail: ' + choosed_company.email);
            }
            selectAccountNumbers(choosed_company);
        }
    });

</script>
@if(in_array(mb_strtolower(File::extension(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$invoice->injury_files->file)), ['tiff', 'tif']))
    <script>
        $(function () {
            Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ url('injuries/preview-doc', [$invoice->injury_files->id]) }}');
            xhr.responseType = 'arraybuffer';
            xhr.onload = function (e) {
                var buffer = xhr.response;
                var tiff = new Tiff({buffer: buffer});
                for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                    tiff.setDirectory(i);
                    var canvas = tiff.toCanvas();

                    $('.image-body').append(canvas);
                }

                $('.image-body canvas').each(function () {
                    $(this).css('width', '100%');
                });
            };
            xhr.send();
        });
    </script>
@endif
