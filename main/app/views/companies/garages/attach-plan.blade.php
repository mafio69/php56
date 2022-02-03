@extends('layouts.main')



@section('main')
    <div class="row">
        <form action="{{ URL::to('company/garages/attach-plan', [$branch->id]) }}" method="post" role="form">
            {{ Form::token() }}
            <div class="col-sm-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('company/garages/show', [$branch->id]) }}" class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-ban fa-fw"></i>
                            anuluj
                        </a>
                        <strong>
                            Dodawanie programu do oddziału {{ $branch->short_name }}
                        </strong>
                        <button type="submit" class="btn btn-xs btn-primary pull-right">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            zapisz
                        </button>
                    </div>
                    <div class="panel-body">
                        <h4 class="text-center lead">Wskaż program:</h4>
                        @foreach($plans->chunk(4) as $chunk)
                            <div class="row">
                                @foreach($chunk as $plan)
                                    <div class="col-sm-3">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="plan_id" value="{{ $plan->id }}">
                                                {{ $plan->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        <hr>
                        <div id="plan-container">

                        </div>
                        <hr>
                        <div id="branch-brands-container" style="display: none;">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Dostępne marki
                                    <button id="add_multi" class="btn btn-xs btn-success pull-right">
                                        <i class="fa fa-plus"></i> Wielomarkowe
                                    </button>
                                </div>
                                <div class="panel-body">
                                    @foreach($branch->branchBrands->chunk(3) as $chunk)
                                        <div class="row">
                                            @foreach($chunk as $brand)
                                                {{$brand->multibrand}}
                                                <div class="col-sm-6 col-md-4 brand-group-selector" data-brand="{{ $brand->id }}">
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <p class="form-control-static">{{ $brand->brand->name }}
                                                                @if($brand->authorization)
                                                                    <span class="label label-info pull-right">autoryzowany</span>
                                                                @endif
                                                                @if($brand->brand->if_multibrand)
                                                                    <span class="label label-success pull-right">wielomarkowy</span>
                                                                @endif
                                                            </p>
                                                        <span class="input-group-btn add-brand" data-brand="{{ $brand->id }}" data-if-multibrand="{{ $brand->brand->if_multibrand }}">
                                                                <button class="btn btn-default" type="button">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div id="brands-container">
                                <div class="panel panel-default">
                                    <table class="table table-hover table-condensed">
                                        <thead>
                                        <th>marka</th>
                                        <th>wielomarkowy</th>
                                        <th>autoryzacja</th>
                                        <th>sprzedał</th>
                                        <th></th>
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
    <script type="text/javascript" >
        $(document).ready(function(){
            $('input[name="plan_id"]').on('change', function(){
                plan_id = $('input[name="plan_id"]:checked').val();

                $.ajax({
                    url: "/company/garages/load-plan-groups",
                    data: {
                        plan_id: plan_id
                    },
                    dataType: "html",
                    type: "GET",
                    success: function( data ) {
                        $('#plan-container').html(data);
                    }
                });
            });

            $('#plan-container').on('change', 'input[name="plan_group_id"]', function(){
                $('#branch-brands-container').show();
            });

            $('#branch-brands-container').on('click', '.add-brand', function(){
                addBrand(this);
            });

            $('#branch-brands-container').on('click', '.remove-branch-brand-row', function () {
                $(this).parents('tr').remove();
                brand_id = $(this).data('brand');
                $('.brand-group-selector[data-brand="'+brand_id+'"]').show();
            });

            $('#add_multi').on('click', function (e) {
                e.preventDefault();
                $('.remove-branch-brand-row').each(function () {
                    if($(this).data('ifMultibrand') == '1') $(this).parents('tr').remove();
                });
                $('.add-brand').each(function () {
                    if($(this).data('ifMultibrand') == '1') addBrand(this);
                });
            });

            async function addBrand(element) {
                branch_brand_id = $(element).data('brand');
                $.ajax({
                    url: "/plan/groups/add-brand",
                    data: {
                        branch_brand_id: branch_brand_id
                    },
                    dataType: "html",
                    type: "GET",
                    success: function( data ) {
                        $('#brands-container table').append(data);
                        $(element).parents('.brand-group-selector').hide();
                    }          
                });
            }
        });
    </script>
@stop

