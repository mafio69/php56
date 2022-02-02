@if(Auth::user()->can('wykaz_polis#zarzadzaj'))
<div class="btn-group " style="min-width:130px;">
    <a href="#" target="{{ URL::to('insurances/manage-dialog/restore', [$leasingAgreement->id]) }}"
       type="button" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal">
        <i class="fa fa-undo"></i> wycofaj z archiwum
    </a>
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a href="#" target="{{ URL::to('insurances/manage-dialog/restore', [$leasingAgreement->id]) }}"
               class="modal-open" data-toggle="modal" data-target="#modal">
                <i class="fa fa-undo"></i> wycofaj z archiwum
            </a>
        </li>
        @if($leasingAgreement->activeInsurance() && $leasingAgreement->activeInsurance()->if_refund_contribution == 1)
            <li class="divider"></li>
            <li>
                @if($leasingAgreement->has_yacht == 1)
                    <a href="{{ URL::to('insurances/manage-actions/assign-after-refund-yacht', [$leasingAgreement->id]) }}">
                        <i class="fa fa-plus fa-fw"></i> wprowadź nową polisę
                    </a>
                @else
                    <a href="{{ URL::to('insurances/manage-actions/assign-after-refund', [$leasingAgreement->id]) }}">
                        <i class="fa fa-plus fa-fw"></i> wprowadź nową polisę
                    </a>
                @endif
            </li>
        @endif
    </ul>
</div>
@endif