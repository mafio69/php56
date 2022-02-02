@extends('layouts.main')

@section('header')

    Edycja danych Info Flota dla <i>{{ $company->name }}</i>

@stop

@section('main')

    <div class="row marg-btm">
        <div class="pull-right">
            <button target="{{ URL::action('VmanageCompaniesController@getCreateCsm', [$company->id]) }}" class="btn btn btn-primary modal-open" data-toggle="modal" data-target="#modal">
                <i class="fa fa-plus"></i> dodaj nową informację
            </button>
            <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
        </div>
    </div>

    <div class="row">
        @foreach($csm_types as $id => $name)
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="panel panel-primary ">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ $name }}
                            <button target="{{ URL::action('VmanageCompaniesController@getEditCsm', [$company->id, $id]) }}" class="btn btn-xs btn-info modal-open pull-right marg-btm" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-pencil"></i> edytuj
                            </button>
                        </h3>
                    </div>
                    <div class="panel-body">
                        @if($company->csm()->where('vmanage_csm_type_id', $id)->first())
                            {{ $company->csm()->where('vmanage_csm_type_id', $id)->first()->content }}
                        @else
                            ---
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>



@stop

