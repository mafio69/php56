@extends('layouts.main')


@section('header')

    {{$company->name }}

    <div class="pull-right">
        <a href="{{ URL::to('companies/index', [$company->company_group_id]) }}" class="btn btn-default">Powrót</a>
    </div>

@stop

@section('main')

    <div class="row">

        <div class="col-sm-6">
            <div class="panel panel-default small">
                <div class="panel-heading ">Dane serwisu - siedziba
                    @if(Auth::user()->can('serwisy#zarzadzaj'))
                        <a href="{{ URL::to('companies/edit', array($company->id)) }}" class="btn btn-warning btn-xs pull-right"><i class="fa fa-pencil"></i> edytuj</a>
                        <span class="btn btn-primary btn-xs modal-open pull-right"
                            target={{ URL::to('companies/assign-guardian', array($company->id)) }}
                            data-toggle="modal" data-target="#modal" style="margin-right: 5px">
                            <i class="fa fa-male fa-fw"></i> przypisz opiekuna
                        </span>
                        @if(!is_null($company->guardian))
                            <span class="btn btn-danger btn-xs modal-open pull-right"
                                target={{ URL::to('companies/delete-guardian', array($company->id)) }}
                                data-toggle="modal" data-target="#modal" style="margin-right: 5px">
                                <i class="fa fa-trash fa-fw"></i> Usuń opiekuna
                            </span>
                        @endif    
                    @endif
                </div>
                <table class="table table-condensed table-hover" style="table-display:grid">
                    <tr>
                        <td class="text-right" width="25%"><label>Nazwa:</label></td>
                        <Td width="25%">{{ $company->name }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right" width="25%"><label>Telefon:</label></td>
                        <Td width="25%">{{ $company->phone }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Adres:</label></td>
                        <td>{{ $company->address }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><label>Email:</label></td>
                        <td>{{ $company->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>NIP:</label></td>
                        <td>{{ $company->nip }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><label>Numer umowy:</label></td>
                        <td>{{ $company->www }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>KRS:</label></td>
                        <td>{{ $company->krs }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><label>Nr konta:</label></td>
                        <td>{{ $company->account_nr }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Regon:</label></td>
                        <td>{{ $company->regon }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><label>Adnotacje:</label></td>
                        <td>{{ $company->remarks }}</td>
                    </tr>
                    <tr>
                        <td class="text-right"><label>Grupa kontrahenta:</label></td>
                        <td>{{ $company->contractorGroup ? $company->contractorGroup->name : '' }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><label>Grupy:</label></td>
                        <td>{{ implode('; ', $company->groups->lists('name')) }}</td>
                    </tr>
                    @if($company->guardian)
                    <tr>
                            {{$company->guardian->user->phone}}
                        <td class="text-right"><label>DR CAS: </label></td>
                        <td class="text-left">{{$company->guardian->user->name}}</td>
                        <td >
                            {{Form::token()}}
                                <input id="guardian_phone" class="form-control  " placeholder="telefon" style="width: 150px" name="phone" type="text" value='{{$company->guardian->phone}}'>    
                        </td>
                        <td>
                                <button class="btn btn-success btn-xs pull-left" id="save-phone">Zapisz
                                </button>
                                <button class="content-loader btn btn-success btn-xs pull-left" disabled style="display: none;">
                                <i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
                                </button>
                        </td>
                        <td class="text-right"><label>E-mail DR:</label></td>
                        <td class="text-left">{{$company->guardian->user->email}}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default small">
                <div class="panel-heading">Prowizje
                    @if(Auth::user()->can('serwisy#zarzadzaj'))
                        <a href="{{ URL::to('companies/commissions', array($company->id)) }}" class="btn btn-success btn-xs pull-right"><i class="fa fa-dollar fa-fw"></i> prowizje</a>
                    @endif
                </div>
                @if($company->commissionType)
                    <table class="table">
                        <tr>
                            <td class="text-right"><label>Typ prowizji:</label></td>
                            <td>{{ $company->commissionType->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-right"><label>Cykl rozliczeniowy:</label></td>
                            <td>{{ $company->billingCycle->name }}</td>
                        </tr>
                    </table>
                    @if($company->commission_type_id == 1)
                        @include('companies.commissions.commission-linear', ['readonly' => 1])
                    @elseif($company->commission_type_id == 2)
                        @include('companies.commissions.commission-threshold-amount', ['readonly' => 1])
                    @elseif($company->commission_type_id == 3)
                        @include('companies.commissions.commission-threshold-value', ['readonly' => 1])
                    @elseif($company->commission_type_id == 4)
                        @include('companies.commissions.commission-brand', ['readonly' => 1])
                    @endif
                @endif
            </div>
        </div>
        <div class="col-sm-12">
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default small">
                <div class="panel-heading">
                    Lista oddziałów

                    @if(Auth::user()->can('serwisy#warsztaty#dodaj_warsztat'))
                        <a href="{{ URL::to('company/garages/create', array($company->id)) }}" class="btn btn-xs btn-primary pull-right">
                            <span class="glyphicon glyphicon-plus-sign"></span> Dodaj warsztat
                        </a>
                    @endif
                </div>
                <table class="table  table-hover table-condensed">
                    <thead>
                    <th></th>
                    <th></th>
                    <th>nazwa</th>
                    <th>adres</th>
                    <th>nip</th>
                    <th>email</th>
                    <th>telefon</th>
                    <th>priorytet</th>
                    <Th>uwagi</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    </thead>
                    @foreach ($company->branches as $k => $garage)
                        <tr class="vertical-middle">
                            <Td width="10px">{{ ++$k }}.</Td>
                            <th >
                                @if($garage->suspended==1)<i class="fa fa-exclamation fa-2x" data-toggle="tooltip" data-placement="top" title="Zawieszony"></i>@endif
                                @if($garage->transferredCompany)
                                    <i class="fa fa-fw fa-chain tips" title="przeniesiony z: <i>{{ $garage->transferredCompany->name }}</i>"></i>
                                @endif
                            </th>
                            <td>
                                {{$garage->short_name}}
                            </td>
                            <td>
                                {{$garage->code.' '.$garage->city.' - '.$garage->street}}<br/>
                                {{ ($garage->voivodeship) ? $garage->voivodeship->name : '' }}
                            </td>
                            <td>
                                {{ $garage->nip }}
                            </td>
                            <td>{{$garage->email}}</td>
                            <td>{{$garage->phone}}</td>

                            <td>{{$garage->priority}}</td>
                            <td>{{$garage->remarks}}</td>
                            <td>
                                @if($garage->if_map == 1)
                                    <a href="#" target="{{ URL::to('company/garages/show-map', [$garage->id]) }}" class="btn btn-primary btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-map-marker fa-fw"></i> mapa</a>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('serwisy#warsztaty#zarzadzaj'))
                                    <a href="{{ URL::to('company/garages/show', array($garage->id)) }}"
                                       class="btn btn-primary btn-xs">
                                        <i class="fa fa-search fa-fw"></i> podgląd
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->can('serwisy#warsztaty#zarzadzaj'))
                                    <a href="#" target="{{ URL::to('company/garages/delete', [$garage->id]) }}"
                                       class="btn btn-danger btn-xs modal-open" data-toggle="modal"
                                       data-target="#modal">
                                        <i class="fa fa-trash fa-fw"></i> usuń
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">Rachunki bankowe serwisu</div>                       
                        <table class="table">
                            <?php $if_user_insert = false ?>
                            @if($company->accountNumbersWithTrashed->count() > 0)
                                @foreach($company->accountNumbersWithTrashed as $number)
                                    <tr>
                                        <td class="text-left" width="25%"><label>Numer rachunku:</label></td>
                                    <Td class="text-left">
                                        @if($number->if_user_insert)
                                        <?php $if_user_insert = true ?>
                                        <div class="alert {{$number->deleted_at?"alert-danger":"alert-warning"}}" role="alert" style="padding: 2px; margin: 0px">
                                            {{ $number->account_number }}
                                        </div>
                                        @else
                                        <div class="alert {{$number->deleted_at?"alert-danger":""}}" role="alert" style="padding: 2px; margin: 0px">
                                            {{ $number->account_number }}
                                        </div>
                                        @endif         
                                        </td>
                                        <td>
                                            @if(Auth::user()->can('serwisy#warsztaty#zarzadzaj'))
                                                <a href="#"
                                                   target="{{ URL::to('company/account-numbers/delete', [$number->id]) }}"
                                                   class="btn btn-danger btn-xs modal-open pull-right"
                                                   data-toggle="modal"
                                                   data-target="#modal"
                                                   {{$number->deleted_at?' disabled':''}}
                                                   >
                                                    <i class="fa fa-trash fa-fw"></i> Usuń
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><h5>Brak przypisanych numerów kont bankowych</h5></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    
                    @if(Auth::user()->can('serwisy#warsztaty#zarzadzaj'))
                        <a href="#"
                           target="{{ URL::to('company/account-numbers/create', [$company->id]) }}"
                           class="btn btn-success btn-xs btn-block modal-open pull-center" data-toggle="modal"
                           data-target="#modal">
                            <i class="fa fa-plus fa-fw"></i> Dodaj
                        </a>
                    @endif
                
                    @if($if_user_insert)
                    <div style="margin-top: 20px">
                        <div class="alert alert-danger col-sm-2" role="alert" style="padding: 0px; width: 20px; margin-right: 5px">&nbsp</div>
                        <div class="col-sm-5" style="margin-left: 5px">
                            Rachunek usunięty
                        </div>
                        <div class="alert alert-warning col-sm-2" role="alert" style="padding: 0px; width: 20px; margin-right: 5px">&nbsp</div>
                        <div class="col-sm-5" style="margin-left: 5px">
                            Rachunek wprowadzony ręcznie
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>


@endsection
@section('headerJs')
    @parent
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
            });
        
        $(this).button('loading');
        $('#save-phone').on('click', function () {
            $.ajax({
                beforeSend: function(){
                    $('.content-loader').show();
                    $('#save-phone').hide();
                },
                type: "POST",
                url: "{{ url('company-guardians/phone') }}",
                data: {
                    guardian_id: '{{$company->guardian?$company->guardian->id:null}}',
                    phone : $('#guardian_phone').val(),
                    _token: $('input[name="_token"]').val(),
                },
                
                success: function(data){
                    
                    $('.content-loader').hide();
                    $('#save-phone').show();
                    data = $.parseJSON( data );
                    if(data.code == 0) {
                        window.alert('Zaktualizowano numer telefonu');
                    }
                }
                });
            return false;
        });
       
     </script>
@stop
