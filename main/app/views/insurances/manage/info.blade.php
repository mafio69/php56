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
        {{{ Session::get('prev') }}}
        @else
        {{{ URL::previous() }}}
        @endif
                " class="btn btn-default">Powrót</a>
    </div>
@stop

@section('main')
    {{ Form::token() }}
    @include('insurances.manage.card_file.nav')
    <div class="tab-content">
        @include('insurances.manage.card_file.communicator')
        @include('insurances.manage.card_file.agreement_data')
        @include('insurances.manage.card_file.objects')
        @if($agreement->insurances)
            @include('insurances.manage.card_file.insurances')
        @endif
        @include('insurances.manage.card_file.gen_docs')
        @include('insurances.manage.card_file.files')
        @include('insurances.manage.card_file.history')
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

        });
        $('#modal, #modal-lg').on('click', '#generate-document', function(){
            var missed = 0;
            $('#dialog-generate-doc-form input').each(function(){
                if($(this).val() == '' || $(this).val() == null){
                    missed = 1;
                }
            });
            if(missed == 1){
                if(confirm('Pozostały niewypełnione pola. Czy mimo to wygenerować dokument?')){
                    $.ajax({
                        type: "POST",
                        url: $('#dialog-generate-doc-form').prop( 'action' ),
                        data: $('#dialog-generate-doc-form').serialize(),
                        assync:false,
                        cache:false,
                        beforeSend: function(){
                            $('#modal .modal-body').html( 'Trwa generowanie dokumentu. Proszę czekać.');
                            $('#generate-document').attr('disabled',"disabled");
                        },
                        complete: function( data ) {
                            window.open(data.responseText,'_blank');
                            setTimeout(function(){
                                window.location.hash = "#files";
                                window.location.reload();
                            }, 500);
                        },
                        dataType: 'json'
                    });
                }else{

                }
            }else{
                $.ajax({
                    type: "POST",
                    url: $('#dialog-generate-doc-form').prop( 'action' ),
                    data: $('#dialog-generate-doc-form').serialize(),
                    assync:false,
                    cache:false,
                    beforeSend: function(){
                        $('#modal .modal-body').html( 'Trwa generowanie dokumentu. Proszę czekać.');
                        $('#generate-document').attr('disabled',"disabled");
                    },
                    complete: function( data ) {
                        window.open(data.responseText,'_blank');
                        setTimeout(function(){
                            window.location.hash = "#files";
                            window.location.reload();
                        }, 500);
                    },
                    dataType: 'json'
                });
            }

            return false;
        });
    </script>

@stop
