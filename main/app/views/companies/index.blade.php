@extends('layouts.main')


@section('header')

    @if($group)
        Serwisy w grupie <i>{{ $group->name }}</i>
    @endif
    <div class="pull-right">
        @if(Auth::user()->can('serwisy#zarzadzaj'))
        <span class="btn btn-primary fileinput-button let_disable" id="upload-btn">
            <span class="glyphicon glyphicon-plus-sign "></span> Importuj opiekunów z pliku</a>
                <form id="fileupload" method="POST">
                    {{ Form::token() }}
                    <input type="file" name="file" >
                </form>
            </span>
        @endif
        @if(Auth::user()->can('serwisy#dodaj_firme'))
            <a href="{{ URL::to('companies/create') }}" class="btn btn-small btn-primary iframe"><span class="glyphicon glyphicon-plus-sign"></span> Dodaj firmę</a>
        @endif
        @if(Auth::user()->can('serwisy#stworz_grupe'))
            <span target="{{ URL::to('companies/create-group') }}" class="btn btn-primary btn-small modal-open" data-toggle="modal" data-target="#modal">
                <span class="glyphicon glyphicon-plus-sign"></span> Stwórz grupę
            </span>
        @endif
    </div>

@stop

@section('main')
    @if($group)
    <div class="panel panel-primary">
        <div class="panel-heading">
            Właściele przypisani do grupy
            @if(Auth::user()->can('serwisy#zarzadzaj'))
                <span target="{{ URL::to('companies/edit-group', [$group->id]) }}" class="btn btn-xs btn-info pull-right modal-open" data-toggle="modal" data-target="#modal">
                    <i class="fa fa-pencil"></i> edytuj grupę
                </span>
            @endif
        </div>
        <div class="panel-body">
            @foreach($owners as $owner_id => $owner)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    {{ $owner }}
                    @if($group->owners->contains($owner_id))
                        <i class="fa fa-check marg-left"></i>
                    @else
                        <i class="fa fa-minus marg-left"></i>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <ul class="nav nav-tabs marg-btm">
        @foreach($groups as $group_id => $group_name)
            <li role="presentation" @if( $group_id == 0 && is_null($group)) class="active" @elseif(!is_null($group) && $group_id == $group->id) class="active" @endif>
                <a href="{{ URL::to('companies/index', [$group_id]) }}">{{ $group_name }}</a>
            </li>
        @endforeach
        <div class="pull-right" style="width: 350px;">
            @if($group)
                {{ Form::open(array('url' => 'companies/search/'.$group->id, 'method' => 'post', 'id' => 'search-form')) }}
            @else
                {{ Form::open(array('url' => 'companies/search/', 'method' => 'post', 'id' => 'search-form')) }}
            @endif
                <div class="input-group">
                    <input type="text" name="term" value="{{ htmlentities(Session::get('search_company')) }}" id="search_garage" class="form-control" placeholder="lokalizacja/nazwa/ulica warsztatu w grupie...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="search_garage_btn"><i class="fa fa-search"></i></button>
                        </span>
                </div>
            {{ Form::close() }}
        </div>
    </ul>
    <div class="table-responsive">
        <table class="table  table-hover  table-condensed">
            <thead>
            <th></th>
            <th >nazwa</th>
            <th>grupa kontrahenta</th>
            <th >adres</th>
            <th >nip</th>
            <th >krs</th>
            <Th>regon</th>
            <th>nr konta</th>
            <th>www</th>
            <th>email</th>
            <th>telefon</th>
            @if($group && $group->id == 1)
            <th>status VAT</th>
            @endif
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            </thead>
            <?php $lp = (($companies->getCurrentPage()-1)*$companies->getPerPage()) + 1; ?>
            @foreach ($companies as $k => $company)
                <tr class="vertical-middle">
                    <td>{{ $lp++ }}.</td>
                    <Td>{{$company->name}}
                        <div style="margin-top:3px;">
                            @if(isset($typegaragesA[$company->id]))
                                @foreach($typegaragesA[$company->id] as $k => $v)
                                    @if($k == 1)
                                        <span class="ico-lg lakier_o tips" title="blacharsko-lakierniczy (osobowe)"></span>
                                    @elseif($k == 10)
                                        <span class="ico-lg lakier_c tips" title="blacharsko-lakierniczy (ciężarowe)"></span>
                                    @elseif($k == 2 )
                                        <span class="ico-lg mechaniczna_o tips" title="mechaniczny (osobowe)"></span>
                                    @elseif($k == 8)
                                        <span class="ico-lg mechaniczna_c tips" title="mechaniczny (ciężarowe)"></span>
                                    @elseif($k == 3)
                                        <span class="ico-lg wulkanizator_o tips" title="wulkanizacyjny (osobowe)"></span>
                                    @elseif($k == 9)
                                        <span class="ico-lg wulkanizator_c tips" title="wulkanizacyjny (ciężarowe)"></span>
                                    @elseif($k == 4 )
                                        <span class="ico-lg stacja_p_o tips" title="diagnostyka podstawowa (osobowe)"></span>
                                    @elseif($k == 5)
                                        <span class="ico-lg stacja_p_c tips" title="diagnostyka podstawowa (ciężarowe)"></span>
                                    @elseif($k == 6)
                                        <span class="ico-lg stacja_r_o tips" title="diagnostyka okręgowa (osobowe)"></span>
                                    @elseif($k == 7)
                                        <span class="ico-lg stacja_r_c tips" title="diagnostyka okręgowa (ciężarowe)"></span>
                                    @endif
                                @endforeach
                            @endif
                            @if($company->hasTug24h == 1)
                                <span class="ico-md holowanie24 tips" title="holownik 24h"></span>
                            @elseif($company->hasTug == 1)
                                <span class="ico-md holowanie tips" title="holownik"></span>
                            @endif
                        </div>
                    </td>
                    <td>
                        {{ $company->contractorGroup ? $company->contractorGroup->name : '' }}
                    </td>
                    <td>{{$company->code.' '.$company->city.' - '.$company->street}}</td>
                    <td>{{$company->nip}}</td>
                    <Td>{{$company->krs}}</td>
                    <td>{{$company->regon}}</td>
                    <td>{{ ($company->account_nr != NULL) ? $company->account_nr : '---' }} </td>
                    <td>{{$company->www}}</td>
                    <td>{{$company->email}}</td>
                    <td>{{$company->phone}}</td>
                    @if($group && $group->id == 1)
                        <td>
                            @if($company->companyVatCheck)
                                <a tabindex="0" class="btn btn-xs @if($company->companyVatCheck->status_code == 'C') btn-info @else btn-danger @endif btn-popover" role="button" data-toggle="popover" data-trigger="hover" data-content="{{ $company->companyVatCheck->status }}" style="overflow: hidden;white-space: nowrap; text-overflow: ellipsis; max-width: 100px;">
                                    <i class="fa fa-info-circle"></i> {{ $company->companyVatCheck->status }}
                                </a>
                            @endif
                        </td>
                    @endif
                    <td>
                        @if($company->remarks != '')
                            <a tabindex="0" class="btn btn-xs btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="adnotacje" data-content="{{ $company->remarks }}">
                                <i class="fa fa-info-circle"></i> adnotacje
                            </a>
                        @endif
                    </td>
                    <td>
                        <a href="{{ URL::to('companies/show', array($company->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search fa-fw"></i> podgląd</a>
                    </td>
                    <td>
                        @if(Auth::user()->can('serwisy#zarzadzaj'))
                            <a href="#" target="{{ URL::to('companies/groups', [$company->id]) }}" class="btn btn-info btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-arrows-h fa-fw"></i> przypisane grupy</a>
                        @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('serwisy#zarzadzaj'))
                            <a href="{{ URL::to('companies/pair', [$company->id]) }}" class="btn btn-warning btn-xs"><i class="fa fa-chain fa-fw"></i> sparuj</a>
                        @endif
                    </td>
                    <td>
                        @if(Auth::user()->can('serwisy#zarzadzaj'))
                            <a href="#" target="{{ URL::to('companies/delete',[$company->id]) }}" class="btn btn-danger btn-xs modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-trash fa-fw"></i> usuń</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="pull-right" style="clear:both;">{{ $companies->appends(Input::all())->links() }}</div>
    </div>


@stop

@section('headerJs')
    @parent
    <script type="text/javascript">
    $(function () {
        var url = "{{ URL::route('company.guardians.post', ['postUploadGuardiansFile'] ) }}";
        $('#fileupload').fileupload({
            singleFileUploads: true,
            url: url,
            dataType: 'json',
            add: function (e, data) {
                var dialog_href= "{{ URL::route('company.guardians.get', ['getUploadGuardiansFileDialog'] ) }}";
				$.get( dialog_href, function( data ) {
				  $('#modal .modal-content').html(data);
				});
				$('#modal').modal('show');

                if (e.isDefaultPrevented()) {
                    return false;
                }
                if (data.autoUpload || (data.autoUpload !== false &&
                        $(this).fileupload('option', 'autoUpload'))) {
                    data.process().done(function () {
                        data.submit();
                    });
                }
				
                },
                done: function (e, data) {
                var response = data.result;
                setTimeout(
                    function(){
                        if(response.status == 'error'){
                            $('#guardiansUploadDialog').html(response.msg);
                            $('#guardiansUploadDialogClose').removeAttr('disabled');
                        } else if(response.status == 'success'){
                            $('#progress').html('<h3>Wgrano opiekunów serwisu</h3>')
                        }
                    }
                , 200);
            },
            })
        });

        $(document).ready(function() {
            $('#search_garage').keypress(function(e) {
                if(e.which == 13) {
                    $('#search-form').submit();
                }
            });
            $('#search_garage_btn').click(function(){
                $('#search-form').submit();
            });
        });
    </script>
@stop
