@extends('layouts.main')

@section('header')

    Kartoteka umowy
    @if($agreement->leasingAgreementType)
        {{ $agreement->leasingAgreementType->name }}
    @else
        ---
    @endif
    nr {{$agreement->nr_contract}} <br><small>{{ $agreement->nr_agreement }}</small>
    @if($agreement->has_yacht == 1)
        <i class="fa fa-ship"></i>
    @endif
    @if($agreement->if_foreign == 1)
        <i class="fa fa-globe"></i>
    @endif

    <div class="pull-right">
        <a href="
          @if(Session::has('prev'))
        {{ Session::get('prev') }}
        @else
        {{ URL::previous() }}
        @endif
                " class="btn btn-default">Powrót</a>
    </div>
@stop

@section('main')
    {{ Form::token() }}
    @include('insurances.manage.card_file-yacht.nav')
    <div class="tab-content">
        @include('insurances.manage.card_file-yacht.communicator')
        @include('insurances.manage.card_file-yacht.agreement_data')
        @include('insurances.manage.card_file-yacht.yachts')
        @include('insurances.manage.card_file-yacht.insurances')
        @include('insurances.manage.card_file-yacht.files')
        @include('insurances.manage.card_file-yacht.history')
    </div>



@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            var hash = window.location.hash;

            $('#info_tabs a[href="' + hash + '"]').tab('show');

            $('.nav-tabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');

                if(history.pushState) {
                    history.pushState(null, null, e.target.hash);
                }
                else {
                    location.hash = e.target.hash;
                }
            });

            $('.datepicker').datepicker();

            $('.payment-confirmation').on('change', function(){
                var $alert = $(this);

                $.ajax({
                    type: "POST",
                    url: $alert.attr('hrf'),
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'date'  : $('#payment_'+$alert.data('payment-id')).val(),
                        'payment_id': $alert.data('payment-id'),
                        'agreement_id': '{{ $agreement->id }}'
                    },
                    assync:false,
                    cache:false,
                    success: function( data ) {
                        if(data.status == 'error')
                        {
                            $alert.parent().removeClass('active');
                            $.notify({
                                message: data.msg
                            },{
                                type: 'danger'
                            });
                        }else {
                            $alert.parent().addClass('active disabled');

                            $alert.attr('disabled', 'disabled');

                            $('#payment_'+$alert.data('payment-id')).attr('disabled', 'disabled');

                            $.notify({
                                message: data.msg
                            },{
                                type: 'success'
                            });
                        }
                    },
                    dataType: 'json'
                });
            });

        });
    </script>

@stop