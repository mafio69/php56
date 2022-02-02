@extends('insurances.manage.index_template')

@section('page-title')
    Polisy - wznowienia przyszłe (jachty)
@stop

@section('table-content')
    <table class="table table-hover  table-condensed" id="users-table">
        <thead>
        <Th style="width:30px;">lp.</th>
        <Th></Th>
        <th>nr umowy</th>
        <th>leasingobiorca</th>
        <th>TU</th>
        <th>polisa od</th>
        <th>polisa do</th>
        <th>SU [netto]</th>
        <th>nr zgłoszenia</th>
        <th></th>
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
                <Td>
                    @if($leasingAgreement->detect_problem == 1)
                        <i class="fa fa-exclamation-triangle red tips" title="problem w trakcie importu"></i>
                    @endif
                    @if($leasingAgreement->has_yacht == 1)
                        <i class="fa fa-ship blue"></i>
                    @endif
                    @if($leasingAgreement->if_foreign == 1)
                        <i class="fa fa-globe blue"></i>
                    @endif
                    @if(!is_null($leasingAgreement->reported_to_resume))
                        <i class="fa fa-flag warning tips" title="umowa oznaczona do wznowienia {{ $leasingAgreement->reported_to_resume }}"></i>
                    @endif
                    @if($leasingAgreement->refundedInsurances->count() > 0)
                        <i class="fa fa-money blue tips" title="zwrot składki"></i>
                    @endif
                    @if($leasingAgreement->if_foreign == 1)
                        <i class="fa fa-globe blue" title="umowa oznaczona jako obca"></i>
                    @endif
                </Td>
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
                    @if($leasingAgreement->client)
                        {{ $leasingAgreement->client->name }}
                    @else
                        <i>--- błąd importu leasingobiorcy ---</i>
                    @endif
                </td>
                <td>{{ ($leasingAgreement->insurances->first()->insuranceCompany)?$leasingAgreement->insurances->first()->insuranceCompany->name:'---'}}</td>
                <td>{{ $leasingAgreement->insurances->first()->date_from}}</td>
                <td>{{ $leasingAgreement->insurances->first()->date_to}}</td>
                <Td>{{ number_format($leasingAgreement->loan_net_value, 2, ",", " ") }} zł</Td>
                <td>
                    @if(Auth::user()->can('kartoteka_polisy#wejscie'))
                        <a  type="button" class="btn btn-link" href="{{ URL::to('insurances/info/show', [$leasingAgreement->id]) }}">
                            {{ $leasingAgreement->nr_agreement }}
                        </a>
                    @else
                        {{ $leasingAgreement->nr_agreement }}
                    @endif
                </td>
                <td>
                    @include('insurances.manage.options.resume')
                </td>
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
    </div>
@stop

