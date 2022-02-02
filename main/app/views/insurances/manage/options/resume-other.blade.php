@if(Auth::user()->can('wykaz_polis#zarzadzaj'))
    <div class="btn-group " style="min-width:130px;" >
        @if(count($leasingAgreement->insurances))
            <a href="{{ URL::to('insurances/manage-actions/resume-yacht',[ $leasingAgreement->id]) }}" type="button" class="btn btn-primary btn-sm" >Wznów polisę</a>
        @else
        <a href="{{ URL::to('insurances/manage-actions/assign', [$leasingAgreement->id]) }}" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-file"></i> przypisz polisę</a>
        @endif
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            @if(count($leasingAgreement->insurances))
                <li><a href="{{ URL::to('insurances/manage-actions/resume-yacht', [$leasingAgreement->id, 2]) }}">wznów polisę</a></li>
            @else
            <li><a href="{{ URL::to('insurances/manage-actions/assign', [$leasingAgreement->id]) }}">przypisz polisę</a></li>
            @endif
            <li class="divider"></li>
            <li>
                <a href="#" target="{{ URL::to('insurances/manage-dialog/archive', [$leasingAgreement->id]) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    przenieś umowę do archiwum
                </a>
            </li>
            <li class="divider"></li>
            <li>
                @if($leasingAgreement->has_yacht == 1)
                    <a href="{{ URL::to('insurances/manage-actions/refund-yacht', [$leasingAgreement->id]) }}">
                        zwrot składki
                    </a>
                @else
                    <a href="#" target="{{ URL::to('insurances/manage-dialog/refund',[ $leasingAgreement->id]) }}" class="modal-open" data-toggle="modal" data-target="#modal">
                        zwrot składki
                    </a>
                @endif
            </li>
        </ul>
    </div>
@endif
