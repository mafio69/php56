@extends('layouts.main')

@section('header')
    DLS Majątek - Wyszukiwanie
@stop


@section('main')

    <div class="col-sm-12">
        <form class="form-horizontal" method="post" id="search-form" >
            {{ Form::token() }}
            <div class="row">
                <div class="col-sm-6 col-md-4 marg-top-min">
                    <div class="form-group form-group-sm">
                        <label class="col-sm-4 control-label ">Numer umowy:</label>
                        <div class="col-sm-8">
                            {{ Form::text('contract_number', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
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
            <div class="col-sm-12 vehicles-container">
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
                    url:'/dos/other/injuries/make/search-syjon',
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.container-loader').show();
                        $('.vehicles-container').html('<div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2 text-center">Trwa wyszukiwanie w SYJON...</div>');

                    }
                }).done(function(data){
                    if(data == 'empty results'){
                        searchInObjects();
                    }else {
                        $('.container-loader').hide();
                        $('.vehicles-container').html(data);
                        $('#search-contracts').button('reset');
                        loaded = 10;

                        $('html, body').animate({
                            scrollTop: $(".vehicles-container").first().offset().top
                        }, 500);
                    }
                });
            });

            function searchInObjects(){
                $.ajax({
                    url:'/dos/other/injuries/make/search-objects',
                    method: 'post',
                    data: $('#search-form').serialize(),
                    beforeSend: function () {
                        $('.vehicles-container').html('<div class="alert alert-warning col-sm-12 col-md-8 col-md-offset-2 text-center">Trwa wyszukiwanie w bazie AS...</div>');
                    }
                }).done(function(data){
                    if(data == 'empty results'){
                        $('.container-loader').hide();
                        $('.vehicles-container').html('<div class="alert alert-danger col-sm-12 col-md-8 col-md-offset-2 text-center">Nie dopasowano wyników wyszukiwania.</div>');
                    }else {
                        $('.container-loader').hide();
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
                    url:'/dos/other/injuries/make/load-next-syjon/'+loaded,
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
        });


    </script>

@stop
