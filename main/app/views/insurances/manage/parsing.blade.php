@extends('layouts.main')

@section('header')

    Importowanie nowych umów

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
        {{ Form::open(array('url' => URL::to('insurances/upload/add-new' ), 'id' => 'page-form' )) }}
        <input type="hidden" name="filename" value="{{ $filename }}"/>
        <div class="col-sm-12 " id="insurances-container" style="display: none;">
            <div class="page-header">
                <h3 class="text-center">Wczytane umowy:</h3>
            </div>
            <div class="page-header" id="new-insurances-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-primary">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseNewInsurances" aria-expanded="false" aria-controls="collapseOne">
                                Nowe umowy <span class="badge">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseNewInsurances" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                    <th>Nr umowy</th>
                                    <Th>Wart. netto pożyczki</Th>
                                    <th>Wart. brutto</th>
                                    <th>Stawka</th>
                                    <th>Składka</th>
                                    <Th>Okres ubezp. od </Th>
                                    <th>Okres ubezp. do</th>
                                    <th>Ilość rat</th>
                                    <th>Data akceptacji</th>
                                    <Th>Leasingobiorca</Th>
                                    <th>NIP</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-header" id="exist-insurances-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-danger">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseExistInsurances" aria-expanded="false" aria-controls="collapseOne">
                                Umowy, które istnieją już w systemie <span class="badge">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseExistInsurances" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                    <th>Nr umowy</th>
                                    <Th>Wart. netto pożyczki</Th>
                                    <th>Wart. brutto</th>
                                    <th>Stawka</th>
                                    <th>Składka</th>
                                    <Th>Okres ubezp. od </Th>
                                    <th>Okres ubezp. do</th>
                                    <th>Ilość rat</th>
                                    <th>Data akceptacji</th>
                                    <Th>Leasingobiorca</Th>
                                    <th>NIP</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-header" id="mismatched-objects-container" style="display: none;">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-danger">
                        <div class="panel-heading pointer" role="tab" id="headingOne">
                            <h4 class="panel-title collapse-header" data-toggle="collapse" data-target="#collapseMismatchedObjects" aria-expanded="false" aria-controls="collapseOne">
                                Obiekty, które nie zostały dopasowane do umowy <span class="badge">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseMismatchedObjects" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                    <th>lp.</th>
                                        <th>Przedmiot leasingu</th>
                                        <Th>Wart.  z faktury netto przedm. umowy pożyczki</Th>
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
                    {{ Form::submit('Zapisz',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;', 'data-loading-text' => 'Trwa importowanie produktów...'))  }}
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
                url: "{{ URL::to('insurances/upload/parse', [$filename])  }}",
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

                        if(isset(data.parsedData.new)){
                            $.each(data.parsedData.new, function(index, value) {
                                $('#new-insurances-container table > tbody:last').append(value);
                            });
                            $('#new-insurances-container span.badge').html(data.parsedData.new.length);
                            $('#new-insurances-container').show();
                        }

                        if(isset(data.parsedData.exist)){
                            $.each(data.parsedData.exist, function(index, value) {
                                $('#exist-insurances-container table > tbody:last').append(value);
                            });
                            $('#exist-insurances-container span.badge').html(data.parsedData.exist.length);
                            $('#exist-insurances-container').show();
                        }

                        if(isset(data.parsedData.mismatched)){
                            $.each(data.parsedData.mismatched, function(index, value) {
                                $('#mismatched-objects-container table > tbody:last').append(value);
                            });
                            $('#mismatched-objects-container span.badge').html(data.parsedData.mismatched.length);
                            $('#mismatched-objects-container').show();
                        }
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

