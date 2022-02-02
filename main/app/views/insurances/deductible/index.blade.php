@extends('layouts.main')

@section('header')

    Franszyzy redukcyjne stawek

@stop

@section('sub-header')
    <h4 class="lead marg-btm overflow text-primary">
        Wybrany ubezpieczyciel {{ $insuranceCompanies[$insurance_company_id] }}
        <div class="pull-right marg-right">
            <label for="group_select">
                <small>Wybierz ubezpieczyciela:</small>
            </label>
            <div class="input-group" style="width: 300px;">
                {{ Form::select('insurance_company_id', $insuranceCompanies, $insurance_company_id, array('class' => 'form-control', 'id' => 'insurance_company_id')) }}
            </div>
        </div>
    </h4>
@stop

@section('main')
    <div class="marg-top">
        <table class="table table-bordered table-condensed table-middle table-auto  table-hover " id="users-table">
            <thead>
            <th>lp.</th>
            <th>Nazwa stawki</th>
            <th>Wartość franszyzy redukcyjnej</th>
            <th></th>
            </thead>
            @foreach ($rates as $k => $rate)
                <tr>
                    <td>{{++$k}}.</td>
                    <td>
                        {{ $rate->name }}
                    </td>
                    <td>
                        @if($rate->deductible_percent)
                            {{ $rate->deductible_percent }} %
                        @elseif($rate->deductible_value)
                            {{ $rate->deductible_value }} zł
                        @else
                            ---
                        @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('wykaz_franszyz#zarzadzaj'))
                            <button target="{{ URL::to('insurances/deductible/edit', [$rate->id] ) }}"
                                    class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal">
                                <i class="fa fa-edit"></i> edytuj
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>


@stop


@section('headerJs')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {

            $('#insurance_company_id').on('change', function () {
                self.location = '/insurances/deductible/index/' + $(this).val();
            });
        });
    </script>

@stop
