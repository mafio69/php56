@extends('layouts.main')

@section('header')

    Importowanie zestawienia umów

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
        {{ Form::open(array('url' => URL::to('insurances/store/add-collection' ), 'id' => 'page-form' )) }}
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
                                Przetworzone umowy <span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseNewInsurances" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                        <th>lp.</th>
                                        <th>Nr umowy</th>
                                        <th>Czy polisa</th>
                                        <th>Korekta</th>
                                        <th>Zmiana danych</th>
                                        <Th>Zwrot składki</Th>
                                        <th>Rodzaj polisy</th>
                                        <th>Finansujący</th>
                                        <th>Typ płatności</th>
                                        <th>Suma ubezp.</th>
                                        <th>Netto/brutto</th>
                                        <th>Miejsce ubezp.</th>
                                        <Th>Decyzja obciążenia</Th>
                                        <th>Grupa</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="collapseNewInsurancesTbody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row marg-btm">
                <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
                    {{ Form::submit('Zapisz',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-lg disabled', 'style' => 'width:400px; height: 50px;', 'data-loading-text' => 'Trwa importowanie produktów...'))  }}
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
                url: "{{ URL::to('insurances/upload/parse-collection', [$filename, 200])  }}",
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

                        if(isset(data.parsedData.agreements)){
                            $.each(data.parsedData.agreements, function(index, value) {
                                $('#new-insurances-container table > tbody#collapseNewInsurancesTbody:last').append(value);
                            });
                            $('#new-insurances-container span.counted-agreements').html(data.parsedData.agreements.length/2);
                            $('#new-insurances-container').show();
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

            $('#new-insurances-container').on('click', '.show_hide_insurances', function(){
                $('.insurance_row').hide();
                $(this).parent().parent().next('tr.insurance_row').show();
            });
        });
    </script>
@stop

