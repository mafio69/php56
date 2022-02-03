@extends('layouts.main')

@section('header')
    Zarządzanie nieobecnościami {{ $absence_user ->name }}
    <a href="{{ url('tasks/excludes') }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-ban fa-fw"></i> anuluj
    </a>
@endsection

@section('main')
    <style>
        #calendar .ui-state-busy a {background:#e6e6e6 !important;}
        #calendar .ui-state-free a {background:none !important;}
        #calendar .ui-state-free a.ui-state-hover {background:lightgrey !important;}
    </style>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <form action="{{ URL::to('tasks/excludes/update', [$absence_user->id]) }}" class="page-form" method="post" id="page-form">
                        {{ Form::token() }}
                        <div class="orw">
                            <input type="hidden" name="days" value="{{ implode(',', $absence_user->taskExcludes->lists('absence_formatted')) }}">
                            <div class="col-sm-12">
                                <h4>Kalendarz nieobecności:</h4>
                                <div id="calendar"></div>
                            </div>
                        </div>
                        <div class="row marg-top">
                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::submit('Zapisz',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-block off-disable', 'data-loading-text' => 'Trwa zapisywanie...'))  }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('headerJs')
    @parent

    <script>
        $(document).ready(function(){

            $( "form#page-form" ).submit(function(e) {
                let form_btn = $('#form_submit');
                form_btn.button('loading');

                if(! $('#page-form').valid()  ) {

                    setTimeout(function(){
                        form_btn.button('reset');
                    }, 100);

                    e.preventDefault();
                    return false;
                }
                return true;
            });


            var days = $('input[name="days"]').val().split(",");

            $('#calendar').datepicker({
                numberOfMonths: 3,
                showOtherMonths: false,
                selectOtherMonths: false,
                minDate: 0,
                onSelect: function(d) {
                    var i = $.inArray(d, days);

                    if (i == -1)
                        days.push(d);
                    else
                        days.splice(i, 1);

                    $('input[name="days"]').val(days.join(','))
                },
                beforeShowDay: function(d) {
                    return ([true, $.inArray($.datepicker.formatDate('yy-mm-dd', d), days) == -1 ? 'ui-state-free' : 'ui-state-busy']);
                }
            });
        });
    </script>
@endsection
