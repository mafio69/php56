@extends('layouts.main')


@section('header')

Lista generacji dla {{ $model->name }}

<div class="pull-right">
    <a href="{{ URL::route('settings.brand.models', array($model->brand_id)) }}" class="btn btn-sm btn-warning">powrót</a>
{{--	<button target="{{{ URL::to('/settings/brand/'.$model->id.'/models/create') }}}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj generację</button>--}}
</div>

@stop

@section('main')
@include('modules.flash_notification')
    <div class="table-responsive">
        <table class="table  table-hover table-condensed" id="users-table">
            <thead>
                    <th>lp.</th>
                    <Th>generacja</Th>
{{--                    <th ></th>--}}
{{--                    <Th></Th>--}}
            </thead>
            <?php
                $lp = (($generations->getCurrentPage()-1)*$generations->getPerPage()) + 1;
                foreach ($generations as $k => $generation)
                { ?>
                <tr >
                    <td width="20px">{{$lp++}}.</td>
                    <Td>{{$generation->name}}</td>
{{--                    <td>--}}
{{--                        <button target="{{ URL::route('brands-edit', array($generation->id)) }}" class="btn btn-warning btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-pencil fa-fw"></i> edytuj</button>--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        <button target="{{ URL::route('brands-delete', array($generation->id)) }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-trash-o fa-fw"></i> usuń</button>--}}
{{--                    </td>--}}
                </tr>
                <?php }
             ?>
        </table>
        <div class="pull-right" style="clear:both;">{{ $generations->links() }}</div>
    </div>


@stop

@section('headerJs')
	@parent
	<script type="text/javascript">
	    $(document).ready(function() {

	    });
    </script>
@stop

