@extends('insurances.manage.index_template')

@section('page-title')
    Polisy - wyszukiwanie globalne
@stop

@section('table-content')
    <table class="table table-hover  table-condensed" id="users-table">
        <thead>
        <Th style="width:30px;">lp.</th>
        <th></th>
        <th>nr umowy</th>
        <th>nr zgłoszenia</th>
        <th>leasingobiorca</th>
        <Th>Ubezpieczyciel</Th>
        <Th>wprowadzający</Th>
        <Th>data zgłoszenia</th>
        <th>status</th>
        <Th></Th>
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
                    @if($leasingAgreement->detect_problem == 1)
                        <i class="fa fa-exclamation-triangle red tips" title="problem w trakcie importu"></i>
                    @endif
                    @if($leasingAgreement->has_yacht == 1)
                        <i class="fa fa-ship blue"></i>
                    @endif
                    @if(!is_null($leasingAgreement->reported_to_resume))
                        <i class="fa fa-flag warning tips" title="umowa oznaczona do wznowienia {{ $leasingAgreement->reported_to_resume }}"></i>
                    @endif
                    @if($leasingAgreement->insurances()->where('if_refund_contribution', 1)->count() > 0)
                        <i class="fa fa-money blue tips" title="zwrot składki"></i>
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
                <td>
                    @if($leasingAgreement->insurances->count() > 0)
                        {{ ($leasingAgreement->insurances->last()->insuranceCompany)? $leasingAgreement->insurances->last()->insuranceCompany->name:$leasingAgreement->import_insurance_company}}
                    @else
                        {{ $leasingAgreement->import_insurance_company }}
                    @endif
                </td>
                <td>{{ $leasingAgreement->user->name }}</td>
                <td>{{substr($leasingAgreement->created_at, 0, -3)}}</td>
                <td>
                    <b>
                        @if($leasingAgreement->insurances->isEmpty() && is_null($leasingAgreement->withdraw))
                            <?php $step = 'new';?>
                            nowe
                        @elseif(is_null($leasingAgreement->withdraw) && !is_null($leasingAgreement->archive))
                            archiwum
                            <?php $step = 'archive';?>
                        @elseif( !is_null($leasingAgreement->withdraw) )
                            wycofana
                        @elseif($leasingAgreement->insurances->count() > 0 &&
                            is_null($leasingAgreement->withdraw) && is_null($leasingAgreement->archive) &&
                            $leasingAgreement->insurances()->where(function($query){
                                    $to = \Carbon\Carbon::now()->endOfMonth();
                                    $from = \Carbon\Carbon::now()->startOfMonth();
                                    $query->active()->whereBetween('date_to',array($from,$to));
                                })->first())
                                <?php $step = 'resume';?>
                            wznowienie aktualne
                        @elseif($leasingAgreement->insurances->count() > 0 &&
                            is_null($leasingAgreement->withdraw) && !is_null($leasingAgreement->archive) &&
                                $leasingAgreement->insurances()->where(function($query){
                                    $to = \Carbon\Carbon::now()->subMonths(1)->endOfMonth();
                                    $query->active()->where('date_to', '<', $to);
                                })->first())
                            <?php $step = 'resume-outdated';?>
                            wznowienie archiwalne
                        @elseif($leasingAgreement->insurances->count() > 0 &&
                            $leasingAgreement->whereNull('withdraw')->whereNull('archive')
                                ->whereHas('insurances', function($query){
                                    $query->active();
                                })->first())
                            <?php $step = 'inprogress';?>
                            trwająca
                        @endif
                    </b>
                </td>
                <Td>
                    @if(isset($step))
                        @include('insurances.manage.options.'.$step)
                    @endif
                </Td>
            </tr>
        @endforeach
    </table>
    @include('insurances.manage.partials.legend')
    <div class="pull-right" style="clear:both;">{{ $leasingAgreements->appends(Input::query())->links() }}</div>
@stop

