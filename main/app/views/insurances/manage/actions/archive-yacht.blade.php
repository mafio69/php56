@extends('layouts.main')

@section('header')

    Przenoszenie do archiwum polisy do umowy nr {{ $leasingAgreement->nr_contract }}

    <div class="pull-right">
        <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-primary">
                <div class="panel-heading">Wybierz polisę do archiwum z istniejących polis do umowy nr {{ $leasingAgreement->nr_contract }}:</div>
                <table class="panel-body table table-condensed table-hover">
                    <tr>
                        <th>lp</th>
                        <th>Nr polisy</th>
                        <th>Data polisy</th>
                        <th>Polisa od</th>
                        <th>Polisa do</th>
                        <th>Składka leasingobiorcy</th>
                        @foreach($coveragesTypes as $k => $name)
                            <th></th>
                        @endforeach
                        <th class="text-center"><em>wybierz</em></th>
                    </tr>
                    @foreach($leasingAgreement->insurances()->active()->get() as $k => $insurance)
                        <tr
                                @if($insurance->date_to && (\Carbon\Carbon::createFromFormat('Y-m-d', $insurance->date_to) < \Carbon\Carbon::now()) )
                                class="danger"
                                @endif
                                >
                            <td>{{ ++$k }}.</td>
                            <td>{{ $insurance->insurance_number }}</td>
                            <td>{{ $insurance->insurance_date }}</td>
                            <td>{{ $insurance->date_from }}</td>
                            <td>{{ $insurance->date_to }}</td>
                            <td>{{ $insurance->contribution_lessor }} zł</td>
                            @foreach($insurance->coverages as $coverage_lp => $coverage)
                                <td>
                                    {{ $coverage->type->name }} <i class="fa fa-check"></i>
                                </td>
                            @endforeach
                            @for($i = $insurance->coverages->count(); $i < count($coveragesTypes); $i++)
                                <td></td>
                            @endfor
                            <td class="text-center">
                                <label>
                                    <input type="radio" name="insurance_to_archive" value="{{ $insurance->id }}">
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-10 col-lg-offset-1 ">
            <div class="panel panel-primary ">
                <div class="panel-body">
                    <form action="{{ URL::to('insurances/manage-actions/move-archive-yacht', [$leasingAgreement->id]) }}" method="post"  id="dialog-form">
                        {{Form::token()}}
                        {{ Form::hidden('leasing_agreement_id', $leasingAgreement->id) }}
                        {{ Form::hidden('leasing_agreement_insurance_id', null) }}
                        <div id="refund-container">
                            <div class="row">
                                <div class="text-center col-md-8 col-md-offset-2" >
                                    {{ Form::submit('Przenieś',  array('class' => 'form_submit btn btn-primary btn-block', 'id' => 'form_submit', 'data-loading-text' => 'Trwa przenoszenie do archiwum...', 'disabled'))  }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {

            $( "form#page-form" ).submit(function(e) {
                var $btn = $('#form_submit').button('loading');
                if(! $('#page-form').valid()) {
                    e.preventDefault();
                    $btn.button('reset');
                    return false;
                }
                return true;
            });

            $('input[name="insurance_to_archive"]').on('change', function(){
                var radio = $(this);
                $('input[name="leasing_agreement_insurance_id"]').val(radio.val());
                $('#form_submit').removeAttr('disabled');
            });

        });
    </script>
@stop