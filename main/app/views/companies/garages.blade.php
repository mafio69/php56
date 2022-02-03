@extends('layouts.main')


@section('header')

Warsztaty należące do <i>{{$company->name }}</i><br/>

<div class="pull-right">

    <a href="{{ URL::to('companies/index', [$company->company_group_id]) }}" class="btn btn-default">Powrót</a>
    @if(Auth::user()->can('serwisy#warsztaty#dodaj_warsztat'))
        <a href="{{ URL::to('company/garages/create', array($company->id)) }}" class="btn btn-small btn-primary iframe">
            <span class="glyphicon glyphicon-plus-sign"></span> Dodaj warsztat
        </a>
    @endif
</div>

@stop

@section('main')

    <div class="table-responsive">
        <table class="table  table-hover table-condensed" >
            <thead>
                <th></th>
								<th></th>
                <th>nazwa skrócona</th>
                <th>adres</th>
                <th>email</th>
                <th>telefon</th>
                <th>priorytet</th>
                <Th>uwagi</th>
                <th>marki osobowe</th>
                <th>marki ciężarowe</th>
                <th></th>
                <th></th>
            </thead>

            <?php $lp = (($garages->getCurrentPage()-1)*$garages->getPerPage()) + 1; ?>
            @foreach ($garages as $k => $garage)
                <tr class="vertical-middle">
                    <Td>{{ $lp++ }}.</Td>
										<th>@if($garage->suspended==1)<i class="fa fa-exclamation fa-2x" data-toggle="tooltip" data-placement="top" title="Zawieszony"></i>@endif</th>
                    <td>
                        {{$garage->short_name}}
                        <div style="margin-top:3px;">
                        @foreach($garage->typegarages as $k => $v)
                            @if($v->typegarages_id == 1)
                                <span class="ico-lg lakier_o tips" title="blacharsko-lakierniczy (osobowe)"></span>
                            @elseif($v->typegarages_id == 10)
                                <span class="ico-lg lakier_c tips" title="blacharsko-lakierniczy (ciężarowe)"></span>
                            @elseif($v->typegarages_id == 2 )
                                <span class="ico-lg mechaniczna_o tips" title="mechaniczny (osobowe)"></span>
                            @elseif($v->typegarages_id == 8)
                                <span class="ico-lg mechaniczna_c tips" title="mechaniczny (ciężarowe)"></span>
                            @elseif($v->typegarages_id == 3)
                                <span class="ico-lg wulkanizator_o tips" title="wulkanizacyjny (osobowe)"></span>
                            @elseif($v->typegarages_id == 9)
                                <span class="ico-lg wulkanizator_c tips" title="wulkanizacyjny (ciężarowe)"></span>
                            @elseif($v->typegarages_id == 4 )
                                <span class="ico-lg stacja_p_o tips" title="diagnostyka podstawowa (osobowe)"></span>
                            @elseif($v->typegarages_id == 5)
                                <span class="ico-lg stacja_p_c tips" title="diagnostyka podstawowa (ciężarowe)"></span>
                            @elseif($v->typegarages_id == 6)
                                <span class="ico-lg stacja_r_o tips" title="diagnostyka okręgowa (osobowe)"></span>
                            @elseif($v->typegarages_id == 7)
                                <span class="ico-lg stacja_r_c tips" title="diagnostyka okręgowa (ciężarowe)"></span>
                            @endif
                        @endforeach
                        @if($garage->tug24h == 1)
                            <span class="ico-md holowanie24 tips" title="holownik 24h"></span>
                        @elseif($garage->tug == 1)
                            <span class="ico-md holowanie tips" title="holownik"></span>
                        @endif
                        </div>
                    </td>
                    <td>
                        {{$garage->code.' '.$garage->city.' - '.$garage->street}}<br/>
                        {{ ($garage->voivodeship) ? $garage->voivodeship->name : '' }}
                    </td>
                    <td>{{$garage->email}}</td>
                    <td>{{$garage->phone}}</td>

                    <td>{{$garage->priority}}</td>
                    <td>{{$garage->remarks}}</td>
                    <td>
                        @foreach($garage->brands as $v)
                            @if($v->typ == 1)
                                {{$v->name}},
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($garage->brands as $v)
                            @if($v->typ == 2)
                                {{$v->name.','}}
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @if($garage->if_map == 1)
                            <a href="#" target="{{ URL::to('company/garages/show', [$garage->id]) }}" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-map-marker fa-fw"></i> mapa</a>
                        @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('serwisy#warsztaty#zarzadzaj'))
                            <a href="{{ URL::to('company/garages/edit', array($garage->id)) }}" class="btn btn-warning btn-sm">edytuj</a>
                            <a href="#" target="{{ URL::to('company/garages/delete', [$garage->id]) }}" class="btn btn-danger btn-sm modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-trash fa-fw"></i> usuń</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
	</div>
    <div class="pull-right" style="clear:both;">{{ $garages->links() }}</div>



@stop
@section('headerJs')
	@parent
	<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
	</script>
@stop
