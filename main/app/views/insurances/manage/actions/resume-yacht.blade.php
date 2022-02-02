@extends('layouts.main')

@section('header')

    Wznowienie polisy do umowy nr {{ $leasingAgreement->nr_contract }}

    <div class="pull-right">
        <a href="{{ URL::previous() }}" class="btn btn-default">Anuluj</a>
    </div>
@stop

@section('main')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-primary">
                <div class="panel-heading">Wybierz polisę do wznowienia z istniejących polis do umowy nr {{ $leasingAgreement->nr_contract }}:</div>
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
                    @foreach($leasingAgreement->insurances()->active()->whereNull('refunded_insurance_id')->get() as $k => $insurance)
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
                            <td>{{ $insurance->contribution_lessor }} {{ Config::get('definition.currencies')[$insurance->contribution_lessor_currency_id] }}</td>
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
                                    <input type="radio" name="insurance_to_resume" value="{{ $insurance->id }}">
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
                    {{ Form::open(array('url' => URL::to('insurances/manage-actions/store-resume-yacht'), 'class' => 'page-form form-horizontal', 'id' => 'page-form' )) }}
                        <div id="resume-container">

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

            $( "form#page-form" ).submit(function(e) {
                var $btn = $('#form_submit').button('loading');
                if(! $('#page-form').valid()) {
                    e.preventDefault();
                    $btn.button('reset');
                    return false;
                }
                return true;
            });

            $('input[name="insurance_to_resume"]').on('change', function(){
                var radio = $(this);
                $.ajax({
                    url: "{{ URL::to('insurances/manage-actions/resume-yacht-content') }}",
                    data: {
                        insurance_id: radio.val(),
                        _token: $('input[name="_token"]').val()
                    },
                    dataType: "html",
                    type: "GET",
                    success: function( data ) {
                        $('#resume-container').html(data);
                    }
                });
            });

        });
    </script>
@stop