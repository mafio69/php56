@extends('layouts.main')


@section('header')

    {{ $branch->short_name }}

    <div class="pull-right">
        <a href="{{ URL::to('companies/show', [$company->id]) }}" class="btn btn-default">Powrót</a>
    </div>

@stop

@section('main')

    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane serwisu - siedziba
                </div>
                <table class="table table-condensed table-hover">
                    <tr>
                        <td class="text-right"><label>Nazwa:</label></td>
                        <Td>{{ $company->name }}</td>
                        <td class="text-right"><label>Telefon:</label></td>
                        <Td>{{ $company->phone }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Adres:</label></td>
                        <td>{{ $company->address }}</td>
                        <td class="text-right"><label>Email:</label></td>
                        <td>{{ $company->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>NIP:</label></td>
                        <td>{{ $company->nip }}</td>
                        <td class="text-right"><label>Numer umowy:</label></td>
                        <td>{{ $company->www }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>KRS:</label></td>
                        <td>{{ $company->krs }}</td>
                        <td class="text-right"><label>Nr konta:</label></td>
                        <td>{{ $company->account_nr }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Regon:</label></td>
                        <td>{{ $company->regon }}</td>
                        <td class="text-right"><label>Adnotacje:</label></td>
                        <td>{{ $company->remarks }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Grupy:</label></td>
                        <td colspan="3">{{ implode('; ', $company->groups->lists('name')) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="panel {{ $branch->suspended == 1 ? 'panel-danger' : 'panel-primary' }} small">
                <div class="panel-heading ">
                    @if($branch->suspended)
                        <span class="label label-danger marg-right">
                            zawieszona
                        </span>
                    @endif
                    Dane oddziału
                    @if(Auth::user()->can('serwisy#warsztaty#zarzadzaj'))
                        <a href="{{ URL::to('company/garages/edit', array($branch->id)) }}" class="btn btn-warning btn-xs pull-right">
                            <i class="fa fa-pencil fa-fw"></i> edytuj
                        </a>
                    @endif
                </div>
                <table class="table table-condensed table-hover">
                    <tr>
                        <td class="text-right" width="130"><label>Nazwa:</label></td>
                        <Td>{{ $branch->short_name }}</td>
                        <td class="text-right"><label>Telefon:</label></td>
                        <Td>{{ $branch->phone }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Adres:</label></td>
                        <td>{{ $branch->address }} {{ $branch->voivodeship ? '<br>'.$branch->voivodeship->name : '' }}</td>
                        <td class="text-right">
                            <label>Email główny:</label>
                        </td>
                        <td>{{ $branch->email }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            <label>Email dodatkowy:</label>
                        </td>
                        <td>{{ $branch->other_emails }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Zakres:</label></td>
                        <td colspan="3">{{ implode('; ', $branch->typegarages->lists('name')) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>NIP:</label></td>
                        <td>{{ $branch->nip }}</td>
                        <td class="text-right"><label>Priorytet:</label></td>
                        <td>{{ $branch->priority }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Osoby kont.</label></td>
                        <td>{{ $branch->contact_people }}</td>
                        <td class="text-right"><label>Ilość aut zastępczych</label></td>
                        <td>
                            @foreach($branch->typevehicle as $typevehicle)
                                {{ $typevehicle->name }}: {{ $typevehicle->pivot->value }};
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Posiada holownik:</label></td>
                        <td>
                            @if($branch->tug == 1)
                                <i class="fa fa-check"></i>
                                @if($branch->tug_remarks)
                                    <span class="label label-info" data-toggle="tooltip" data-placement="top" title="{{ $branch->tug_remarks }}">
                                        <i class="fa fa-question-circle"></i>
                                    </span>
                                @endif
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                        <td class="text-right"><label>Dostępność hol. 24h</label></td>
                        <td>
                            @if($branch->tug == 1&& $branch->tug24h == 1)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-minus"></i>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Godziny pracy:</label></td>
                        <td>{{ substr($branch->open_time, 0, -3) }} - {{ substr($branch->close_time, 0 , -3) }}</td>
                        <td class="text-right"><label>Posiadane autoryzacje:</label></td>
                        <td>{{ implode(', ', $branch->authorizations->lists('name') ) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Uwagi:</label></td>
                        <td>{{ $branch->remarks }}</td>
                        <td class="text-right"><label>Kierowalność/priorytety:</label></td>
                        <td>{{ $branch->priorities }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default small">
                <div class="panel-heading">
                    Obsługiwane marki
                    <a href="{{ url('company/garages/edit-branch-brands', $branch->id)}}" class="btn btn-warning btn-xs pull-right">
                        <i class="fa fa-pencil fa-fw"></i>
                        zmień
                    </a>
                </div>
                <table class="table table-condensed table-hover">
                    <thead>
                        <th>Nazwa</th>
                        <th>Typ</th>
                        <th>Czy autoryzowany</th>
                    </thead>
                    <?php $has_multibrand = false?>
                    @foreach($branch->branchBrands->sortBy(function($branchBrand){ return $branchBrand->brand->name; }) as $branchBrand)
                        @if(!$branchBrand->if_multibrand)
                            <tr>
                                <td>
                                    {{ $branchBrand->brand->name }}
                                </td>
                                <td>
                                    {{ $branchBrand->brand->typ == 1 ? 'osobowe' : 'ciężarowe' }}
                                </td>
                                <td>
                                    {{ $branchBrand->authorization ? 'tak' : 'nie' }}
                                </td>
                            </tr>
                        @else
                            <?php $has_multibrand = true ?>
                        @endif
                    @endforeach
                    @if($has_multibrand)
                        <tr><td colspan="3"><button id="show_multi" class="btn btn-xs btn-success pull-left"><i class="fa fa-plus"></i> Wielomarkowe</button></td></tr>
                        <tbody  style="display: none" id="multibrands">
                        @foreach($branch->branchBrands->sortBy(function($branchBrand){ return $branchBrand->brand->name; }) as $branchBrand)
                            @if($branchBrand->if_multibrand)
                                <tr>
                                    <td>
                                        {{ $branchBrand->brand->name }}
                                    </td>
                                    <td>
                                        {{ $branchBrand->brand->typ == 1 ? 'osobowe' : 'ciężarowe' }}
                                    </td>
                                    <td>
                                        {{ $branchBrand->authorization ? 'tak' : 'nie' }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default small">
                <div class="panel-heading">
                    Lista programów
                    <a href="{{ url('company/garages/attach-plan', [$branch->id]) }}" class="btn btn-xs btn-primary pull-right">
                        <i class="fa fa-chain fa-fw"></i>
                        dołącz
                    </a>
                </div>
                <table class="table  table-hover  table-condensed">
                    <thead>
                    <th>#</th>
                    <th>program</th>
                    <th>grupa</th>
                    <th>marki</th>
                    <th>czy sprzedał</th>
                    <th></th>
                    <th></th>
                    </thead>
                    <?php $lp = 1; ?>
                    @foreach ($branch->branchPlanGroups as $k => $branchPlanGroup)
                        @forelse($branchPlanGroup->branchBrands as $i => $branchBrand)
                        <tr class="vertical-middle">
                            @if($i == 0)
                                <td rowspan="{{ count($branchPlanGroup->branchBrands) }}" class="vertical-middle">{{ $lp++ }}.</td>

                                <td rowspan="{{ count($branchPlanGroup->branchBrands) }}" class="vertical-middle">
                                    {{ $branchPlanGroup->planGroup->plan->name  }}
                                </td>
                                <td rowspan="{{ count($branchPlanGroup->branchBrands) }}" class="vertical-middle">
                                    {{ $branchPlanGroup->planGroup->name  }}
                                </td>
                            @endif
                                <td>
                                    {{ $branchBrand->brand->name }}
                                </td>
                                <td>
                                    {{ $branchBrand->pivot->if_sold ? 'tak' : 'nie' }}
                                </td>
                            @if($i == 0)
                                <td rowspan="{{ count($branchPlanGroup->branchBrands) }}" class="vertical-middle">
                                    <a href="{{ url('company/garages/edit-group', [$branchPlanGroup->id]) }}" class="btn btn-warning btn-xs">
                                        <i class="fa fa-pencil fa-fw"></i> zmień
                                    </a>
                                </td>
                                <td rowspan="{{ count($branchPlanGroup->branchBrands) }}" class="vertical-middle">
                                    <span target="{{ url('company/garages/delete-branch-plan-group', [$branchPlanGroup->id]) }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-trash fa-fw"></i> usuń
                                    </span>
                                </td>
                            @endif
                        </tr>
                        @empty
                        @if($branchPlanGroup->planGroup()->exists())
                            <tr class="vertical-middle">
                                <td class="vertical-middle">{{ $lp++ }}.</td>

                                <td class="vertical-middle">
                                    {{ $branchPlanGroup->planGroup->plan->name  }}
                                </td>
                                <td class="vertical-middle">
                                    {{ $branchPlanGroup->planGroup->name  }}
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td class="vertical-middle">
                                    <a href="{{ url('company/garages/edit-group', [$branchPlanGroup->id]) }}" class="btn btn-warning btn-xs">
                                        <i class="fa fa-pencil fa-fw"></i> zmień
                                    </a>
                                </td>
                                <td  class="vertical-middle">
                                    <span target="{{ url('company/garages/delete-branch-plan-group', [$branchPlanGroup->id]) }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                        <i class="fa fa-trash fa-fw"></i> usuń
                                    </span>
                                </td>
                            </tr>
                        @endif
                        @endforelse
                    @endforeach
                </table>
            </div>
        </div>
    </div>



@stop
@section('headerJs')
    @parent
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
            
            $('#show_multi').on('click', function(){
                if($('#multibrands').css('display') == 'none')$('#multibrands').show(300);
                else $('#multibrands').hide(300);
            });
        });
    </script>
@stop
