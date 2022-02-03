@extends('layouts.main')

@section('header')
    Zarządzanie nieobecnościami
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
                    <form action="{{ URL::to('tasks/excludes/store') }}" class="page-form" method="post" id="page-form">
                        {{ Form::token() }}
                        <div class="row" id="search_guardian_container" >
                            <div class="form-group">
                                <label class="col-sm-3 col-md-2 col-lg-3 text-right control-label">Wyszukaj pracownika po loginie lub nazwisku:</label>
                                <div class="col-sm-9 col-md-8 col-lg-6">
                                    {{ Form::text('search_guardian', '', array('class' => 'form-control required', 'required', 'id' => 'search_guardian', 'placeholder' => 'wprowadź login lub nazwisko pracownika'))  }}
                                    {{ Form::hidden('user_id') }}
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-8 col-md-offset-2 alert alert-danger" role="alert" id="guardian_alert" style="display: none;">Nie znaleziono pasującego pracownika w bazie</div>
                            <div class="col-sm-12">
                                <hr>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-md-2 col-lg-3 text-right  control-label">Login:</label>
                            <div class="col-sm-9 col-md-8 col-lg-6">
                                {{ Form::input('text', 'searched_login', null, ['id' => 'searched_login', 'class' => 'form-control ', 'readonly'])}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-md-2 col-lg-3 text-right control-label">Nazwisko:</label>
                            <div class="col-sm-9 col-md-8 col-lg-6">
                                {{ Form::input('text', 'searched_name', null, ['id' => 'searched_name', 'class' => 'form-control ', 'readonly'])}}
                            </div>
                        </div>
                        <div class="orw">
                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <input type="hidden" name="days">
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
                                {{ Form::submit('Wprowadź',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-block off-disable', 'data-loading-text' => 'Trwa wprowadzanie...'))  }}
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
            $('#search_guardian').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "{{ url('tasks/assignments/search') }}",
                        data: {
                            term: request.term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        type: "POST",
                        success: function( data ) {
                            if (jQuery.isEmptyObject(data))
                                $('#guardian_alert').show();

                            response( $.map( data, function( item ) {
                                return item;
                            }));
                        }
                    });
                },
                minLength: 2,
                open: function(event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function(event, ui) {
                    if(ui.item.id != $('input[name="user_id"]').val())
                    {
                        $('input[name="user_id"]').val(ui.item.id);
                        $('#searched_login').val(ui.item.login);
                        $('#searched_name').val(ui.item.name);
                    }
                }
            }).bind("keypress", function(e) {
                if(e.which == 13){
                    setTimeout(function(){
                        $('#search_guardian').focusout();
                    },500);
                }else{
                    $('input[name="user_id"]').val('');
                    $('#searched_login').val('');
                    $('#searched_name').val('');
                    $('#guardian_alert').hide();
                }
            });


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


            var days = [];

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
