@extends('layouts.main')

@section('header')

    Dodawanie polisy cesji do umowy nr {{ $leasingAgreement->nr_contract }}

    <div class="pull-right">
        <a href="{{{ URL::previous() }}}#insurances-data" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">

        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/info-insurances/store-cession', [$leasingAgreement->id]), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                    <div class="row">
                        <div class="col-sm-12" id="cession_container">
                            <h4 class="page-header marg-top-min overflow">Wyszukaj leasingobiorcę do cesji
                                <p class="pull-right btn btn-sm btn-primary modal-open" target="{{ URL::to('insurances/info-dialog/create-client') }}" data-toggle="modal" data-target="#modal"><i class="fa fa-plus"></i> dodaj nowego leasingobiorcę</p>
                            </h4>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input class="form-control " id="client_name" name="client_name" placeholder="wg nazwy">
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" id="client_NIP" name="client_NIP" placeholder="wg NIP">
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" id="client_REGON" name="client_REGON" placeholder="wg REGON">
                                </div>
                            </div>
                            <div class="row" >
                                <div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2" role="alert" style="display: none;" id="searching_info">
                                </div>
                            </div>
                            {{ Form::hidden('client_id', '', array('id' => 'client_id')) }}
                            <div id="client_info_container" style="display: none;">
                                <h4 class="marg-top">Dane leasingobiorcy</h4>
                                <div class="form-group row">
                                    <div class="col-sm-12 col-md-6">
                                        <table class="table table-hover table-condensed">
                                            <tr class="active">
                                                <Td colspan="2">
                                                    <span class="sm-title">Dane rejestrowe:</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Nazwa:</label></td>
                                                <td class="client_info" id="client_info_name"></td>
                                            </tr>
                                            <tr>
                                                <td><label>NIP:</label></td>
                                                <td class="client_info" id="client_info_NIP"></td>
                                            </tr>
                                            <tr>
                                                <td><label>REGON:</label></td>
                                                <td class="client_info" id="client_info_REGON"></td>
                                            </tr>
                                            <tr class="active">
                                                <Td colspan="2"><span class="sm-title">Adres rejestrowy:</span></td>
                                            </tr>
                                            <tr>
                                                <td><label>Kod pocztowy:</label></td>
                                                <td class="client_info" id="client_info_registry_post"></td>
                                            </tr>
                                            <tr>
                                                <td><label>Miato:</label></td>
                                                <td class="client_info" id="client_info_registry_city"></td>
                                            </tr>
                                            <tr>
                                                <td><label>Ulica:</label></td>
                                                <td class="client_info" id="client_info_registry_street"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <table class="table table-hover table-condensed">
                                            <tr class="active">
                                                <Td colspan="2">
                                                    <span class="sm-title">Adres kontaktowy:</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Kod pocztowy:</label></td>
                                                <td class="client_info" id="client_info_correspond_post"></td>
                                            </tr>
                                            <tr>
                                                <td><label>Miato:</label></td>
                                                <td class="client_info" id="client_info_correspond_city"></td>
                                            </tr>
                                            <tr>
                                                <td><label>Ulica:</label></td>
                                                <td class="client_info" id="client_info_correspond_street"></td>
                                            </tr>
                                            <tr class="active">
                                                <Td colspan="2">
                                                    <span class="sm-title">Dane kontaktowe:</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Telefon:</label></td>
                                                <td class="client_info" id="client_info_phone"></td>
                                            </tr>
                                            <tr>
                                                <td><label>Email:</label></td>
                                                <td class="client_info" id="client_info_email"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="height bg-primary"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="page-header marg-top-min">Dane nowej polisy</h4>
                        </div>
                        {{ Form::hidden('user_id', Auth::user()->id) }}
                        {{ Form::hidden('leasing_agreement_id', $leasingAgreement->id) }}
                        <div class="col-sm-12 ">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nr umowy</label>
                                <div class="col-sm-8">
                                    <input value="{{ $leasingAgreement->nr_contract }}/cesja" class="form-control" name="nr_contract"  placeholder="numer umowy">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nr zgłoszenia</label>
                                <div class="col-sm-8">
                                    <input value="{{ Auth::user()->insurances_global_nr }}" class="form-control" name="notification_number"  placeholder="numer zgłoszenia" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Utwórz cesję',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa dodawanie cesji...'))  }}
                        </div>
                    </div>
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
            $( "form#page-form" ).submit(function(e) {

                var $btn = $('#form_submit').button('loading');
                if(! $('#page-form').valid()) {
                    e.preventDefault();
                    $btn.button('reset');
                    return false;
                }

                if($('#client_id').val() == '')
                {
                    e.preventDefault();
                    $.notify({
                        icon: "fa fa-exclamation-triangle",
                        message: "prosze wybrać nowego leasingobiorcę"
                    },{
                        type: 'danger',
                        placement: {
                            from: 'bottom',
                            align: 'right'
                        },
                        delay: 5000,
                        timer: 500
                    });
                    $btn.button('reset');
                    return false;
                }
                return true;
            });

            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,  changeMonth: true,changeYear: true, dateFormat: "yy-mm-dd",
                onClose: function( selectedDate ) {
                    if($(this).attr('date-opt') == 'from'){
                        $( '#date_to' ).datepicker( "option", "minDate", selectedDate );
                    }else if( $(this).attr('date-opt') == 'to' ) {
                        $( '#date_from' ).datepicker("option", "maxDate", selectedDate);
                    }
                }
            });

            $('#client_name, #client_NIP, #client_REGON').autocomplete({
                source: function( request, response ) {
                    var input = this.element;
                    var col_name = $(input).attr('name');
                    $.ajax({
                        url: "{{ URL::to('insurances/info-insurances/search-client') }}",
                        data: {
                            id_insurance: "{{ $leasingAgreement->client_id }}",
                            col_name: col_name,
                            term: request.term,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            if(data.length > 0) {
                                $('#searching_info').text('').hide();
                                response($.map(data, function (item) {
                                    return item;
                                }));
                            }else{
                                $('#searching_info').text('Brak leasingobiorców o podanych parametrach wyszukiwania.').show();
                            }
                        }
                    });
                },
                minLength: 3,
                select: function(event, ui) {
                    var client_id = ui.item.id;
                    $('#client_id').val(client_id);
                    $.ajax({
                        url: "{{ URL::to('insurances/info-insurances/client-info') }}",
                        data: {
                            client_id: client_id,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        type: "GET",
                        success: function( data ) {
                            $.each( data, function( key, value ) {
                                $('#client_info_'+key).html(value);
                            });
                            $('#client_info_container').show();
                        }
                    });
                },
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                }
            }).on('keyup', function(e){
                //no eneter or esc
                if (e.which != 13 && e.which != 27) {
                    $('#client_info_container').hide();
                    $('.client_info').html('');
                    $('#searching_info').hide().html('');
                    $('#client_id').val('');
                }

            });

            $('#modal').on('click', '#addClient', function(){
                var $btn = $(this).button('loading');
                if($('#dialog-form').valid()) {
                    $.ajax({
                        type: "POST",
                        url: $('#dialog-form').prop('action'),
                        data: $('#dialog-form').serialize(),
                        assync: false,
                        cache: false,
                        success: function (data) {
                            if (data.status == 'success'){
                                $.each( data.client, function( key, value ) {
                                    $('#client_info_'+key).html(value);
                                });
                                $('#searching_info').text('').hide();
                                $('#client_id').val(data.client.id);
                                $('#client_info_container').show();
                            }else if (data.status == 'error'){
                                $.notify({
                                    icon: "fa fa-minus",
                                    message: data.msg
                                },{
                                    type: 'danger',
                                    placement: {
                                        from: 'bottom',
                                        align: 'right'
                                    },
                                    delay: 2500,
                                    timer: 500
                                });
                            }

                            $('#modal').modal('hide');
                            $('#modal .modal-content').html('');
                        },
                        dataType: 'json'
                    });
                }else {
                    $btn.button('reset');
                }
                return false;
            });

        });
    </script>
    @include('insurances.manage.partials.check-owner')
@stop