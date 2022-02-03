@extends('layouts.main')

@section('header')

    Wprowadzanie nowej umowy majątkowej

    <div class="pull-right">
        <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/create/store'), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                    <div class="row marg-btm">
                        @include('insurances.manage.create.partials.client')
                        @include('insurances.manage.create.partials.agreement-info')
                        @include('insurances.manage.create.partials.objects')
                    </div>
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Wprowadź umowę',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa wprowadzanie umowy...'))  }}
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
                        message: "prosze wybrać leasingobiorcę"
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

            $('.date').datepicker({ showOtherMonths: true, selectOtherMonths: true,
                changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd"
            });
        });
    </script>

@stop