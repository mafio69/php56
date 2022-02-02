@extends('layouts.main')

@section('header')

    Importowanie wznowień umów

    <div class="pull-right">
        <a href="{{ URL::to('insurances/manage/index' ) }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 text-center">
            <div class="parsing-doc-progress" id="parsing-doc-progress">
                <div class="alert alert-info" role="alert">
                    <h3>Trwa przetwarzanie pliku</h3>
                    <i class="fa fa-cog fa-spin"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 ">
            <div class="alert alert-danger text-center" id="result-alert" style="display: none;"role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span id="alert-msg"></span>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 ">
            <div class="alert alert-success text-center" id="result-success" style="display: none;"role="alert">
                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                <span id="success-msg"></span>
            </div>
        </div>
        {{ Form::open(array('url' => URL::to('insurances/store/add-resume'), 'id' => 'page-form' )) }}
        <input type="hidden" name="filename" value="{{ $filename }}"/>
        <div class="col-sm-12 " id="insurances-container" style="display: none;">
            <div class="page-header">
                <h3 class="text-center">Wczytane wznowienia:</h3>
            </div>
            <div class="page-header" id="with_existing_insurance-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-primary">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseWithExistingInsurance" aria-expanded="false" aria-controls="collapseOne">
                                Wznowienia na istniejących umowach <span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseWithExistingInsurance" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                    <th>Nr umowy</th>
                                    <th>Ważność obecnej polisy</th>
                                    <Th>Wart. netto pożyczki</Th>
                                    <th>Wart. brutto</th>
                                    <th>Stawka</th>
                                    <th>Składka</th>
                                    <th>Ilość rat</th>
                                    <Th>Leasingobiorca</Th>
                                    <th>NIP</th>
                                    <th></th>
                                    <Th>oznacz do wznowienia</Th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <div class="pull-right legend">
                                    <p class="bg-danger" style="padding: 10px;">Obecna polisa jest jeszcze w aktualna.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="page-header" id="in_archive-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-warning">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseInArchiveInsurance" aria-expanded="false" aria-controls="collapseOne">
                                Wznowienia na umowach w archiwum<span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseInArchiveInsurance" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                    <th>Nr umowy</th>
                                    <Th>Wart. netto pożyczki</Th>
                                    <th>Wart. brutto</th>
                                    <th>Stawka</th>
                                    <th>Składka</th>
                                    <th>Ilość rat</th>
                                    <Th>Leasingobiorca</Th>
                                    <th>NIP</th>
                                    <th></th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-header" id="without_existing_insurance-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-warning">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseWithoutExistingInsurance" aria-expanded="false" aria-controls="collapseOne">
                                Umowy leasingowe, dla których nie zainicjowano umów ubezpieczeniowych  <span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseWithoutExistingInsurance" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                    <th>Nr umowy</th>
                                    <Th>Wart. netto pożyczki</Th>
                                    <th>Wart. brutto</th>
                                    <th>Stawka</th>
                                    <th>Składka</th>
                                    <th>Ilość rat</th>
                                    <Th>Leasingobiorca</Th>
                                    <th>NIP</th>
                                    <th></th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-header" id="missing_agreements-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-danger">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseMissingAgreements" aria-expanded="false" aria-controls="collapseOne">
                                Wznowienia nie posiadające dopasowanych umów leasingowych w systemie <span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseMissingAgreements" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                    <th>Nr umowy</th>
                                    <Th>Wart. netto pożyczki</Th>
                                    <th>Wart. brutto</th>
                                    <th>Stawka</th>
                                    <th>Składka</th>
                                    <th>Ilość rat</th>
                                    <Th>Leasingobiorca</Th>
                                    <th>NIP</th>
                                    <th></th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row marg-btm">
                <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
                    {{ Form::submit('Zapisz',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;', 'data-loading-text' => 'Trwa zapisywanie wznowień...'))  }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                type: "POST",
                url: "{{ URL::to('insurances/upload/parse', [$filename, 'resume'])  }}",
                data: {_token: $('input[name="_token"]').val()},
                assync:false,
                cache:false,
                dataType: 'json',
                success: function( data ) {
                    $('#parsing-doc-progress').hide();
                    if(data.status == 'error'){
                        $('#alert-msg').html(data.msg);
                        $('#result-alert').show();
                    }else if(data.status == 'success'){
                        $('#success-msg').html(data.msg);
                        $('#result-success').show();
                        $('#insurances-container').show();

                        if(isset(data.parsedData.with_existing_insurance)){
                            $.each(data.parsedData.with_existing_insurance, function(index, value) {
                                $('#with_existing_insurance-container table > tbody:last').append(value);
                            });
                            $('#with_existing_insurance-container span.counted-agreements').html(data.parsedData.with_existing_insurance.length);
                            $('#with_existing_insurance-container').show();
                        }

                        if(isset(data.parsedData.without_existing_insurance)){
                            $.each(data.parsedData.without_existing_insurance, function(index, value) {
                                $('#without_existing_insurance-container table > tbody:last').append(value);
                            });
                            $('#without_existing_insurance-container span.counted-agreements').html(data.parsedData.without_existing_insurance.length);
                            $('#without_existing_insurance-container').show();
                        }

                        if(isset(data.parsedData.missing_agreements)){
                            $.each(data.parsedData.missing_agreements, function(index, value) {
                                $('#missing_agreements-container table > tbody:last').append(value);
                            });
                            $('#missing_agreements-container span.counted-agreements').html(data.parsedData.missing_agreements.length);
                            $('#missing_agreements-container').show();
                        }

                        if(isset(data.parsedData.in_archive)){
                            $.each(data.parsedData.in_archive, function(index, value) {
                                $('#in_archive-container table > tbody:last').append(value);
                            });
                            $('#in_archive-container span.counted-agreements').html(data.parsedData.in_archive.length);
                            $('#in_archive-container').show();
                        }

                        $('.btn-popover').popover({
                            'html' : true,
                            'placement' : 'left',
                            'template' : '<div class="popover custom-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                        });
                    }
                },
                error: function(){
                    $('#parsing-doc-progress').hide();
                    $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                    $('#result-alert').show();
                }

            });
        });
    </script>
@stop

