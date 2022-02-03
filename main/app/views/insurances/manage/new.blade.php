@extends('insurances.manage.index_template')

@section('page-title')
    Polisy - nowe
@stop

@section('table-content')
        <table class="table table-hover  table-condensed" id="users-table">
            <thead>
                <Th style="width:30px;">lp.</th>
                <Th></Th>
                <th>nr umowy</th>
                <th>nr zgłoszenia</th>
                <th>leasingobiorca</th>
                <Th>Ubezpieczyciel</Th>
                <Th>wprowadzający</Th>
                <Th>data zgłoszenia</th>
                <Th></Th>
                <th></th>
            </thead>
            <?php $lp = (($leasingAgreements->getCurrentPage()-1)*$leasingAgreements->getPerPage()) + 1;?>
            @foreach ($leasingAgreements as $leasingAgreement)
                <tr class="odd gradeX vertical-middle"
                    @if(Session::has('last_agreement') && $leasingAgreement->id == Session::get('last_agreement'))
                        style="background-color: honeydew;"
                        <?php Session::forget('last_agreement');?>
                    @endif
                >
                    <td>{{$lp++}}.</td>
                    <td>
                        @if($leasingAgreement->has_yacht == 1)
                            <i class="fa fa-ship blue"></i>
                        @endif
                        @if($leasingAgreement->if_foreign == 1)
                            <i class="fa fa-globe blue" title="umowa oznaczona jako obca"></i>
                        @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('kartoteka_polisy#wejscie'))
                            <a type="button" class="btn btn-link"  href="{{ URL::to('insurances/info/show', [$leasingAgreement->id]) }}">
                                {{ $leasingAgreement->nr_contract }}
                            </a>
                        @else
                            {{ $leasingAgreement->nr_contract }}
                        @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('kartoteka_polisy#wejscie'))
                            <a  type="button" class="btn btn-link" href="{{ URL::to('insurances/info/show', [$leasingAgreement->id]) }}">
                                {{ $leasingAgreement->nr_agreement }}
                            </a>
                        @else
                            {{ $leasingAgreement->nr_agreement }}
                        @endif
                    </td>
                    <td>{{ $leasingAgreement->client->name }}</td>
                    <td>{{ $leasingAgreement->import_insurance_company }}</td>
                    <td>{{ $leasingAgreement->user->name }}</td>
                    <td>{{ substr($leasingAgreement->created_at, 0, -3) }}</td>
                    <Td>
                        @include('insurances.manage.options.new')
                    </Td>
                    <td>
                        @if($leasingAgreement->remarks)
                            <a class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="left"  title="{{ $leasingAgreement->remarks }}">
                                <i class="fa fa-info"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        @include('insurances.manage.partials.legend')
        <div class="pull-right" style="clear:both;">{{ $leasingAgreements->appends(Input::query())->links() }}</div>
@stop
