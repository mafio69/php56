@extends('layouts.main')

@section('header')

    Dodawanie opiekuna floty dla {{ $company->owner->name }} <small><i>( {{ $company->name }} )</i></small>

@stop

@section('main')

    {{ Form::open(array('url' => URL::action('VmanageCompanyGuardiansController@postStore', [$company->id]), 'class' => 'form-horizontal')) }}

    <div class="row marg-btm">
        <div class="checkbox pull-left marg-left">
            <label>
                <input type="checkbox" name="existing_guardian" id="existing_guardian"> Przypisz istniejącego opiekuna
            </label>
        </div>
        <div class="pull-right">
            <a href="{{ URL::action('VmanageCompanyGuardiansController@getIndex', [$company->id]) }}" class="btn btn-default">Anuluj</a>
        </div>
    </div>
    <div class="row" id="new_guardian_container">
        <h4 class="inline-header"><span>Dane nowego opiekuna:</span></h4>
        <div class="form-group @if ($errors->has('login')) has-error @endif">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Login:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::input('text', 'login', null, ['class' => 'form-control required', 'placeholder' => 'login'])}}
                @if ($errors->has('login')) <p class="help-block">{{ $errors->first('login') }}</p> @endif
            </div>
        </div>
        <div class="form-group @if ($errors->has('name')) has-error @endif">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Nazwisko:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::input('text', 'name', null, ['class' => 'form-control required', 'placeholder' => 'nazwisko'])}}
                @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
            </div>
        </div>
        <div class="form-group @if ($errors->has('password')) has-error @endif">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Hasło:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::password('password', ['class' => 'form-control required', 'placeholder' => 'hasło'])}}
                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
            </div>
        </div>
        <div class="form-group @if ($errors->has('password_confirm')) has-error @endif">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Powtórz hasło:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::password('password_confirm', ['class' => 'form-control required', 'placeholder' => 'powtórz hasło'])}}
                @if ($errors->has('password_confirm')) <p class="help-block">{{ $errors->first('password_confirm') }}</p> @endif
            </div>
        </div>
    </div>
    <div class="row" id="search_guardian_container" style="display: none;">
        <h4 class="inline-header"><span>Dane istniejącego opiekuna:</span></h4>
        <div class="form-group">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Wyszukaj opiekuna po loginie lub nazwisku:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::text('search_guardian', '', array('class' => 'form-control', 'id' => 'search_guardian', 'placeholder' => 'wprowadź login lub nazwisko opiekuna'))  }}
                {{ Form::hidden('user_id') }}
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-md-offset-2 alert alert-danger" role="alert" id="guardian_alert" style="display: none;">Nie znaleziono pasującego opiekuna w bazie</div>

        <div class="form-group">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Login:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::input('text', 'searched_login', null, ['id' => 'searched_login', 'class' => 'form-control ', 'readonly'])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 col-md-2 col-lg-3 control-label">Nazwisko:</label>
            <div class="col-sm-9 col-md-8 col-lg-6">
                {{ Form::input('text', 'searched_name', null, ['id' => 'searched_name', 'class' => 'form-control ', 'readonly'])}}
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <h4 class="inline-header "></h4>
        <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
            {{ Form::submit('Zapisz',  array('id'=>'store', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;'))  }}
        </div>
    </div>
    {{ Form::close() }}

@stop

@section('headerJs')
    @parent
    <script type="text/javascript" >
        $(document).ready(function(){
            $("form").submit(function(e) {
                var self = this;
                e.preventDefault();
                var btn = $('#store');
                btn.attr('disabled', 'disabled');

                if($("form").valid()){
                    self.submit();
                }else{
                    btn.removeAttr('disabled');
                }
                return false; //is superfluous, but I put it here as a fallback
            });

            $('#existing_guardian').on('change', function(){
                if($(this).is(':checked')) {
                    $('#new_guardian_container').hide();
                    $('#search_guardian_container').show();
                }else {
                    $('#search_guardian_container').hide();
                    $('#new_guardian_container').show();
                }
            }).change();

            $('#search_guardian').autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: "<?php echo  URL::action('VmanageCompanyGuardiansController@postSearchGuardian');?>",
                        data: {
                            term: request.term,
                            company_id: "{{ $company->id }}",
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
        });
    </script>

@stop

