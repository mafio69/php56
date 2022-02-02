<div class="tab-pane fade in" id="objects-data">
    @if(is_null($agreement->archive) && Auth::user()->can('kartoteka_polisy#zarzadzaj'))
        <h4 class="page-header marg-top-min overflow">
            <button class="btn btn-primary pull-right modal-open" target="{{ URL::to('insurances/info-dialog/create-object', [$agreement->id]) }}" data-toggle="modal" data-target="#modal"><i class="fa fa-plus"></i> dodaj przedmiot umowy</button>
        </h4>
    @endif
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
            <table class="table table-condensed table-hover">
                <thead>
                    <th>lp.</th>
                    <th>nazwa przedmiotu</th>
                    <th>kategoria</th>
                    <th>Wart. z faktury netto przedm. umowy pożyczki</th>
                    <Th>Wart. brutto przedm. umowy pożyczki</Th>
                    <th></th>
                    <th></th>
                </thead>
                @foreach($agreement->objects as $k => $object)
                    <tr>
                        <td><b>{{ ++$k }}</b>.</td>
                        <Td>{{ $object->name }}</td>
                        <Td>
                            @if( $object->object_assetType )
                                {{ $object->object_assetType->name }}
                            @else
                                ---
                            @endif
                        </td>
                        <Td>{{ number_format($object->net_value,2,"."," ") }} zł</td>
                        <Td>{{ number_format($object->gross_value,2,"."," ") }} zł</td>
                        <td>
                            @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                <i class="fa fa-trash tips modal-open" target="{{ URL::to('insurances/info-dialog/delete-object', [$object->id]) }}" data-toggle="modal" data-target="#modal" title="usuń" style="font-size: 17px;cursor: pointer;"></i>
                            @endif
                        </td>
                        <td>
                            @if(Auth::user()->can('kartoteka_polisy#zarzadzaj'))
                                <i class="fa fa-pencil-square-o tips modal-open" target="{{ URL::to('insurances/info-dialog/edit-object', [$object->id]) }}" data-toggle="modal" data-target="#modal" title="edytuj" style="font-size: 17px;cursor: pointer;"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

    </div>



</div>
