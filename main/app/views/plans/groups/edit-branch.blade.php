@extends('layouts.main')



@section('main')
    <div class="row">
        <form action="{{ URL::to('plan/groups/update-branch', [$group->id]) }}" method="post" role="form">
            {{ Form::token() }}
            <div class="col-sm-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">
                        <a href="{{ URL::to('plan/groups/show', [$group->plan_group_id]) }}" class="btn btn-default btn-xs pull-left">
                            <i class="fa fa-ban fa-fw"></i>
                            anuluj
                        </a>
                        <strong>
                            Aktualizowanie serwisu {{ $group->branch->short_name }} do grupy {{ $group->planGroup->name }}
                        </strong>
                        <button type="submit" class="btn btn-xs btn-primary pull-right">
                            <i class="fa fa-floppy-o fa-fw"></i>
                            zapisz
                        </button>
                    </div>
                    <div class="panel-body">
                        <div id="branch-container">
                            @foreach($group->branch->branchBrands->chunk(3) as $chunk)
                                <div class="row">
                                    @foreach($chunk as $brand)
                                        <div class="col-sm-6 col-md-4 brand-group-selector"  @if(array_key_exists($brand->brand_id, $branchBrands)) style="display: none;" @endif data-brand="{{ $brand->brand_id }}" >
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <p class="form-control-static">{{ $brand->brand->name }}
                                                        @if($brand->authorization)
                                                            <span class="label label-info pull-right">autoryzowany</span>
                                                        @endif
                                                    </p>
                                                    <span class="input-group-btn add-brand" data-brand="{{ $brand->id }}">
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
                            <div id="brands-container">
                                <div class="panel panel-default">
                                    <table class="table table-hover table-condensed">
                                        <thead>
                                        <th>marka</th>
                                        <th>autoryzacja</th>
                                        <th>sprzedał</th>
                                        <th></th>
                                        </thead>
                                        @foreach($branchBrands as $k => $branchBrand)
                                            <tr>
                                                <td>{{ $branchBrand['brand']->name }}</td>
                                                <td>{{ $branchBrand['authorization'] ? 'tak' : 'nie' }}</td>
                                                <td>
                                                    <label>
                                                        <input type="checkbox" name="sold_yes[{{ $branchBrand['brand']->id }}]" value="1" @if(isset($branchBrand['sold_yes'])) checked @endif > tak
                                                    </label>
                                                    <label class="marg-left">
                                                        <input type="checkbox" name="sold_no[{{ $branchBrand['brand']->id }}]" value="1" @if(isset($branchBrand['sold_no'])) checked @endif> nie
                                                    </label>
                                                    <input type="hidden" name="branch_brand_id[{{ $branchBrand['brand']->id }}]" value="{{ $branchBrand['id'] }}">
                                                </td>
                                                <td>
                                                <span class="btn btn-xs btn-danger remove-branch-brand-row" data-brand="{{ $branchBrand['brand']->id }}">
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
            $('[data-toggle="tooltip"]').tooltip()

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
        });
    </script>
@stop

