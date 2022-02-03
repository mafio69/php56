@extends('layouts.main')

@section('header')

    Importowanie umowów jachtów

    <div class="pull-right">
        <a href="{{ URL::to('insurances/manage/index' ) }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    @if(Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
        </div>
    @endif
    {{ Form::open(array('url' => URL::to('insurances/store/add-yachts' ), 'id' => 'page-form' )) }}
        <input type="hidden" name="filename" value="{{ $filename }}"/>
            <div class="row">
                <div class="form-group col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                    <label for="owner_id">Wybierz finansującego:</label>
                    {{ Form::select('owner_id', $owners, 1, ['class' => 'form-control', 'id' => 'owner_id']) }}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
                    <label for="owner_id">Wybierz typ umów:</label>
                    {{ Form::select('leasing_agreement_type_id', $agreementTypes, 2, ['class' => 'form-control', 'id' => 'leasing_agreement_type_id']) }}
                </div>
            </div>
            <div class="row marg-btm">
                <div class="center-block " style="text-align:center; margin-bottom:40px; margin-top:40px;">
                    {{ Form::submit('Rozpocznij import',  array('id' => 'form_submit', 'class' => 'btn btn-primary btn-lg', 'style' => 'width:400px; height: 50px;', 'data-loading-text' => 'Trwa importowanie umów...'))  }}
                </div>
            </div>
        </div>
    {{ Form::close() }}
@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
    </script>
@stop

