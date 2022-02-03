@extends('layouts.main')

@section('header')

    Importowanie raportu umów

    <div class="pull-right">
        <a href="{{ URL::to('insurances/manage/index' ) }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2" id="parse-info-container">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('id' => 'parse-info-form')) }}
                        <input type="hidden" name="filename" value="{{ $filename }}"/>
                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Ubezpieczyciel:</label>
                                {{ Form::select('insurance_company_id', $insuranceCompanies, 7, ['class' => 'form-control'])}}
                            </div>
                        </div>
                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Spółka:</label>
                                {{ Form::select('owner_id', $owners, 3, ['class' => 'form-control'])}}
                            </div>
                        </div>
                        <div class="row marg-btm" >
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Nr zgłoszenia:</label>
                                <input value="{{ date('m/Y') }}" type="text" name="notification_number" class="form-control required datePicker" placeholder="wybierz datę" required >
                            </div>
                        </div>
                        <div class="row marg-btm">
                            <div class="col-sm-12 col-md-8 col-md-offset-2">
                                <label>Tryb płatności:</label>
                                {{ Form::select('leasing_agreement_payment_way_id', $leasingAgreementPaymentWays, 1, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="row marg-top">
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::button('Rozpocznij import',  array('class' => 'btn btn-primary btn-block', 'id' => 'start-import', 'data-loading-text' => 'Trwa importowanie umów...'))  }}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        @include('insurances.manage.processing.report.new-leasing')
        @include('insurances.manage.processing.report.new-loan')
        @include('insurances.manage.processing.report.resume-leasing')
        @include('insurances.manage.processing.report.resume-loan')
        @include('insurances.manage.processing.report.resume-2months')
        @include('insurances.manage.processing.report.refund')

        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 ">
            <div class="alert alert-danger text-center" id="result-alert" style="display: none;"role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span id="alert-msg"></span>
            </div>
        </div>

        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 ">
            <a class="btn btn-success btn-block" id="complete-btn" style="display: none;" href="/insurances/manage/inprogress">
                <i class="fa fa-check"></i>
                Import zakończył się sukcesem. Kliknij aby powrócić...
            </a>
        </div>

    </div>
@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        var import_data;

        function parseNewLeasing()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-report', [$filename, 'parseNewLeasingAgreements'])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#parse-info-container').slideUp();
                    $('#new-leasing-container').show();
                },
                success: function( data ) {
                    $('#new-leasing-container .progress-container').hide();

                    $('#new-leasing-container .parsed span.badge').html(data.parsedAgreements.length);
                    $.each(data.parsedAgreements, function(i, item) {
                        $('#new-leasing-container .parsed ol').append("<li>"+item.nr_contract+"</li>");
                    });

                    $('#new-leasing-container .existing span.badge').html(data.existingAgreements.length);
                    $.each(data.existingAgreements, function(i, item) {
                        $('#new-leasing-container .existing ol').append('<li>'+item+'</li>');
                    });
                    $('#new-leasing-container .agreements-container').show();

                    parseNewLoan();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#new-leasing-container .panel').hide();
                        $('#new-leasing-container .import-warning .alert-msg').html(request.responseText);
                        $('#new-leasing-container .import-warning').show();
                        parseNewLoan();
                    }else {
                        $('#new-leasing-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }

        function parseNewLoan()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-report', [$filename, 'parseNewLoanAgreements'])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#new-loan-container').show();
                    $('html, body').animate({
                        scrollTop: $("#new-loan-container").first().offset().top
                    }, 500);
                },
                success: function( data ) {
                    $('#new-loan-container .progress-container').hide();

                    $('#new-loan-container .parsed span.badge').html(data.parsedAgreements.length);
                    $.each(data.parsedAgreements, function(i, item) {
                        $('#new-loan-container .parsed ol').append("<li>"+item.nr_contract+"</li>");
                    });

                    $('#new-loan-container .existing span.badge').html(data.existingAgreements.length);
                    $.each(data.existingAgreements, function(i, item) {
                        $('#new-loan-container .existing ol').append('<li>'+item+'</li>');
                    });
                    $('#new-loan-container .agreements-container').show();

                    parseResumeLeasing();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#new-loan-container .panel').hide();
                        $('#new-loan-container .import-warning .alert-msg').html(request.responseText);
                        $('#new-loan-container .import-warning').show();
                        parseResumeLeasing();
                    }else {
                        $('#new-loan-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }

        function parseResumeLeasing()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-report', [$filename, 'parseResumeLeasingAgreements'])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#resume-leasing-container').show();
                    $('html, body').animate({
                        scrollTop: $("#resume-leasing-container").first().offset().top
                    }, 500);
                },
                success: function( data ) {
                    $('#resume-leasing-container .progress-container').hide();

                    $('#resume-leasing-container .parsed span.badge').html(data.parsedAgreements.length);
                    $.each(data.parsedAgreements, function(i, item) {
                        $('#resume-leasing-container .parsed ol').append("<li>"+item.nr_contract+"</li>");
                    });

                    $('#resume-leasing-container .existing span.badge').html(data.existingAgreements.length);
                    $.each(data.existingAgreements, function(i, item) {
                        $('#resume-leasing-container .existing ol').append('<li>'+item+'</li>');
                    });

                    $('#resume-leasing-container .alreadyResumed span.badge').html(data.alreadyResumedAgreements.length);
                    $.each(data.alreadyResumedAgreements, function(i, item) {
                        $('#resume-leasing-container .alreadyResumed ol').append('<li>'+item+'</li>');
                    });

                    $('#resume-leasing-container .agreements-container').show();

                    parseResumeLoan();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#resume-leasing-container .panel').hide();
                        $('#resume-leasing-container .import-warning .alert-msg').html(request.responseText);
                        $('#resume-leasing-container .import-warning').show();
                        parseResumeLoan();
                    }else {
                        $('#resume-leasing-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }

        function parseResumeLoan()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-report', [$filename, 'parseResumeLoanAgreements'])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#resume-loan-container').show();
                    $('html, body').animate({
                        scrollTop: $("#resume-loan-container").first().offset().top
                    }, 500);
                },
                success: function( data ) {
                    $('#resume-loan-container .progress-container').hide();

                    $('#resume-loan-container .parsed span.badge').html(data.parsedAgreements.length);
                    $.each(data.parsedAgreements, function(i, item) {
                        $('#resume-loan-container .parsed ol').append("<li>"+item.nr_contract+"</li>");
                    });

                    $('#resume-loan-container .existing span.badge').html(data.existingAgreements.length);
                    $.each(data.existingAgreements, function(i, item) {
                        $('#resume-loan-container .existing ol').append('<li>'+item+'</li>');
                    });

                    $('#resume-loan-container .alreadyResumed span.badge').html(data.alreadyResumedAgreements.length);
                    $.each(data.alreadyResumedAgreements, function(i, item) {
                        $('#resume-loan-container .alreadyResumed ol').append('<li>'+item+'</li>');
                    });

                    $('#resume-loan-container .agreements-container').show();

                    parseResume2months();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#resume-loan-container .panel').hide();
                        $('#resume-loan-container .import-warning .alert-msg').html(request.responseText);
                        $('#resume-loan-container .import-warning').show();
                        parseResume2months();
                    }else {
                        $('#resume-loan-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }

        function parseResume2months()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-report', [$filename, 'parseResume2MonthsAgreements'])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#resume-2months-container').show();
                    $('html, body').animate({
                        scrollTop: $("#resume-2months-container").first().offset().top
                    }, 500);
                },
                success: function( data ) {
                    $('#resume-2months-container .progress-container').hide();

                    $('#resume-2months-container .parsed span.badge').html(data.parsedAgreements.length);
                    $.each(data.parsedAgreements, function(i, item) {
                        $('#resume-2months-container .parsed ol').append("<li>"+item.nr_contract+"</li>");
                    });

                    $('#resume-2months-container .existing span.badge').html(data.existingAgreements.length);
                    $.each(data.existingAgreements, function(i, item) {
                        $('#resume-2months-container .existing ol').append('<li>'+item+'</li>');
                    });

                    $('#resume-2months-container .alreadyResumed span.badge').html(data.alreadyResumedAgreements.length);
                    $.each(data.alreadyResumedAgreements, function(i, item) {
                        $('#resume-2months-container .alreadyResumed ol').append('<li>'+item+'</li>');
                    });

                    $('#resume-2months-container .agreements-container').show();

                    parseRefund();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#resume-2months-container .panel').hide();
                        $('#resume-2months-container .import-warning .alert-msg').html(request.responseText);
                        $('#resume-2months-container .import-warning').show();
                        parseRefund();
                    }else {
                        $('#resume-2months-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }

        function parseRefund()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-report', [$filename, 'parseRefundAgreements'])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#refund-container').show();
                    $('html, body').animate({
                        scrollTop: $("#refund-container").first().offset().top
                    }, 500);
                },
                success: function( data ) {
                    $('#refund-container .progress-container').hide();

                    $('#refund-container .parsed span.badge').html(data.existingAgreementsList.length);
                    $.each(data.existingAgreementsList, function(i, item) {
                        $('#refund-container .parsed ol').append("<li>"+item+"</li>");
                    });

                    $('#refund-container .unparsed span.badge').html(data.unparsedAgreementsList.length);
                    $.each(data.unparsedAgreementsList, function(i, item) {
                        $('#refund-container .unparsed ol').append('<li>'+item+'</li>');
                    });

                    $('#refund-container .alreadyArchived span.badge').html(data.alreadyArchivedAgreementsList.length);
                    $.each(data.alreadyArchivedAgreementsList, function(i, item) {
                        $('#refund-container .alreadyArchived ol').append('<li>'+item+'</li>');
                    });

                    $('#refund-container .agreements-container').show();

                    $('#complete-btn').show();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#refund-container .panel').hide();
                        $('#refund-container .import-warning .alert-msg').html(request.responseText);
                        $('#refund-container .import-warning').show();
                        $('#complete-btn').show();
                    }else {
                        $('#refund-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }

        $(document).ready(function(){
            $('.datePicker').datepicker({ showOtherMonths: true, selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: "mm/yy",
                showButtonPanel: true,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            });

            $('#start-import').on('click', function(){
                $('#parse-info-form').validate();
                if($('#parse-info-form').valid()) {
                    $(this).button('loading');
                    import_data = $('#parse-info-form').serialize();
                    parseNewLeasing();
                }
            });
        });
    </script>
@stop

