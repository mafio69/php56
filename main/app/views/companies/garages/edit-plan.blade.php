@extends('layouts.main')



@section('main')
    <div class="row">
        <form action="{{ URL::to('company/garages/update-group', [$group->id]) }}" method="post" role="form">
            {{ Form::token() }}
            <div class="col-sm-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('company/garages/show', [$group->branch_id]) }}" class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-ban fa-fw"></i>
                            anuluj
                        </a>
                        <strong>
                            Edycja programu dla siedziby {{ $group->branch->short_name }}
                        </strong>
                        <button type="submit" class="btn btn-xs btn-primary pull-right">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            zapisz
                        </button>
                    </div>
                    <div class="panel-body">
                        <div id="branch-brands-container">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Dostępne marki
                                    <button id="add_multi" class="btn btn-xs btn-success pull-right"><i class="fa fa-plus"></i> Wielomarkowe</button></td>
                                </div>
                                <div class="panel-body">
                                    @foreach($group->branch->branchBrands->chunk(3) as $chunk)
                                        <div class="row">
                                            @foreach($chunk as $brand)
                                                <div class="col-sm-6 col-md-4 brand-group-selector" data-brand="{{ $brand->id }}" data-if-multibrand="{{$brand->if_multibrand}}"
                                                    @if(in_array($brand->id, $group->branchBrands->lists('id')) )
                                                        style="display: none;"
                                                     @endif
                                                >
                                                    <div class="form-group">
                                                        <div class="input-group ">
                                                            <p class="form-control-static">{{ $brand->brand->name }}
                                                                @if($brand->authorization)
                                                                    <span class="label label-info pull-right">autoryzowany</span>
                                                                @endif
                                                                @if($brand->if_multibrand)
                                                                <span class="label label-success pull-right">wielomarkowy</span>
                                                            @endif
                                                            </p>
                                                            <span class="input-group-btn add-brand" data-brand="{{ $brand->id }}" data-if-multibrand="{{$brand->if_multibrand}}">
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
                                        @foreach($group->branchBrands as $k => $branchBrand)
                                            <tr>
                                                <td>{{ $branchBrand->brand->name }}</td>
                                                <td>{{ $branchBrand->if_multibrand ? 'tak' : 'nie' }}</td>
                                                <td>{{ $branchBrand->authorization ? 'tak' : 'nie' }}</td>
                                                <td>
                                                    <label>
                                                        <input type="checkbox" name="sold_yes[{{ $k }}]" value="1" @if($branchBrand->pivot->if_sold) checked @endif> tak
                                                    </label>
                                                    <label class="marg-left">
                                                        <input type="checkbox" name="sold_no[{{ $k }}]" value="0" @if(!$branchBrand->pivot->if_sold) checked @endif> nie
                                                    </label>
                                                    <input type="hidden" name="branch_brand_id[{{ $k }}]" value="{{ $branchBrand->id }}">
                                                </td>
                                                <td>
                                                <span class="btn btn-xs btn-danger remove-branch-brand-row" data-brand="{{ $branchBrand->id }}" data-if-multibrand="{{$branchBrand->if_multibrand}}">
                                                        <i class="fa fa-trash"></i>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
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
                })
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

