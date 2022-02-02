@extends('layouts.main')



@section('main')
    <div class="row">
        <form action="{{ URL::to('plan/groups/attach-branch') }}" method="post" role="form">
            {{ Form::token() }}
            {{ Form::hidden('plan_group_id', $plan_group->id) }}
            {{ Form::hidden('branch_id') }}
            <div class="col-sm-12 col-md-8 col-md-offset-2 ">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('plan/groups/show', [$plan_group->id]) }}" class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-arrow-left fa-fw"></i>
                            wróć
                        </a>
                        <strong>
                            Dodawanie serwisu do {{ $plan_group->name }}
                        </strong>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <label>Wyszukiwanie siedziby serwisu:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="name">Nazwa serwisu</label>
                                    {{ Form::text('name', '', array('class' => 'form-control term', 'placeholder' => 'nazwa'))  }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="nip">NIP serwisu</label>
                                    {{ Form::text('nip', '', array('class' => 'form-control term', 'placeholder' => 'NIP'))  }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="brand">Marka</label>
                                    {{ Form::text('brand', '', array('class' => 'form-control term', 'placeholder' => 'marka'))  }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="aso">ASO</label>
                                    <br>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="aso" value="1" >
                                            tak
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="aso" value="0">
                                            nie
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="aso" value="3" checked>
                                            niezależnie
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-lg-offset-4 text-center">
                                <div class="form-group">
                                    <button class="btn btn-xs btn-info btn-block search" type="button">
                                        <i class="fa fa-search faf-w"></i> szukaj
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <hr>
            </div>
            <div class="col-sm-12 text-center content-loader" style="display: none;">
                <h2>
                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                </h2>
            </div>
            <div class="col-sm-12 col-md-6" id="search-container">

            </div>
            <div class="col-sm-12 col-md-6" id="branch-container">

            </div>
        </form>
    </div>
@stop

@section('headerJs')
    @parent
    <script type="text/javascript" >
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip()

            $('.term').on('keyup' , function(e){
                if(e.which == 13)
                {
                    e.preventDefault();
                    $('.search').click();
                }
            });

            $('.search').on('click', function(){
                $.ajax({
                    type: "GET",
                    url: '/plan/groups/search-branch',
                    data: $('form').serialize(),
                    assync: false,
                    cache: false,
                    beforeSend: function(){
                        $('.content-loader').show();
                        $('#search-container, #branch-container').html('');
                    },
                    success: function (data) {
                        $('.content-loader').hide();
                        $('#search-container').append(
                            '<div class="panel panel-default"><table class="table table-hover table-condensed"><thead>' +
                                '<th>#</th>' +
                                '<th></th>' +
                                '<th>nazwa</th>' +
                                '<th>adres</th>' +
                                '<th>NIP</th>' +
                                '<th></th>' +
                            '</thead></table></div>');
                        $.each(data, function (i ,branch) {
                            $('#search-container table').append(
                                '<tr>' +
                                    '<td>' + ++i + '.</td>' +
                                    '<td>' + ((branch.suspended) ? '<span class="label label-danger">zawieszona</span>' : '') +'</td>' +
                                    '<td>' + branch.short_name + '</td>' +
                                    '<td>' + branch.address + '</td>' +
                                    '<td>' + (branch.nip  == null ? branch.company.nip : branch.nip) + '</td>' +
                                    '<td><span class="btn btn-xs btn-primary branch-checked" data-branch="' + branch.id + '"><i class="fa fa-check-square-o fa-fw"></i> wybierz</span></td>' +
                                '</tr>'
                            );
                        })
                    },
                    dataType: 'json'
                });
            });

            $('#search-container').on('click', '.branch-checked', function(){
                $('#search-container tr').removeClass('info');
                $(this).parents('tr').addClass('info');
                branch_id = $(this).data('branch');
                $('input[name="branch_id"]').val(branch_id);

                $.ajax({
                    url: "/plan/groups/branch-brands",
                    data: $('form').serialize(),
                    dataType: "html",
                    type: "GET",
                    success: function( data ) {
                        $('#branch-container').html(data);
                    }
                });
            });

            $('#branch-container').on('click', '.add-brand', function(){
                branch_brand_id = $(this).data('brand');
                var el = $(this);
                $.ajax({
                    url: "/plan/groups/add-brand",
                    data: {
                        branch_brand_id: branch_brand_id
                    },
                    dataType: "html",
                    type: "GET",
                    success: function( data ) {
                        $('#brands-container table').append(data);
                        el.parents('.brand-group-selector').hide();
                    }
                });
            });

            $('#branch-container').on('click', '.remove-branch-brand-row', function () {
                brand_id = $(this).data('brand');
                $(this).parents('tr').remove();
                $('.brand-group-selector[data-brand="'+brand_id+'"]').show();
            });

            $('#branch-container').on('click', '.submit', function(){
                $.ajax({
                    url: "/plan/groups/attach-branch",
                    data: $('form').serialize(),
                    dataType: "json",
                    type: "post",
                    success: function( data ) {
                        $('#brands-container table tr.brand-row').remove();
                    }
                });
            });
        });
    </script>
@stop

