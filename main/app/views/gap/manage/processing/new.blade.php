@extends('layouts.main')

@section('header')

    Importowanie nowych umów

    <div class="pull-right">
        <a href="{{ url('gap/agreements/new') }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
      <div class="page-header" id="patern-container">
          <div class="panel-group" id="patern" role="tablist" aria-multiselectable="true">
              <div class="panel panel-primary">
                  <div class="panel-heading pointer" role="tab" id="headingOne">
                      <h4 class="panel-title">
                          Schemat odczytu pliku <span class="small">Prosimy o weryfikację</span>
                      </h4>
                  </div>
                  <form id="patern_form">
                  <div class="panel-body">
                          <table class="table table-condensed table-hover">
                              <thead>
                                  <th></th>
                                  <th>Import kolumna</th>
                                  <Th>Import nagłówek</Th>
                                  <th>Przykład 1</th>
                                  <th>Przykład 2</th>
                                  <th>Wartość w systemie</th>
                                  <th></th>
                              </thead>
                              <tbody>
                                @foreach($results['parsedData'] as $key => $result)
                                <tr>
                                    <td>
                                      @if(isset($result['danger']))
                                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                      @endif
                                    </td>
                                    <td>
                                      {{$key}}
                                    </td>
                                    @for($i=1; $i<=3; $i++)
                                    <td>
                                      @if(isset($result['val'][$i]))
                                        {{$result['val'][$i]}}
                                      @endif
                                    </td>
                                    @endfor
                                    <td>
                                      {{Form::select('patern['.$key.']',$paterns,(isset($result['code'])&&$result['code']) ? $result['code'] : 0 ,array('class'=>'from-contorl paterns','data-column'=>$key))}}
                                    </td>
                                </tr>
                                @endforeach

                              </tbody>
                          </table>
                    </div>
                    </form>
              </div>
          </div>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 ">
            <div class="alert alert-danger text-center" id="result-alert" style="display: none;"role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span id="alert-msg"></span>
            </div>
        </div>
      </div>
      <div class="row marg-btm">
          <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
              <span class="btn btn-primary btn-lg" id="start_import">Rozpocznij import</span>
          </div>
      </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 text-center">
            <div class="parsing-doc-progress" id="parsing-doc-progress"  style="display:none;">
                <div class="alert alert-info" role="alert">
                    <h3>Trwa przetwarzanie pliku</h3>
                    <i class="fa fa-cog fa-spin"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 ">
            <div class="alert alert-success text-center" id="result-success" style="display: none;"role="alert">
                <span class="glyphicon glyphicon-ok pull-right" aria-hidden="true"></span>
                <span id="success-msg"></span>
            </div>
        </div>
        {{ Form::open(array('url' => url('/gap/store/addNew'), 'id' => 'page-form' )) }}
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
                                Nowe umowy <span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseNewInsurances" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>

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
                                Umowy, które istnieją już w systemie <span class="badge counted-agreements">0</span>
                                <i class="fa fa-arrows-v pull-right"></i>
                            </h4>
                        </div>
                        <div id="collapseExistInsurances" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <table class="table table-condensed table-hover">
                                    <thead>

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
          $('.paterns').change(function(){
            $(this).parents('tr').removeClass('danger');
          })
          $('#start_import').click(function(){
            $(this).hide();
            $('#result-alert').hide();
            $('#parsing-doc-progress').show();
            var paterns = {};
            $('.paterns').each(function(){
              paterns[$(this).data('column')]=$(this).children('option:selected').val();
            })
            $.ajax({
                type: "POST",
                url: "{{ url('gap/upload/parse/new/'.$filename)}}",
                data: {_token: $('input[name="_token"]').val(), paterns: paterns},
                assync:false,
                cache:false,
                dataType: 'json',
                success: function( data ) {
                    $('#parsing-doc-progress').hide();
                    if(data.status == 'error'){
                        $('#alert-msg').html(data.msg);
                        $('#result-alert').show();
                        $('#parsing-doc-progress').hide();
                    }else if(data.status == 'error_patern'){
                        $('#alert-msg').html(data.msg);
                        $.each(data.data,function(){
                          $('[data-column="'+this+'"]').parents('tr').addClass('danger');
                        });
                        $('#result-alert').show();
                        $('#start_import').show();
                        $('#parsing-doc-progress').hide();
                    }else if(data.status == 'success'){
                        $('#success-msg').html(data.msg);
                        $('#result-success').show();
                        $('#insurances-container').show();
                        $('#patern-container').hide();

                        if(isset(data.parsedData.new)){
                            $('#new-insurances-container table > thead').html(data.parsedData.new[0]);
                            $.each(data.parsedData.new, function(index, value) {
                               if(index>0)
                                  $('#new-insurances-container table > tbody:last').append(value);
                            });
                            $('#new-insurances-container span.counted-agreements').html(data.parsedData.new.length-1);
                            $('#new-insurances-container').show();
                        }

                        if(isset(data.parsedData.exist)){
                            $('#exist-insurances-container table > thead').html(data.parsedData.exist[0]);
                            $.each(data.parsedData.exist, function(index, value) {
                              if(index>0)
                                $('#exist-insurances-container table > tbody:last').append(value);
                            });
                            $('#exist-insurances-container span.counted-agreements').html(data.parsedData.exist.length-1);
                            $('#exist-insurances-container').show();
                        }

                    }
                },
                error: function(data){
                    $('#parsing-doc-progress').hide();
                    $('#alert-msg').html('Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.');
                    $('#result-alert').show();
                }

            });
          });
        });
    </script>
@stop
