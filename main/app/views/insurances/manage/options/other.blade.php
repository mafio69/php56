@if(Auth::user()->can('wykaz_polis#zarzadzaj'))
    <div class="btn-group " style="min-width:130px;">
        <a href="#" target="{{ URL::to('insurances/manage-dialog/archive', [$leasingAgreement->id]) }}"
           type="button" class="btn btn-primary btn-sm modal-open" data-toggle="modal" data-target="#modal">
            przenieś umowę do archiwum
        </a>
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="#" target="{{ URL::to('insurances/manage-dialog/archive', [$leasingAgreement->id]) }}"
                   class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    przenieś umowę do archiwum
                </a>
            </li>
        </ul>
    </div>
@endif