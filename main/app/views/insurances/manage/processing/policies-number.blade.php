@extends('layouts.main')

@section('header')

    Importowanie numerów polis

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
                        <div class="row marg-top">
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::button('Rozpocznij import',  array('class' => 'btn btn-primary btn-block', 'id' => 'start-import', 'data-loading-text' => 'Trwa importowanie umów...'))  }}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        @include('insurances.manage.processing.report.policies')

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

        function parsePolicies()
        {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse-policies', [$filename])  }}",
                data: import_data,
                assync:false,
                cache:false,
                dataType: 'json',
                beforeSend: function(  ) {
                    $('#parse-info-container').slideUp();
                    $('#policies-container').show();
                },
                success: function( data ) {
                    console.log(data);
                    $('.progress-container').hide();

                    $('#policies-container .parsed span.badge').html(data.parsed.length);
                    $.each(data.parsed, function(i, item) {
                        $('#policies-container .parsed ol').append('<li>nr polisy: '+item.policy_number + '; nr umowy:'+ item.contract_number +'</li>');
                    });

                    $('#policies-container .existing span.badge').html(data.existing.length);
                    $.each(data.existing, function(i, item) {
                        $('#policies-container .existing ol').append('<li>nr polisy: '+item.policy_number + '; nr umowy:'+ item.contract_number +'</li>');
                    });

                    $('#policies-container .missing span.badge').html(data.missing.length);
                    $.each(data.missing, function(i, item) {
                        $('#policies-container .missing ol').append('<li>nr polisy: '+item.policy_number + '; nr umowy:'+ item.contract_number +'</li>');
                    });

                    $('.policies-container').show();
                    $('#complete-btn').show();
                },
                error: function(request){
                    if(request.status == 406)
                    {
                        $('#policies-container .panel').hide();
                        $('#policies-container .import-warning .alert-msg').html(request.responseText);
                        $('#policies-container .import-warning').show();
                    }else {
                        $('#policies-container').hide();
                        $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                        $('#result-alert').show();
                    }
                }
            });
        }


        $(document).ready(function(){
            $('#start-import').on('click', function(){
                $('#parse-info-form').validate();
                if($('#parse-info-form').valid()) {
                    $(this).button('loading');
                    import_data = $('#parse-info-form').serialize();
                    parsePolicies();
                }
            });
        });
    </script>
@stop

