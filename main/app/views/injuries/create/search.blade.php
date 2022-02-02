@extends('layouts.main')

@section('header')
    DLS Pojazdy - Wyszukiwanie w Syjon
@stop


@section('main')

    <div class="col-sm-12">
        <form class="form-horizontal" method="post" id="search-form" >
            {{ Form::token() }}
            <div class="row">
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm">
                        <label class="col-sm-4 control-label ">Numer rejestracyjny:</label>
                        <div class="col-sm-8">
                            {{ Form::text('registration', null, ['class' => 'form-control ', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm">
                        <label class="col-sm-4 control-label ">Numer umowy:</label>
                        <div class="col-sm-8">
                            {{ Form::text('contract_number', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6  col-md-4 marg-top-min">
                    <div class="form-group form-group-sm">
                        <label class="col-sm-4 control-label ">Numer VIN:</label>
                        <div class="col-sm-8">
                            {{ Form::text('vin', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm ">
                        <label class="col-sm-4 control-label ">Numer polisy:</label>
                        <div class="col-sm-8">
                            {{ Form::text('policy_number', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm ">
                        <label class="col-sm-4 control-label ">Kod klienta:</label>
                        <div class="col-sm-8">
                            {{ Form::text('code_client', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm ">
                        <label class="col-sm-4 control-label ">NIP firmy:</label>
                        <div class="col-sm-8">
                            {{ Form::text('nip_company', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm ">
                        <label class="col-sm-4 control-label ">Nazwa firmy:</label>
                        <div class="col-sm-8">
                            {{ Form::text('name_company', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group text-center">
                    <span class="btn btn-primary" id="search-contracts">
                        <i class="fa fa-search fa-fw"></i>
                        szukaj
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="container-alert">

            </div>
            <div class="col-sm-12 vehicles-container">
            </div>
            <div class="col-sm-12" id="map-container">

            </div>


            <div class="container-loader col-sm-12" style="display: none; z-index: 99999;">
                <h3 class="text-center">
                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                </h3>
            </div>
        </div>
    </div>


@stop


@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            var loaded = 0;

            $('#search-contracts').on('click', function(){
                $.ajax({
                    url:'/injuries/make/search-syjon-vehicles',
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.container-loader').show();
                        $('.container-alert').html('<div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2 text-center">Trwa wyszukiwanie w SYJON...</div>');
                        $('.vehicles-container').html('');
                        $('#map-container').html('');
                    }
                }).done(function(data){
                    if(data == 'empty results'){

                    }else {
                        $('.vehicles-container').html(data);
                        loaded = 10;
                    }
                    searchInVmanageVehicles();
                });
            });

            function searchInVmanageVehicles(){
                $.ajax({
                    url:'/injuries/make/search-vmanage-vehicles',
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.container-alert').html('<div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2 text-center">Trwa wyszukiwanie w bazie firm...</div>');
                    }
                }).done(function(data){
                    if(data == 'empty results' && loaded > 0) {
                        $('.container-loader').hide();
                        $('.container-alert').html('');
                        $('#search-contracts').button('reset');

                        $('html, body').animate({
                            scrollTop: $(".vehicles-container").first().offset().top
                        }, 500);
                    }else if(data == 'empty results'){
                        searchInNonAs();
                    }else {
                        $('.container-loader').hide();
                        $('.vehicles-container').append(data);
                        $('.container-alert').html('');
                        $('#search-contracts').button('reset');

                        $('html, body').animate({
                            scrollTop: $(".vehicles-container").first().offset().top
                        }, 500);
                    }
                });
            }

            function searchInNonAs(){
                $.ajax({
                    url:'/injuries/make/search-non-as-vehicles',
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.container-alert').html('<div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2 text-center">Trwa wyszukiwanie w szkodach AS...</div>');
                    }
                }).done(function(data){
                    if(data == 'empty results'){
                        searchInAs();
                    }else {
                        $('.container-loader').hide();
                        $('.container-alert').html('');
                        $('.vehicles-container').html(data);
                        $('#search-contracts').button('reset');

                        $('html, body').animate({
                            scrollTop: $(".vehicles-container").first().offset().top
                        }, 500);
                    }
                });
            }

            function searchInAs(){
                $.ajax({
                    url:'/injuries/make/search-as-vehicles',
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.container-alert').html('<div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2 text-center">Trwa wyszukiwanie w bazie AS...</div>');
                    }
                }).done(function(data){
                    if(data == 'empty results'){
                        $('.container-loader').hide();
                        $('.container-alert').html('<div class="alert alert-danger col-sm-12 col-md-8 col-md-offset-2 text-center">Nie dopasowano wyników wyszukiwania.</div>');
                        $('.vehicles-container').html('');
                    }else {
                        $('.container-loader').hide();
                        $('.container-alert').html('');
                        $('.vehicles-container').html(data);
                        $('#search-contracts').button('reset');

                        $('html, body').animate({
                            scrollTop: $(".vehicles-container").first().offset().top
                        }, 500);
                    }
                });
            }

            $('.vehicles-container').on('click', '#load-next', function () {
                var total = $(this).data('total');
                total = parseInt(total);

                $.ajax({
                    url:'/injuries/make/load-next-syjon-vehicles/'+loaded,
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.container-loader').show();
                    }
                }).done(function(data){
                    $('.container-loader').hide();
                    $('.table-contracts').append(data);
                });

                loaded += 10;

                if(loaded >= total)
                {
                    $(this).hide();
                }else{
                    $('.counter-loaded').html(loaded);
                    $(this).button('reset');
                }
            });

            $('#modal-lg').on('click', '#set-branch-special', function(){
                //    var $btn = $(this).button('loading');
                //  if($('#assign-branch-form').valid()) {
                $('.modal-open-lg-special').removeAttr("disabled");
                if($('#id_warsztat').val())
                    $('#branch_id').val($('#id_warsztat').val());
                else
                    $('#branch_id').val(0);
                if($('#dont_send_sms').is(':checked'))
                    $('#branch_dont_send_sms').val(1);
                else
                    $('#branch_dont_send_sms').val(0);
                $('#branch_text').text('Przypisany serwis');
                $('#branch_data').html($('#data_warsztat').html());
                $('#branch_data').show();
                $('#branch_text').show();
                $('#modal-lg').modal("hide");


                return false;
            });

            $('.vehicles-container').on('click', '.suggest-branch', function(){
                var vehicle_id = $(this).data('vehicle');
                var contract_id = $(this).data('contract');
                var vehicle_type = $(this).data('type');
                var contract_internal_agreement_id = $(this).data('agreement');
                var policy_id = $(this).data('policy');
                $.ajax({
                    url:'/injuries/make/load-map-suggestion',
                    method: 'post',
                    data: {vehicle_id: vehicle_id, contract_internal_agreement_id: contract_internal_agreement_id, policy_id: policy_id, contract_id: contract_id, _token: $('input[name="_token"]').val(), vehicle_type: vehicle_type},
                    dataType: 'html',
                    beforeSend: function () {
                        $('.container-loader').show();
                    }
                }).done(function(data){
                    $('.container-loader').hide();
                    $('#map-container').html(data);
                });
            });
        });

    </script>

@stop
