@extends('layouts.main')


@section('header')

Lista modeli dla {{ $brand->name }}

<div class="pull-right">
    <a href="{{ URL::route('brands') }}" class="btn btn-sm btn-warning">powrót</a>
	<button target="{{{ URL::to('/settings/brand/'.$brand->id.'/models/create') }}}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj model</button>
</div>

@stop


@section('leftNav')
    @parent
    <div class="l-menu-content left-nav-element">
        <table id="l-menu-show" style="width: 26px;">
            <tr>
                <td>
                    <?php $exist_filter = 0;?>

                    @if($exist_filter == 0)
                        <i class="fa fa-angle-right"></i>
                    @endif
                </td>
            </tr>
        </table>
    </div>
@stop

@section('leftNavContent')
    @parent
    <form method="post" id="search-form" action="{{ URL::route('session.setSearch') }}" >
        {{Form::token()}}
        <nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="l-menu">
            <div class="cbp-search-para">
                <label>Liczba wierszy na stronie:</label>
                <select class="form-control input-sm  search-el" id="s-pagin" name="pagin">
                    <option value="10"
                            @if(Session::get('search.pagin', '10') == 10)
                            selected
                            @endif
                    >10</option>
                    <option value="15"
                            @if(Session::get('search.pagin', '10') == 15)
                            selected
                            @endif
                    >15</option>
                    <option value="20"
                            @if(Session::get('search.pagin', '10') == 20)
                            selected
                            @endif
                    >20</option>
                    <option value="25"
                            @if(Session::get('search.pagin', '10') == 25)
                            selected
                            @endif
                    >25</option>
                    <option value="30"
                            @if(Session::get('search.pagin', '10') == 30)
                            selected
                            @endif
                    >30</option>
                    <option value="40"
                            @if(Session::get('search.pagin', '10') == 40)
                            selected
                            @endif
                    >40</option>
                    <option value="50"
                            @if(Session::get('search.pagin', '10') == 50)
                            selected
                            @endif
                    >50</option>
                </select>
            </div>
        </nav>
    </form>
@stop

@section('main')
@include('modules.flash_notification')
    <div class="table-responsive">
        <table class="table  table-hover table-condensed" id="users-table">
            <thead>
                    <th>lp.</th>
                    <Th>model</Th>
                    <Th></Th>
                    <th ></th>
                    <Th></Th>
            </thead>
            <?php
                $lp = (($models->getCurrentPage()-1)*$models->getPerPage()) + 1;
                foreach ($models as $k => $model)
                { ?>
                <tr >
                    <td width="20px">{{$lp++}}.</td>
                    <Td>{{$model->name}}</td>
                    <td>
                        <a href="{{ URL::route('settings.brand.model.generations', array($model->id)) }}" style="width: 150px;" class="btn btn-primary btn-xs"><span class="pull-left"><i class="fa fa-car"></i> generacje</span><span class="badge pull-right">{{ $model->generations->count() }}</span></a>
                    </td>
                    <td>
                        <button target="{{ URL::to('/settings/brand/'.$model->id.'/models/edit') }}" class="btn btn-warning btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil fa-fw"></i> edytuj</button>
                    </td>
                    <td>
                        <button target="{{ URL::to('/settings/brand/'.$model->id.'/models/delete') }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-trash-o fa-fw"></i> usuń</button>
                    </td>
                </tr>
                <?php }
             ?>
        </table>
        <div class="pull-right" style="clear:both;">{{ $models->links() }}</div>
    </div>


@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {

	    });
    </script>
@stop

