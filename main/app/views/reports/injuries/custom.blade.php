@extends('layouts.main')

@section('header')
    Generowanie raportów DLS Pojazdy
@stop

@section('main')
    <div class="row marg-btm">
        <div class="col-lg-8 col-lg-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport konfigurowalny</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::route('reports.injuries.post', array('generateCustom')), 'class' => 'page-form', 'id' => 'orders' )) }}
                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="page-header marg-top-min">Wskaż pola raportu</h5>
                            </div>
                        </div>
                        <div class="row">
                            {{ Form::reportCheckbox('nr_contract', 'Nr umowy leasingu', 1, 'margin-top: -5px;') }}
                            {{ Form::reportCheckbox('registration', 'Nr rejestracyjny pojazdu') }}
                            {{ Form::reportCheckbox('vin', 'VIN pojazdu') }}
                            {{ Form::reportCheckbox('client_name', 'Nazwa klienta') }}
                            {{ Form::reportCheckbox('client_address', 'Adres klienta') }}
                            {{ Form::reportCheckbox('client_nip', 'NIP klienta') }}
                            {{ Form::reportCheckbox('injury_nr', 'Nr szkody ZU') }}
                            {{ Form::reportCheckbox('injury_type', 'Typ szkody') }}
                            {{ Form::reportCheckbox('injury_kind', 'Rodzaj szkody') }}
                            {{ Form::reportCheckbox('date_event', 'Data szkody') }}
                            {{ Form::reportCheckbox('vehicle', 'Przedmiot') }}
                            {{ Form::reportCheckbox('brand', 'Marka pojazdu') }}
                            {{ Form::reportCheckbox('model', 'Model pojazdu') }}
                            {{ Form::reportCheckbox('remarks', 'Opis zdarzenia') }}
                            {{ Form::reportCheckbox('damages', 'Uszkodzenia') }}
                            {{ Form::reportCheckbox('event_place', 'Miejsce zdarzenia') }}
                            {{ Form::reportCheckbox('branch', 'Serwis') }}
                            {{ Form::reportCheckbox('branch_type', 'Grupa serwisu') }}
                            {{ Form::reportCheckbox('branch_address', 'Adres Serwisu') }}
                            {{ Form::reportCheckbox('branch_voivodeship', 'Serwis - województwo') }}
                            {{ Form::reportCheckbox('info', 'Uwagi') }}
                            {{ Form::reportCheckbox('compensation', 'Wysokość odszkodowania') }}
                            {{ Form::reportCheckbox('status', 'Status szkody') }}
                            {{ Form::reportCheckbox('current_status', 'Aktualny status') }}
                            {{ Form::reportCheckbox('owner', 'Właściciel pojazdu') }}
                            {{ Form::reportCheckbox('created', 'Data zgłoszenia') }}
                            {{ Form::reportCheckbox('processing_type', 'Etap procesowania') }}
                            {{ Form::reportCheckbox('task_authorization', 'Wystawiono upoważnienie') }}
                            {{ Form::reportCheckbox('last_action', 'Data ostatniej modyfikacji') }}
                            {{ Form::reportCheckbox('date_end', 'Data zakończenia szkody') }}
                            {{ Form::reportCheckbox('user', 'Przyjmujący szkodę') }}
                            {{ Form::reportCheckbox('days', 'Upłynęło') }}
                            {{ Form::reportCheckbox('leader', 'Prowadzący') }}
                            {{ Form::reportCheckbox('leader_assign', 'Data przypisania prowadzącego') }}
                            {{ Form::reportCheckbox('on_current_step', 'Dni na obecnym etapie') }}
                            {{ Form::reportCheckbox('fv_proforma', 'Zlecono wystawienie FV – Proforma') }}
                            {{ Form::reportCheckbox('fv_proforma_date', 'Data wygenerowania Zał. 5 c Zlecenie wystawienia FV PRO FROMA') }}
                            {{ Form::reportCheckbox('fv_proforma_number', 'Numer FV proforma') }}
                            {{ Form::reportCheckbox('contractor_code', 'Kod kontrahenta') }}
                            {{ Form::reportCheckbox('pro_forma_value',  'Kwota brutto z FV') }}
                            {{ Form::reportCheckbox('invoice_request_confirm', 'Dostarczono FV właściwą') }}
                            {{ Form::reportCheckbox('insurance_company', 'Nazwa TU') }}
                            {{ Form::reportCheckbox('value_undamaged', 'Wartość pojazdu na dzień szkody') }}
                            {{ Form::reportCheckbox('value_repurchase', 'Wartośc pozostałosci') }}
                            {{ Form::reportCheckbox('value_compensation', 'Odszkodowanie wg wyliczeń TU') }}
                            {{ Form::reportCheckbox('insurance_compensation', 'Suma ubezpieczenia') }}
                            {{ Form::reportCheckbox('gap',  'GAP kwota odszkodowania') }}
                            {{ Form::reportCheckbox('value_indemnified', 'Kwota odszkodowania wypłaconego') }}
                            {{ Form::reportCheckbox('date_indemnified', 'Data wypłaty (data decyzji)') }}
                            {{ Form::reportCheckbox('date_theft',   'Data wyrejestrowania (przy kradzieży)') }}
                            {{ Form::reportCheckbox('authorization_date', 'Data wystawienia upoważnienia') }}
                            {{ Form::reportCheckbox('receiver', 'Na kogo upoważnienie') }}
                            {{ Form::reportCheckbox('order_sent', 'Czy wysłano zlecenie') }}
                            {{ Form::reportCheckbox('cfm', 'CFM') }}
                            {{ Form::reportCheckbox('case_nr', 'Numer Szkody wewnętrzny') }}
                            {{ Form::reportCheckbox('net_invoices', 'Wartość Netto z Faktury') }}
                            {{ Form::reportCheckbox('driver', 'Kierowca pojazdu') }}
                            {{-- {{ Form::reportCheckbox('fee', 'Czy naliczyć opłatę') }} --}}
                            {{ Form::reportCheckbox('fee2016', 'Czy naliczyć opłatę 2016') }}
                            {{ Form::reportCheckbox('step', 'Etap Sprawy') }}
                            {{ Form::reportCheckbox('repair_step', 'Etap Naprawy') }}
                            {{ Form::reportCheckbox('reported_ic', 'Zgłoszona do TU') }}
                            {{ Form::reportCheckbox('is_gap', 'Polisa GAP') }}
                            {{ Form::reportCheckbox('person_generated', 'Osoba generująca zlecenie') }}
                            {{ Form::reportCheckbox('production_year', 'Rok produkcji') }}
                            {{ Form::reportCheckbox('contract_status', 'Status umowy na dzień zgłoszenia') }}
                            {{ Form::reportCheckbox('end_leasing', 'Data ważności umowy') }}
                            {{ Form::reportCheckbox('value_compensation_real', 'Kwota wypłaconego odszkodowania na właściciela') }}
                            {{ Form::reportCheckbox('value_compensation_real_gap', 'Kwota wypłaconego odszkodowania GAP') }}
                            {{ Form::reportCheckbox('gap_forecast', 'Prognoza GAP') }}
                            {{ Form::reportCheckbox('previous_nip', 'Uprzedni NIP właściciela') }}

                            {{ Form::reportCheckbox('fv_date', 'Data wprowadzenia FV') }}
                            {{ Form::reportCheckbox('fv_type', 'Typ FV') }}
                            {{ Form::reportCheckbox('date_of_generate_order', 'Data wygenerowania zlecenia') }}
                            {{ Form::reportCheckbox('cost_estimate', 'Kosztorysowe rozliczenie ') }}
                            {{-- {{ Form::reportCheckbox('skip_in_ending_report', 'Opłata Naliczona') }} --}}                           
                            {{ Form::reportCheckbox('injury_has_feed_document', 'Czy naliczyć opłatę') }}
                            {{ Form::reportCheckbox('gap_number', 'numer szkody GAP') }}
                            {{ Form::reportCheckbox('dsp_notification', 'zgłoszenie DSP') }}
                            {{ Form::reportCheckbox('vindication', 'windykacja') }}
                            {{ Form::reportCheckbox('cas_offer_agreement', 'zgoda na ofertę CAS') }}
                            {{ Form::reportCheckbox('if_doc_fee_enabled', 'zgoda na odstępstwo od opłaty za UP') }}
                            {{ Form::reportCheckbox('sap_rodzszk', 'SAP rodzszk') }}
                            {{ Form::reportCheckbox('sales_program', 'Program sprzedazy') }}
                            {{ Form::reportCheckbox('date_total_theft_register', 'Data przejścia na szkodę całkowitą') }}
                            {{ Form::reportCheckbox('client_phone', 'telefon LB/PB') }}
                            {{ Form::reportCheckbox('client_city', 'klient miasto') }}
                            {{ Form::reportCheckbox('client_voivodeship', 'klient województwo') }}
                            {{ Form::reportCheckbox('client_email', 'email Klienta') }}
                            {{ Form::reportCheckbox('gap_type', 'rodzaj produktu GAP') }}
                            {{ Form::reportCheckbox('type_incident', 'rodzaj zdarzenia') }}
                            {{ Form::reportCheckbox('request_loss_value', 'wniosek o HUWP') }}
                            {{ Form::reportCheckbox('consent_to_invoice', 'Zgoda na FV na IGL') }}
                            {{ Form::reportCheckbox('vehicle_type', 'Rodzaj pojazdu') }}
                            {{ Form::reportCheckbox('invoicereceive', 'Odbiorca FV') }}
                        </div>

                        <div class="alert alert-warning" style="margin: 10px; display: none" id="injury_kind_options" role="alert">
                                <input type="checkbox" name="subparams[injury_kind][partial]"> Częściowa
                                <input type="checkbox" name="subparams[injury_kind][total]"> Całkowita
                                <input type="checkbox" name="subparams[injury_kind][theft]"> Kradzież
                        </div>

                        @include('reports.partials.datepicker', array('datepicker_id_from' => 'date_from', 'datepicker_id_to' => 'date_to'))
                        @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').each(function(e){
                $(this).validate().cancelSubmit = true;
            });

            $(".page-form").submit(function(e) {
                var self = this;

                e.preventDefault();

                if($(this).valid()){
                    self.submit();
                }


                return false; //is superfluous, but I put it here as a fallback
            });

            $('.monthdate').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true,maxDate: "+0D",dateFormat: "yy-mm",
                onClose: function(dateText, inst) {
                    $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                }
            });

            $('input[name="fields[injury_kind]"]').change(function (){
                    if($(this).is(':checked'))
                        $('#injury_kind_options').show();
                    else
                        $('#injury_kind_options').hide();
            });
        });
    </script>
@stop
