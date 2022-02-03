@if(Auth::user()->can('wykaz_polis#zarzadzaj'))
    <div class="btn-group " style="min-width:130px;" >
        @if($leasingAgreement->has_yacht == 1)
            <a href="{{ URL::to('insurances/manage-actions/assign-to-yacht', [$leasingAgreement->id]) }}" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-file"></i> przypisz polisę</a>
        @else
            <a href="{{ URL::to('insurances/manage-actions/assign', [$leasingAgreement->id]) }}" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-file"></i> przypisz polisę</a>
        @endif
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            @if($leasingAgreement->has_yacht == 1)
                <li><a href="{{ URL::to('insurances/manage-actions/assign-to-yacht', [$leasingAgreement->id]) }}" ><i class="fa fa-file"></i> przypisz polisę</a></li>
            @else
                <li><a href="{{ URL::to('insurances/manage-actions/assign', [$leasingAgreement->id]) }}" ><i class="fa fa-file"></i> przypisz polisę</a></li>
            @endif
            <li class="divider"></li>
            <li><a href="#" target="{{ URL::to('insurances/manage-dialog/withdraw', [$leasingAgreement->id]) }}" class="modal-open" data-toggle="modal" data-target="#modal"><i class="fa fa-undo"></i> wycofaj umowę</a></li>
        </ul>
    </div>
@endif