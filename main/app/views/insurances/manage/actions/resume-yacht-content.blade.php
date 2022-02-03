<div class="row">
    <div class="col-sm-12">
        <h4 class="page-header marg-top-min">Dane nowej polisy</h4>
    </div>
    {{ Form::hidden('if_continuation', '0') }}
    {{ Form::hidden('if_load_decision', '0')}}
    {{ Form::hidden('leasing_agreement_id', $leasingAgreement->id) }}
    {{ Form::hidden('leasing_agreement_insurance_id', $insurance->id) }}
    {{ Form::hidden('active', 1) }}
    {{ Form::hidden('user_id', Auth::user()->id) }}
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Nr polisy</label>
            <div class="col-sm-8">
                <input value="" class="form-control " name="insurance_number" placeholder="numer polisy">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Nr zgłoszenia</label>
            <div class="col-sm-8">
                <input value="{{ Auth::user()->insurances_global_nr }}" class="form-control" name="notification_number"
                       disabled placeholder="numer zgłoszenia">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Okres leasingu od</label>
            <div class="col-sm-8">
                <input value="{{ $leasingAgreement->insurance_from }}" class="form-control " readonly>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Okres leasingu do</label>
            <div class="col-sm-8">
                <input value="{{ $leasingAgreement->insurance_to }}" class="form-control " readonly>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Długość polisy [msc]</label>
            <div class="col-sm-8">
                <input value="12" class="form-control date" name="months" id="months"
                       placeholder="długość trwania polisy">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Data polisy</label>
            <div class="col-sm-8">
                <input value="{{$insurance->insurance_date}}" class="form-control date required" name="insurance_date"  placeholder="data polisy">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Polisa od</label>
            <div class="col-sm-8">
                <input value="{{$insurance->date_to}}" class="form-control date required" name="date_from" id="date_from" placeholder="polisa od" >
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Polisa do</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <input value="" class="form-control date required" name="date_to" id="date_to"  placeholder="polisa do">
                    <span class="input-group-btn tips" title="edytuj - nie koryguje długości">
                        <button class="btn btn-default" type="button" id="edit-date-to">
                            <i class="fa fa-pencil"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <h4 class="inline-header"></h4>
        <div class="form-group marg-top">
            <label class="col-sm-4 control-label">Wartość z umowy
                {{ ($leasingAgreement->net_gross == 2) ? '[brutto]' : '[netto]'  }}
            </label>
            <div class="col-sm-8">
                <input value="{{ ($leasingAgreement->net_gross == 2) ?  $leasingAgreement->loan_gross_value : $leasingAgreement->loan_net_value}}"
                       class="form-control number currency_input required"
                       name="{{ ($leasingAgreement->net_gross == 2) ?  "loan_gross_value" : "loan_net_value"}}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Przedmioty umowy
            </label>
            <div class="col-sm-8">
                <ul class="list-group">
                    @foreach($leasingAgreement->objects as $object)
                        <li class="list-group-item">{{ $object->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="form-group ">
            <label class="col-sm-4 control-label">Leasingobiorca</label>
            <div class="col-sm-8">
                <p class="help-block form-control">{{ $leasingAgreement->client->name }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="form-group">
            <p class="text-center col-sm-offset-4 col-sm-8 help-block">{{ $leasingAgreement->import_insurance_company }}</p>
            <label class="col-sm-4 control-label">Ubezpieczyciel</label>
            <div class="col-sm-8">
                {{ Form::select('insurance_company_id', $insuranceCompanies, $insurance->insurance_company_id, array('class' => 'form-control', 'id' => 'insurance_company_id') ) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Płatn. ubezp. przez leasingobiorcę</label>
            <div class="col-sm-8">
                {{ Form::select('leasing_agreement_payment_way_id', $paymentWays, $insurance->leasing_agreement_payment_way_id, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group" id="installments-container">
            <label class="col-sm-4 control-label">Liczba rat</label>
            <div class="col-sm-8">
                {{ Form::select('leasing_agreement_installment_id', $installments, $insurance->leasing_agreement_installment_id, array('class' => 'form-control', 'installment') ) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Składka leasingobiorcy</label>
            <div class="col-sm-6">
                <input value="{{ $insurance->contribution_lessor }}" class="form-control number currency_input required"
                       name="contribution_lessor" placeholder="składka leasingobiorcy" id="contribution_lessor" require>
            </div>
            <div class="col-sm-2">
                {{ Form::select('contribution_lessor_currency_id', Config::get('definition.currencies'), $insurance->contribution_lessor_currency_id, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4 control-label"></div>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        <input name="if_foreign_policy" value="1" type="checkbox"> Polisa obca
                    </label>
                </div>
            </div>
        </div>
        <h4 class="inline-header"><span>Termin płatności:</span></h4>
        <div id="payment-deadline-container">

        </div>
        <h4 class="inline-header"><span>Prowizje:</span></h4>
        <div class="form-group">
            <label class="col-sm-4 control-label">Wysokość prowizji</label>
            <div class="col-sm-8">
                {{ Form::text('commission_value', null, array('class' => 'form-control number currency_input', 'placeholder' => 'wysokość prowizji') ) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Data prowizji</label>
            <div class="col-sm-8">
                {{ Form::text('commission_date', null, array('class' => 'form-control date dynamic_datepicker', 'placeholder' => 'data prowizji') ) }}
            </div>
        </div>
        <h4 class="inline-header"><span>Zakres ubezpieczenia:</span></h4>
        <div class="form-group">
            <label class="col-sm-4 control-label">OC</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('coverages[]', 1, ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->count() > 0) ? true : null, ['class' => 'insurance-coverage', 'data-group' => 'oc-group']) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group oc-group" style="display: none;">
            <label class="col-sm-4 control-label">Suma ubezpieczenia OC</label>
            <div class="col-sm-8">
                <input value="{{ ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->first()->amount : '' }}"
                       class="form-control number currency_input" name="oc_insurance"
                       placeholder="suma ubezpieczenia oc" require>
            </div>
        </div>
        <div class="form-group oc-group" style="display: none;">
            <label class="col-sm-4 control-label">Waluta OC</label>
            <div class="col-sm-8">
                {{ Form::select('oc_currency', Config::get('definition.currencies'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->first()->currency_id : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group oc-group" style="display: none;">
            <label class="col-sm-4 control-label">Netto/brutto</label>
            <div class="col-sm-8">
                {{ Form::select('oc_net_gross', Config::get('definition.net_gross'),  ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 1)->first()->net_gross : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">AC</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('coverages[]', 2, ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->count() > 0) ? true : null, ['class' => 'insurance-coverage', 'data-group' => 'ac-group']) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group ac-group" style="display: none;">
            <label class="col-sm-4 control-label">Suma ubezpieczenia AC</label>
            <div class="col-sm-8">
                <input value="{{ ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->first()->amount : '' }}"
                       class="form-control number currency_input" name="ac_insurance"
                       placeholder="suma ubezpieczenia AC" require>
            </div>
        </div>
        <div class="form-group ac-group" style="display: none;">
            <label class="col-sm-4 control-label">Waluta AC</label>
            <div class="col-sm-8">
                {{ Form::select('ac_currency', Config::get('definition.currencies'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->first()->currency_id : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group ac-group" style="display: none;">
            <label class="col-sm-4 control-label">Netto/brutto</label>
            <div class="col-sm-8">
                {{ Form::select('ac_net_gross', Config::get('definition.net_gross'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 2)->first()->net_gross : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">NNW</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('coverages[]', 3, ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->count() > 0) ? true : null, ['class' => 'insurance-coverage', 'data-group' => 'nnw-group']) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group nnw-group" style="display: none;">
            <label class="col-sm-4 control-label">Suma ubezpieczenia NNW</label>
            <div class="col-sm-8">
                <input value="{{ ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->first()->amount : '' }}"
                       class="form-control number currency_input" name="nnw_insurance"
                       placeholder="suma ubezpieczenia nnw" required>
            </div>
        </div>
        <div class="form-group nnw-group" style="display: none;">
            <label class="col-sm-4 control-label">Waluta NNW</label>
            <div class="col-sm-8">
                {{ Form::select('nnw_currency', Config::get('definition.currencies'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->first()->currency_id : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group nnw-group" style="display: none;">
            <label class="col-sm-4 control-label">Netto/brutto</label>
            <div class="col-sm-8">
                {{ Form::select('nnw_net_gross', Config::get('definition.net_gross'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 3)->first()->net_gross : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Mienie osobiste członków załogi</label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('coverages[]', 4, ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->count() > 0) ? true : null, ['class' => 'insurance-coverage', 'data-group' => 'crew-group']) }}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group crew-group" style="display: none;">
            <label class="col-sm-4 control-label">Suma ubezpieczenia mienia osobistego członków załogi</label>
            <div class="col-sm-8">
                <input value="{{ ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->first()->amount : '' }}"
                       class="form-control number currency_input" name="crew_insurance"
                       placeholder="suma ubezpieczenia mienia osobistego członków załogi" required>
            </div>
        </div>
        <div class="form-group crew-group" style="display: none;">
            <label class="col-sm-4 control-label">Waluta ubezpieczenia mienia osobistego członków załogi</label>
            <div class="col-sm-8">
                {{ Form::select('crew_currency', Config::get('definition.currencies'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->first()->currency_id : null, array('class' => 'form-control') ) }}
            </div>
        </div>
        <div class="form-group crew-group" style="display: none;">
            <label class="col-sm-4 control-label">Netto/brutto</label>
            <div class="col-sm-8">
                {{ Form::select('crew_net_gross', Config::get('definition.net_gross'), ($insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->count() > 0) ? $insurance->coverages()->where('leasing_agreement_insurance_coverage_type_id', 4)->first()->net_gross : null, array('class' => 'form-control') ) }}
            </div>
        </div>
    </div>
    <div class="row marg-top" id="acceptation-container" style="display: none;">
        <div class="text-center col-md-8 col-md-offset-2">
            <label class="checkbox-inline text-danger">
                <input type="checkbox" name="acceptation" value="acceptation" class="required" required> Potwierdź
                świadomość, iż zakres polisy nie pokrywa się z numerem zgłoszenia
            </label>
        </div>
    </div>
    <div class="row marg-top">
        <div class="text-center col-md-8 col-md-offset-2">
            {{ Form::submit('Wprowadź wznowienie',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa wprowadzanie polisy...'))  }}
        </div>
    </div>
</div>
<script type="text/javascript">
    var acceptation_required = false;
    $(document).ready(function () {

        $("form#page-form").submit(function (e) {
            var $btn = $('#form_submit').button('loading');
            if (!$('#page-form').valid() || (acceptation_required && !$('[name="acceptation"]').is(':checked'))) {
                e.preventDefault();
                $btn.button('reset');
                return false;
            }
            return true;
        });

        $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
            changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd",
            onClose: function (selectedDate) {
                if ($(this).attr('date-opt') == 'from') {
                    $('#date_to').datepicker("option", "minDate", selectedDate);
                } else if ($(this).attr('date-opt') == 'to') {
                    $('#date_from').datepicker("option", "maxDate", selectedDate);
                }
            }
        });

        $('#date_from').on('change', function () {
            var date_from = $(this).val();
            date_from = date_from.split('-');

            var notification_number = $('input[name="notification_number"]').val();
            notification_number = notification_number.split('/');

            if (date_from[0] != notification_number[1] || date_from[1] != notification_number[0]) {
                acceptation_required = true;
                $('#acceptation-container').show();
            } else {
                acceptation_required = false;
                $('#acceptation-container').hide();
            }

        }).change();

        $('#months, #date_from').on('change', function () {
            if ($('#months').val() == '') {
                $.notify({
                    icon: "fa fa-warning",
                    message: 'proszę wybrać prawidłowy okres ubezpieczenia w miesiącach'
                }, {
                    type: 'danger',
                    placement: {
                        from: 'bottom',
                        align: 'right'
                    },
                    delay: 5000,
                    timer: 500
                });
                $('#date_to').val('');
            } else {
                var date_from = $('#date_from').val() + ' 00:00:00';
                date_from = parseDateToIe(date_from);
                var date_to = date_from;
                date_to.setMonth(date_from.getMonth() + parseInt($('#months').val()));
                date_to.setDate(date_from.getDate() - 1);
                var MyDateString = (date_to.getFullYear() + '-'
                    + ('0' + (date_to.getMonth() + 1)).slice(-2) + '-'
                    + ('0' + date_to.getDate()).slice(-2));
                $("#date_to").val(MyDateString);
            }
        }).change();

        $('select[name="leasing_agreement_payment_way_id"]').on('change', function () {
            if ($(this).find('option:selected').val() == 1) {
                $('#installments-container').show();
            } else {
                $('#installments-container').hide();
            }
        }).change();

        $('select[name="leasing_agreement_payment_way_id"], select[name="leasing_agreement_installment_id"]').on('change', function () {
            $.ajax({
                url: "{{ URL::to('insurances/manage-actions/payment-deadline') }}",
                data: {
                    leasing_agreement_payment_way_id: $('select[name="leasing_agreement_payment_way_id"]').val(),
                    leasing_agreement_installment_id: $('select[name="leasing_agreement_installment_id"]').val(),
                    _token: $('input[name="_token"]').val()
                },
                dataType: "html",
                type: "POST",
                success: function (data) {
                    $('#payment-deadline-container').html(data);
                }
            });
        }).change();

        $('.insurance-coverage').on('change', function () {
            var group_class = $(this).data('group');
            if ($(this).is(':checked')) {
                $("." + group_class).show();  // checked
            } else {
                $("." + group_class).hide();
            }
        }).change();

        $('#edit-date-to').on('click', function () {
            $('#date_to').removeAttr('readonly');
        });

    });
</script>
