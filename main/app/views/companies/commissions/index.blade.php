@extends('layouts.main')


@section('header')
    Prowizje dla {{ $company->name }}
@stop

@section('main')
    <form action="{{ URL::to('companies/store-commissions', [$company->id]) }}" method="post" role="form" id="page-form">
        {{Form::token()}}
        <div class="row marg-btm">
            <div class="pull-right">
                <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
            </div>
        </div>

        <div class="row">

            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-3 col-lg-offset-3">
                    <div class="form-group">
                        <label>Typ prowizji:</label>
                        {{ Form::select('commission_type_id', $commissionTypes, $company->commission_type_id, ['class' => 'form-control required']) }}
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="form-group">
                        <label>Cykl rozliczeniowy:</label>
                        {{ Form::select('billing_cycle_id', $billingCycles, $company->billing_cycle_id, ['class' => 'form-control required']) }}
                    </div>
                </div>
                <div class="commissions-container">
                    @if($company->commission_type_id == 1)
                        @include('companies.commissions.commission-linear', ['readonly' => 0])
                    @elseif($company->commission_type_id == 2)
                        @include('companies.commissions.commission-threshold-amount', ['readonly' => 0])
                    @elseif($company->commission_type_id == 3)
                        @include('companies.commissions.commission-threshold-value', ['readonly' => 0])
                    @elseif($company->commission_type_id == 4)
                        @include('companies.commissions.commission-brand', ['readonly' => 0])
                    @endif
                </div>
            </div>
            <div class="col-sm-12 text-center content-loader" style="display: none;">
                <h2>
                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary" id="form_submit">
                    <i class="fa fa-floppy-o fa-fw"></i> Zapisz
                </button>
            </div>
        </div>
    </form>
@stop

@section('headerJs')
    @parent

    <script>
        $('select[name="commission_type_id"]').on('change', function () {
            $.ajax({
                type: "POST",
                url: "{{ url('companies/commission') }}",
                data: {
                    company_id : '{{ $company->id }}',
                    commission_type_id : $(this).val(),
                    _token: $('input[name="_token"]').val()
                },
                assync:false,
                cache:false,
                beforeSend: function(){
                    $('.content-loader').show();
                },
                success: function( data ) {
                    $('.commissions-container').html(data);
                    $('.content-loader').hide();
                },
                dataType: 'text'
            });
        });
    </script>
@stop

