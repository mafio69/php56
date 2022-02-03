@extends('layouts.main')

@section('main')
    <div class="row">
        <form action="{{ URL::to('company/garages/update-branch-brands', [$branch->id]) }}" method="post" role="form">
            {{ Form::token() }}
            <div class="col-sm-12 col-lg-10 col-lg-offset-1">

                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('company/garages/show', [$branch->id]) }}"
                           class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-ban fa-fw"></i>
                            anuluj
                        </a>
                        <strong>
                            Obsługiwane marki dla {{ $branch->short_name }}
                        </strong>
                        <button type="submit" class="btn btn-xs btn-primary pull-right">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            zapisz
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-md-offset-1 brands-container" data-type="1">
                                <h4 class="text-center ">Osobowe</h4>
                                <div class="panel panel-default">
                                    <table class="table table-condensed table-hover">
                                        <thead>
                                        <th>Marka</th>
                                        <th>Czy autoryzowany</th>
                                        <th></th>
                                        <th></th>
                                        </thead>
                                        @foreach($branch->branchBrands as $branchBrand)
                                            @if($branchBrand->brand->typ == 1)
                                                <tr>
                                                    <td>{{ $branchBrand->brand->name }}</td>
                                                    <td>
                                                        <label>
                                                            <input type="radio" name="authorization[{{ $branchBrand->brand->id }}]" value="1" @if($branchBrand->authorization) checked @endif > tak
                                                        </label>
                                                        <label class="marg-left">
                                                            <input type="radio" name="authorization[{{ $branchBrand->brand->id }}]" value="0" @if(! $branchBrand->authorization) checked @endif> nie
                                                        </label>
                                                        <input type="hidden" name="brand_id[{{ $branchBrand->brand->id }}]" value="{{ $branchBrand->brand->id }}" class="brand_id" data-type="1">
                                                        <input type="hidden" name="as_multibrand[{{ $branchBrand->brand->id }}]" value="{{ $branchBrand->if_multibrand }}"  data-type="1">
                                                    </td>
                                                    <td>
                                                        @if ($branchBrand->if_multibrand)
                                                            <span class="label label-success pull-right">wielomarkowy</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="btn btn-xs btn-danger remove-brand-row"  
                                                        data-type="1">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-md-offset-2 brands-container" data-type="2">
                                <h4 class="text-center">Ciężarowe</h4>
                                <div class="panel panel-default">
                                    <table class="table table-condensed table-hover">
                                        <thead>
                                        <th>Marka</th>
                                        <th>Czy autoryzowany</th>
                                        <th></th>
                                        <th></th>
                                        </thead>
                                        @foreach($branch->branchBrands as $branchBrand)
                                            @if($branchBrand->brand->typ == 2)
                                                <tr>
                                                    <td>{{ $branchBrand->brand->name }}</td>
                                                    <td>
                                                        <label>
                                                            <input type="radio" name="authorization[{{ $branchBrand->brand->id }}]" value="1" @if($branchBrand->authorization) checked @endif > tak
                                                        </label>
                                                        <label class="marg-left">
                                                            <input type="radio" name="authorization[{{ $branchBrand->brand->id }}]" value="0" @if(! $branchBrand->authorization) checked @endif> nie
                                                        </label>
                                                        <input type="hidden" name="brand_id[{{ $branchBrand->brand->id }}]" value="{{ $branchBrand->brand->id }}" class="brand_id" data-type="2">
                                                        <input type="hidden" name="as_multibrand[{{ $branchBrand->brand->id }}]" value="{{ $branchBrand->if_multibrand }}" class="brand_id" data-type="2">
                                                    </td>
                                                    <td>
                                                        @if ($branchBrand->if_multibrand)
                                                            <span class="label label-success pull-right">wielomarkowy</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="btn btn-xs btn-danger remove-brand-row" 
                                                        data-type="2">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <div class="col-sm-6 col-md-4 col-md-offset-1">
                                <div class="form-group ">
                                    <label>Wyszukiwanie marki osobowej:</label>
                                    {{ Form::text('term_1', '', array('class' => 'form-control search', 'placeholder' => 'nazwa marki', 'data-type' => 1))  }}
                                </div>
                                
                                <div class="text-center">
                                    <div class="add-brands btn-primary btn-xs btn-block" data-type="1">
                                        <i class="fa fa-plus fa-fw"></i> dodaj
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-md-offset-2 ">
                                <div class="form-group">
                                    <label>Wyszukiwanie marki ciężarowej:</label>
                                    {{ Form::text('term_2', '', array('class' => 'form-control search', 'placeholder' => 'nazwa marki', 'data-type' => 2))  }}
                                </div>
                                
                                <div class="text-center">
                                    <div class="add-brands btn-primary btn-xs btn-block" data-type="2">
                                        <i class="fa fa-plus fa-fw"></i> dodaj
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr>
                            </div>
                            <div class=" col-sm-6 col-md-4 col-md-offset-1" data-type="1">
                                <div class="search-results" data-type="1">
                                    <table class="table table-condensed table-hover">
                                        <thead>
                                        <th>
                                            <label>
                                                <input type="checkbox" id="check-all"/>
                                                marka
                                            </label>
                                        </th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-md-offset-2 " >
                                <div class="search-results" data-type="2">
                                    <table class="table table-condensed table-hover">
                                        <thead>
                                        <th>
                                            <label>
                                                <input type="checkbox" id="check-all"/>
                                                marka
                                            </label>
                                        </th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('.search').on('keyup', function(){
                searchBrands($(this));
            });
            $('.check').on('change', function(){
                $(this).data('asMultibrand', 0);
            });
            $('.remove-brand-row').on('click', function(){
                $(this).parents('tr').remove();
                searchBrands($(this));
            });
            $('input[name="term_1"]').keyup()
            setTimeout(function(){ $('input[name="term_2"]').keyup() }, 1000);

            $('.search-results').on('change', '#check-all', function(){
                type = $(this).data('type');
                if($(this).is(':checked'))
                {
                    $('.check[data-type="'+type+'"]').prop('checked', true);
                }else{
                    $('.check[data-type="'+type+'"]').prop('checked', false);
                }
            });

            $('.search-results').on('change', '.check', function(){
                type = $(this).data('type');
                if( ! $(this).is(':checked'))
                {
                    $('#check-all[data-type="'+type+'"]').prop('checked', false);
                }
            });

            $('.add-brands').on('click', function () {
                type = $(this).data('type');
                $('input.check[data-type="'+type+'"]:checked').each(function(){
                    var id = $(this).val();
                    var name = $(this).data('name');
                    var if_multibrand = $(this).data('ifMultibrand');
                    var as_multibrand = $(this).data('asMultibrand');

                    var element = '<tr><td>'+name+'</td><td><label><input type="radio" name="authorization['+id+']" value="1"> tak</label><label class="marg-left"><input type="radio" name="authorization['+id+']" value="0" checked> nie</label><input type="hidden" class="brand_id" name="brand_id['+id+']" data-type="'+type+'" value="'+id+'"><input name="if_multibrand['+id+']" type="hidden" value="'+if_multibrand+'"><input name="as_multibrand['+id+']" type="hidden" value="'+as_multibrand+'"></td>"'
                    if(as_multibrand == true) {
                        element += '<td><span class="label label-success pull-right">wielomarkowy</span></td>'
                    } else {
                        element += '<td></td>'
                    }
                    element += '<td><span class="btn btn-xs btn-danger remove-brand-row" data-type="'+type+' data-brand_id="'+id+'><i class="fa fa-trash"></i></span></td></tr>'
                    $('.brands-container[data-type="'+type+'"] table').append(element);
                });
                $('input[name="term_'+type+'"]').keyup();

                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            });

            $('.search-results').on('change', '#check-multi', function(){
                type = $(this).data('type');
                if($(this).is(':checked'))
                {
                    $('.check[data-type="'+type+'"]').each(function (index) {
                        data = $(this).data();
                        if(data['ifMultibrand'] == 1) {
                            $(this).prop('checked', true);
                            $(this).data('asMultibrand', 1);
                        }
                    });
                }else{
                    $('.check[data-type="'+type+'"]').each(function (index) {
                        data = $(this).data();
                        if(data['ifMultibrand'] == 1) {
                            $(this).prop('checked', false);
                        }
                    });
                }
            });

            $('.brands-container').on('click', '.remove-brand-row', function(){
                $(this).parents('tr').remove();
            });
        });

        function searchBrands(el) {
            brands = $.map($('input.brand_id'), function(c){return c.value; });
                type = $(el).data('type');
                $.ajax({
                    url: "/company/garages/search-brand",
                    data: {
                        term: $(el).val(),
                        typ: type ,
                        brands: brands
                    },
                    beforeSend: function(){
                        $('.search-results[data-type="'+type+'"]').html('<p class="text-center"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></p>');
                    },
                    assync: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {
                        $('.search-results[data-type="'+type+'"]').html('<table class="table table-condensed table-hover"><thead><th><label><input type="checkbox" id="check-all" data-type="'+type+'"/> marka</label></th></thead></table>');
                        $('.search-results[data-type="'+type+'"] table').append('<tr><td><label><input type="checkbox" id="check-multi" data-type="'+type+'"/> wielomarkowy</label></td></tr>');
                        $.each(data, function (i, item) {
                            $('.search-results[data-type="'+type+'"] table').append('<tr><td><label><input type="checkbox" class="check" name="check-brands[]" value="'+item.id+'" data-name="'+item.name+'" data-type="'+type+ '" data-if-multibrand="'+item.if_multibrand+'" data-as-multibrand="' + 0 + '"/> '+item.name+'</label></td></tr>');
                        });
                    }
                });
            }
    </script>
@stop

