@extends('layouts.main')

@section('styles')
    @parent
    <link rel="stylesheet" href="/css/bootstrap-table.min.css">
@stop

@section('header')

    Zarządzanie użytkownikami w grupie {{ $group->name }}

    <div class="pull-right">
        <a href="{{ URL::previous() }}" class="btn btn-sm btn-default"><i class="fa fa-arrow-left fa-fw"></i> anuluj</a>
    </div>
@stop

@section('main')

        <div class="row">
            <form action="{{ URL::to('settings/user/groups/update-group-users', [$group->id]) }}" method="post">
                {{ Form::token() }}
                <div class="col-sm-12 col-md-4 col-lg-2 col-md-offset-4 col-lg-offset-5">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-floppy-o fa-fw"></i> zapisz
                    </button>
                </div>
                <div class="hidden-permissions">
                    @foreach($group->users as $user)
                        <input type="hidden" name="users[]" value="{{ $user->id }}">
                    @endforeach
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form id="search-form">
                            {{ Form::token() }}
                            <input type="hidden" name="group_id" value="{{ $group->id }}">

                            <div class="pull-right">
                                <small>
                                <span class="label label-primary ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> - obecni
                                <span class="label label-success marg-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> - nowi
                                <span class="label label-danger marg-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> - usunięci
                                </small>
                            </div>

                            <h4 class="text-center page-header marg-top-min">Filtr</h4>
                            <div class="text-danger text-center" id="result-info"></div>
                            <div class="row">
                                <div class="form-group col-sm-4 text-center">
                                    <label>Login</label>
                                    {{ Form::text('login', null, ['class' => 'form-control input-sm search-element']) }}
                                </div>
                                <div class="form-group col-sm-4 text-center">
                                    <label>Email</label>
                                    {{ Form::text('email', null, ['class' => 'form-control input-sm search-element']) }}
                                </div>
                                <div class="form-group col-sm-4 text-center">
                                    <label>Nazwisko</label>
                                    {{ Form::text('name', null, ['class' => 'form-control input-sm search-element']) }}
                                </div>
                                <div class="col-sm-12 col-lg-4 col-lg-offset-4 text-center">
                                    <button type="submit" class="btn btn-info btn-xs btn-block" off-disable>
                                        <i class="fa fa-search fa-fw"></i>
                                        filtruj
                                    </button>
                                </div>
                                <div class="col-sm-12">
                                    <hr>
                                </div>
                            </div>
                        </form>
                        <div id="search-permissions">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
@stop

@section('headerJs')
    @parent
    <script src="/js/bootstrap-table.min.js"></script>
    <script src="/js/bootstrap-table-pl-PL.min.js"></script>
    <script>

        function search(){
            var ajax_data = $('#search-form').serialize();

            $.ajax({
                url: "/settings/user/groups/load-users",
                data: ajax_data,
                dataType: "html",
                type: "POST",
                success: function( data ) {
                    if(data.length > 0) {
                        $('#search-permissions').html(data);

                        $('#search-permissions table').bootstrapTable({
                            locale:'pl-PL'
                        });

                        $('.row-checkbox').each(function () {
                            var permission_id = $(this).val();
                            if(!$('.hidden-permissions input[value="'+permission_id+'"]').length || !$('.hidden-permissions input[value="'+permission_id+'"]').val().length) {
                                $(this).prop('checked', false).change();
                            }else{
                                $(this).prop('checked', true).change();
                            }
                        });
                    }else{
                        $('#search-permissions').html('Brak resultatów o podanych parametrach wyszukiwania.').show();
                    }
                }
            });
        }
        $('#search-form').on('submit', function(e){
            e.preventDefault();
            search();
        });

        $("#search-form input").on('keyup', function (e) {
            if (e.keyCode == 13) {
                search();
            }
        });

        $('#search-permissions').on('change','input[name="checkAll"]', function(){
            if($(this).is(':checked'))
            {
                $('.row-checkbox').prop('checked', true).change();
            }else{
                $('.row-checkbox').prop('checked', false).change();
            }
        });

        $('#search-permissions').on('change', '.row-checkbox', function(){
            if( ! $(this).is(':checked'))
            {
                $('input[name="checkAll"]').prop('checked', false);
            }

            var was = $(this).data('was');
            var permission_id = $(this).val();
            $(this).parent().parent().removeClass();

            if($(this).is(":checked")){
                if(was == '1')
                {
                    $(this).parent().parent().addClass('info');
                }else{
                    $(this).parent().parent().addClass('success');
                }

                if(!$('.hidden-permissions :input[value="'+permission_id+'"]').length) {
                    $('.hidden-permissions').append('<input type="hidden" name="users[]" value="' + permission_id + '"/>')
                }
            }else{
                if(was == '1') {
                    $(this).parent().parent().addClass('danger');
                }

                $('.hidden-permissions :input[value="'+permission_id+'"]').remove();
            }
        });
    </script>
@stop
