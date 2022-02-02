@extends('layouts.main')

@section('header')

    Grupy stawek dla {{ $insuranceCompanies[$selected_group->insurance_company_id] }}

    @if(Auth::user()->can('wykaz_stawek#zarzadzaj'))
        @if(is_null($selected_group->valid_from))
            <div class="pull-right">
                <button target="{{ URL::to('insurances/groups/create', [$selected_group->id]) }}" class="btn btn-sm btn-primary modal-open" data-toggle="modal" data-target="#modal">
                    <span class="glyphicon glyphicon-plus-sign"></span> Dodaj stawkę
                </button>
                <button target="{{ URL::to('insurances/groups/close', [$selected_group->id]) }}" class="btn btn-sm btn-warning modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    <span class="fa fa-check"></span> Zakończ wprowadzanie
                </button>
            </div>
        @elseif(!is_null( $last_group->valid_from ))
            <div class="pull-right">
                <a href="{{ URL::to('insurances/groups/base', [$selected_group->id]) }}" class=" btn btn-sm btn-info " >
                    <span class="fa fa-plus"></span> Zdefiniuj nowe stawki na bazie wybranych
                </a>
            </div>
            <div class="pull-right">
                <a href="{{ URL::to('insurances/groups/fresh', [$selected_group->id]) }}" class=" btn btn-sm btn-primary marg-right" >
                    <span class="fa fa-plus"></span> Zdefiniuj nowe stawki
                </a>
            </div>
        @endif
    @endif
@stop

@section('sub-header')
        @if(is_null($selected_group->valid_from))
            <h4 class="lead marg-btm overflow text-primary">W trakcie definiowania
        @elseif(is_null($selected_group->valid_to))
            <h4 class="lead marg-btm overflow text-success">Aktualnie obowiązujące od {{ $selected_group->valid_from }}
        @else
            <h4 class="lead marg-btm overflow text-danger">Obowiązujące od {{ $selected_group->valid_from }} do {{ $selected_group->valid_to }}
        @endif

        <div class="pull-right marg-left">
            <label for="group_select"><small>Wybierz grupę stawek:</small></label>
            {{ Form::select('group_select', $groupsA, $selected_group->id, array('class' => 'form-control', 'id' => 'group_select')) }}
        </div>
        <div class="pull-right marg-right">
            <label for="group_select"><small>Wybierz ubezpieczyciela:</small></label>
            <div class="input-group" style="width: 300px;">
                {{ Form::select('insurance_company_id', $insuranceCompanies, $selected_group->insurance_company_id, array('class' => 'form-control', 'id' => 'insurance_company_id')) }}
                @if(Auth::user()->can('wykaz_stawek#zarzadzaj'))
                    <span class="input-group-btn tips" title="dodaj ubezpieczalnię" >
                        <button target="{{ URL::to('insurances/groups/assign-insurance-company') }}" class="btn btn-default modal-open" data-toggle="modal" data-target="#modal" type="button"><i class="fa fa-plus"></i></button>
                    </span>
                @endif
            </div>
        </div>
    </h4>
@stop

@section('main')
    <div class="marg-top">
        <table class="table table-bordered table-condensed table-middle table-auto  table-hover " id="users-table">
            <thead>
                <th >lp.</th>
                <th>Umowa generalna</th>
                <th>Symbol produktu</th>
                <th>Symbol elementu</th>
                <th>Prowizja</th>
                <th>Nazwa stawki</th>
                <th>12 m-cy</th>
                <th>24 m-ce</th>
                <th>36 m-cy</th>
                <th>48 m-cy</th>
                <th>60 m-cy</th>
                <th>72 m-ce</th>
                <th>84 m-ce</th>
                <th>96 m-ce</th>
                <th>108 m-ce</th>
                <th>120 m-ce</th>
                    <th></th>
                    <th></th>
            </thead>

            @foreach ($selected_group->rows as $k => $row)
                <tr>
                    <td>{{++$k}}.</td>
                    <td>
                        @if(is_null($selected_group->valid_from))
                            <a href="#" class="popup-editable" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="general_contract" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź umowę generalną">
                                {{ $row->general_contract }}
                            </a>
                        @else
                            {{ $row->general_contract }}
                        @endif
                    </td>
                    <td>
                        @if( is_null($selected_group->valid_from))
                            <a href="#" class="popup-editable" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="symbol_product" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź symbol produktu">
                                {{ $row->symbol_product }}
                            </a>
                        @else
                            {{ $row->symbol_product }}
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from))
                            <a href="#" class="popup-editable" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="symbol_element" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź symbol elementu">
                                {{ $row->symbol_element }}
                            </a>
                        @else
                            {{ $row->symbol_element }}
                        @endif
                    </td>
                    <td>
                        @if( is_null($selected_group->valid_from))
                            <a href="#" class="popup-editable" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="commission" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź prowizję">
                                @if($row->commission)
                                    {{$row->commission}} %
                                @else
                                    ---
                                @endif
                            </a>
                        @else
                            @if($row->commission)
                                {{$row->commission}} %
                            @else
                                ---
                            @endif
                        @endif
                    </td>
                    <Td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable"  data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="leasing_agreement_insurance_group_rate_id"
                                        data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź nazwę stawki">
                                {{ $row->rate->name }}
                            </a><br/>
                            {{ Form::open( array('url' => URL::to('insurances/groups/add-package', [$row->id]) ) ) }}
                                <button type="submit" class="btn btn-primary btn-xs marg-top-min">
                                    <i class="fa fa-plus fa-fw"></i> pakiet
                                </button>
                            {{ Form::close() }}
                        @else
                            {{ $row->rate->name }}
                        @endif
                        @if($row->packages->count() > 0)
                            <br/>
                            <span class="btn btn-info btn-xs marg-top-min show-packages" data-row="{{ $row->id }}">
                                <i class="fa fa-search fa-fw"></i>
                                pokaż pakiety <span class="badge">{{ $row->packages->count() }}</span>
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_12" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_12 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_12" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                        {{ $row->minimal_12 }}
                                    </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_12 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_12 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_24" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_24 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_24" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_24 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_24 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_24 }} zł
                            @endif
                        @endif
                    </td>
                    <Td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_36" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_36 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_36" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_36 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_36 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_36 }} zł
                            @endif
                        @endif
                    </Td>
                    <td>
                        @if( is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_48" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_48 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_48" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_48 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_48 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_48 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if( is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_60" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_60 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_60" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_60 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_60 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_60 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if( is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_72" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_72 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_72" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_72 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_72 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_72 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_84" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_84 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_84" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_84 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_84 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_84 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_96" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_96 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_96" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_96 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_96 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_96 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_108" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_108 }}
                            </a>  %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_108" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_108 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_108 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_108 }} zł
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                            <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="months_120" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. %">
                                {{ $row->months_120 }}
                            </a> %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $row->id }}" data-token="{{ csrf_token() }}" data-name="minimal_120" data-url="{{ URL::to('insurances/groups/update', [$row->id]) }}" data-title="Wprowadź wart. w zł">
                                    {{ $row->minimal_120 }}
                                </a>
                                zł
                            @endif
                        @else
                            {{ $row->months_120 }} %
                            @if($row->if_minimal == 1)
                                <br/>
                                min. {{ $row->minimal_120 }} zł
                            @endif
                        @endif
                    </td>
                    @if( is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                        <td>
                            <button target="{{ URL::to('insurances/groups/delete', [$row->id] ) }}" class="btn btn-danger btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"><i class="fa fa-trash-o"></i> usuń</button>
                        </td>
                        <td>
                            @if($row->if_minimal == 0)
                                {{ Form::open( array('url' => URL::to('insurances/groups/generate-min-row', [$row->id]) ) ) }}
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus fa-fw"></i>
                                        wartości min.
                                    </button>
                                {{ Form::close() }}
                            @else
                                {{ Form::open( array('url' => URL::to('insurances/groups/remove-min-row', [$row->id]) ) ) }}
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-minus fa-fw"></i>
                                    wartości min.
                                </button>
                                {{ Form::close() }}
                            @endif
                        </td>
                    @endif
                </tr>
                @if($row->packages->count() > 0)
                    @foreach($row->packages as $package)
                        <tr class="package-{{ $row->id }} info" style="display: none;">
                            <td>#</td>
                            <td colspan="3"></td>
                            <Td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    pakiet:
                                    <a href="#" class="popup-editable"  data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="name"
                                       data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź nazwę pakietu">
                                        {{ $package->name }}
                                    </a>
                                @else
                                    {{ $package->name }}
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_12_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_12_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_12_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_12_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_12_percentage)
                                        {{ $package->months_12_percentage }} %
                                    @else
                                        {{ $package->months_12_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_24_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_24_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_24_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_24_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_24_percentage)
                                        {{ $package->months_24_percentage }} %
                                    @else
                                        {{ $package->months_24_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <Td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_36_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_36_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_36_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_36_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_36_percentage)
                                        {{ $package->months_36_percentage }} %
                                    @else
                                        {{ $package->months_36_amount }} zł
                                    @endif
                                @endif
                            </Td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_48_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_48_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_48_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_48_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_48_percentage)
                                        {{ $package->months_48_percentage }} %
                                    @else
                                        {{ $package->months_48_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_60_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_60_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_60_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_60_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_60_percentage)
                                        {{ $package->months_60_percentage }} %
                                    @else
                                        {{ $package->months_60_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if( is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_72_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_72_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_72_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_72_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_72_percentage)
                                        {{ $package->months_72_percentage }} %
                                    @else
                                        {{ $package->months_72_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_84_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_84_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_84_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_84_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_84_percentage)
                                        {{ $package->months_84_percentage }} %
                                    @else
                                        {{ $package->months_84_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_96_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_96_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_96_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_96_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_96_percentage)
                                        {{ $package->months_96_percentage }} %
                                    @else
                                        {{ $package->months_96_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_108_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_108_percentage }}
                                    </a>  %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_108_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_108_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_108_percentage)
                                        {{ $package->months_108_percentage }} %
                                    @else
                                        {{ $package->months_108_amount }} zł
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                    <a href="#" class="popup-editable" data-inputclass="currency_input" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_120_percentage" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź stawkę">
                                        {{ $package->months_120_percentage }}
                                    </a> %
                                    <br/>
                                    <a href="#" class="popup-editable" data-inputclass="number" data-type="text" data-pk="{{ $package->id }}" data-token="{{ csrf_token() }}" data-name="months_120_amount" data-url="{{ URL::to('insurances/groups/update-package', [$package->id]) }}" data-title="Wprowadź wart. min.">
                                        {{ $package->months_120_amount }}
                                    </a>
                                    zł
                                @else
                                    @if($package->months_120_percentage)
                                        {{ $package->months_120_percentage }} %
                                    @else
                                        {{ $package->months_120_amount }} zł
                                    @endif
                                @endif
                            </td>
                            @if(is_null($selected_group->valid_from) && Auth::user()->can('wykaz_stawek#zarzadzaj'))
                                <td>
                                    <button target="{{ URL::to('insurances/groups/delete-package', [$package->id] ) }}" class="btn btn-danger btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                                        <i class="fa fa-trash-o"></i> usuń
                                    </button>
                                </td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </table>
    </div>


@stop


@section('headerJs')
    @parent

    @if(Auth::user()->can('wykaz_stawek#zarzadzaj'))
    <script type="text/javascript">
        $(document).ready(function() {
            $('.popup-editable').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function(value) {
                    if($.trim(value) == '') {
                        return 'Pole wymagane';
                    }
                },
                success: function(response, newValue) {
                    if(!response) {
                        return "Unknown error!";
                    }
                    if(response.success === false) {
                        return response.msg;
                    }else{
                        $.notify({
                            icon: "fa fa-check",
                            message: response.notification
                        },{
                            type: 'success',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            },
                            delay: 2500,
                            timer: 500
                        });
                        if(isset(response.data_name))
                        {
                            $('a[data-name="' + response.data_name + '"]').html('Empty').addClass('editable-empty');
                        }
                    }
                }
            });

            $('#group_select').on('change', function(){
               self.location = '/insurances/groups/index/'+$('#insurance_company_id').val()+'/'+$(this).val();
            });

            $('#insurance_company_id').on('change', function(){
                self.location = '/insurances/groups/index/'+$(this).val();
            });

            $('.show-packages').on('click', function(){
                var $row = $(this).data('row');
                $('.package-' + $row).toggle();
            });
        });
    </script>
    @endif
@stop
