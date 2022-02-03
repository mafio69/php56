@extends('layouts.main')

@section('header')
    Generowanie zestawień
@stop

@section('main')
    @include('insurances.reports.nav')

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Zestawienie umów obcych</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/sheet-other'), 'id' => 'page-form' )) }}
                        @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'order_date_from', 'datepicker_id_to' => 'order_date_to'))
                        <div class="row marg-top">
                            <div class="text-center col-md-8 col-md-offset-2" >
                                {{ Form::submit('Generuj zestawienie',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie zestawienia...'))  }}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Zestawienie jachtów</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/sheet-yachts'), 'id' => 'page-form' )) }}
                    <div class="row marg-top">
                        <div class="col-md-8 col-md-offset-2">
                            <label class="radio-inline">
                                <input type="radio" name="from_type" value="created_at" checked> Data dodania polisy do systemu
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="from_type" value="insurance_date"> Data polisy widniejąca na polisie
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="from_type" value="agreement_created_at"> Data dodania umowy do systemu
                            </label>
                        </div>
                    </div>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'yachts_date_from', 'datepicker_id_to' => 'yachts_date_to'))
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Generuj zestawienie',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie zestawienia...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    <h3 class="panel-title">Zestawienie po przedmiotach</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/sheet-objects'), 'id' => 'page-form' )) }}
                    <div class="row marg-btm" >
                        <div class="col-sm-12 ">
                            <label>Nazwa przedmiotu zaczyna się od:</label>
                            <input type="text" name="object_name" class="form-control required" placeholder="wprowadź początek nazwy przedmiotu"  >
                        </div>
                    </div>
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Generuj zestawienie',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie zestawienia...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Zestawienie różnic</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/sheet-differences'), 'id' => 'page-form' )) }}
                    <div class="row marg-btm" >
                        <div class="col-sm-12 col-md-8 col-md-offset-2">
                            <label>Ubezpieczyciel:</label>
                            {{ Form::select('insurance_company_id', $insuranceCompanies, 7, ['class' => 'form-control'])}}
                        </div>
                    </div>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'differences_date_from', 'datepicker_id_to' => 'differences_date_to'))
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Generuj zestawienie',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie zestawienia...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Zestawienie zawartych ubezpieczeń majątkowych</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/sheet-property-insurances'), 'id' => 'page-form' )) }}
                    <div class="row marg-btm" >
                        <div class="col-sm-12 col-md-8 col-md-offset-2">
                            <label>Ubezpieczyciel:</label>
                            {{ Form::select('insurances_insurance_company_id', $insuranceCompanies, 7, ['class' => 'form-control'])}}
                        </div>
                    </div>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'insurances_date_from', 'datepicker_id_to' => 'insurances_date_to'))
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Generuj zestawienie',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie zestawienia...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row marg-btm">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Rejestr zwrotów składek</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url' => URL::to('insurances/reports/sheet-refunds'), 'id' => 'page-form' )) }}
                    <div class="row marg-btm" >
                        <div class="col-sm-12 col-md-8 col-md-offset-2">
                            <label>Ubezpieczyciel:</label>
                            {{ Form::select('insurances_insurance_company_id', $insuranceCompanies, 7, ['class' => 'form-control'])}}
                        </div>
                    </div>
                    @include('reports.partials.datepicker', array('datepicker_classes' => 'required', 'datepicker_id_from' => 'refunds_date_from', 'datepicker_id_to' => 'refunds_date_to'))
                    <div class="row marg-top">
                        <div class="text-center col-md-8 col-md-offset-2" >
                            {{ Form::submit('Generuj zestawienie',  array('class' => 'form_submit btn btn-primary btn-block', 'data-loading-text' => 'Trwa generowanie zestawienia...'))  }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>


@stop
@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').each(function(e){
                $(this).validate().cancelSubmit = true;
            });

            $(".page-form").submit(function(e) {
                var self = this;

                e.preventDefault();

                if($(this).valid()){
                    self.submit();
                }else{
                    $('.form_submit').button('reset');
                }

                return false; //is superfluous, but I put it here as a fallback
            });
        });
    </script>
@stop
