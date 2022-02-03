@extends('layouts.main')

@section('header')
    Generowanie raportów DLS Majątek
@stop

@section('main')
    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport zleceń</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('dos/other/reports/orders'), 'class' => 'page-form', 'id' => 'orders' )) }}
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'order_date_from', 'datepicker_id_to' => 'order_date_to'))
                    @include('reports.partials.submit')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Raport zleceń zakończonych</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            {{ Form::open(array('url' => URL::to('dos/other/reports/completed-orders'), 'class' => 'page-form', 'id' => 'completed-orders' )) }}
                            @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'completed_order_date_from', 'datepicker_id_to' => 'completed_order_date_to'))
                            @include('reports.partials.submit')
                            {{ Form::close() }}
                        </div>
                        <div class="col-md-4 text-center">
                            <a href="/dos/other/reports/excluded" class="btn btn-info btn-block">
                                <i class="fa fa-search fa-fw"></i>
                                szkody wykluczone z raportu
                                <span class="badge">{{ $skipped }}</span>
                            </a>

                            <span class="btn btn-primary marg-top fileinput-button let_disable">
                                    <i class="fa fa-upload"></i> Wgraj zestawienie</a>
                                <form id="fileupload" method="POST">
                                        {{ Form::token() }}
                                        <input type="file" name="file" >
                                    </form>
                                </span>

                            <div id="progress" class="progress marg-top" >
                                <div id="progress-bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
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
                }else{
                    $('.form_submit').button('reset');
                }

                return false; //is superfluous, but I put it here as a fallback
            });

            $('#fileupload').fileupload({
                singleFileUploads: true,
                url: "{{ URL::to('dos/other/reports/upload-list') }}",
                dataType: 'json',
                add: function (e, data) {
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    if (data.autoUpload || (data.autoUpload !== false &&
                        $(this).fileupload('option', 'autoUpload'))) {
                        data.process().done(function () {
                            data.submit();
                        });
                    }

                },
                done: function (e, data) {
                    var response = data.result;
                    if(response.status == 'success') location.reload();
                    else alert(response.msg);
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress-bar').css(
                        'width',
                        progress + '%'
                    ).attr('aria-valuenow', progress).html(progress + '%');
                }
            });
        });
    </script>
@stop

