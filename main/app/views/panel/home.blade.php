@extends('layouts.main')

@section('header')
Witamy!

@stop

@section('main')
    @if(Auth::user()->can('zablokowani_uzytkownicy#zablokowani_uzytkownicy'))
        <div class="col-lg-4 ">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    Zablokowane konta
                </div>
                @if($locked_users->count() > 0)
                    <div class="panel-body">
                        <table class="table table-hover table-condensed">
                            <thead>
                            <th>#</th>
                            <th>Imię i nazwisko</th>
                            <th>Login</th>
                            <th>Data zablokowania</th>
                            <th></th>
                            </thead>
                            <?php $lp =1;?>
                            @foreach($locked_users as $user)
                                @if(is_null($user->locked_manual))
                                    <tr>
                                        <td>{{ $lp++ }}.</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->login }}</td>
                                        <td>
                                            {{\Carbon\Carbon::parse($user->locked_at)->format('Y-m-d H:i')}}
                                        </td>
                                        <td>
                                            <button target="{{ URL::to('settings/users/unlock', [$user->id]) }}" class="btn btn-primary btn-xs modal-open" data-toggle="modal" data-target="#modal">
                                                <i class="fa fa-unlock"></i> Odblokuj
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(Auth::user()->can('nieczynni_platnicy_vat#nieczynni_platnicy_vat'))
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Nieczynni płatnicy VAT
                </div>
                @if($non_vat_companies->count() > 0)
                    <div class="panel-body">
                        <table class="table table-hover table-condensed">
                            <thead>
                            <th>#</th>
                            <th>nazwa</th>
                            <th>nip</th>
                            <th>status VAT</th>
                            <th>data sprawdzenia</th>
                            </thead>
                            @foreach($non_vat_companies as $k => $company)
                                <tr>
                                    <td>{{ ++$k }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->nip }}</td>
                                    <td>{{ $company->companyVatCheck ? $company->companyVatCheck->status : ''}}</td>
                                    <td>{{ $company->companyVatCheck ? $company->companyVatCheck->updated_at->format('Y-m-d H:i') : '' }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Zmiany statusu z nieaktywnego płatnika na aktywnego
                </div>
                @if(count($changes_vat_companies) > 0)
                    <div class="panel-body">
                        <table class="table table-hover table-condensed">
                            <thead>
                            <th>#</th>
                            <th>nazwa</th>
                            <th>nip</th>
                            <th>status VAT</th>
                            <th>data sprawdzenia</th>
                            </thead>
                            @foreach($changes_vat_companies as $k => $company)
                                <tr>
                                    <td>{{ ++$k }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->nip }}</td>
                                    <td>{{ $company->companyVatCheck ? $company->companyVatCheck->status : ''}}</td>
                                    <td>{{ $company->companyVatCheck ? $company->companyVatCheck->created_at->format('Y-m-d H:i') : ''}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif


@stop

