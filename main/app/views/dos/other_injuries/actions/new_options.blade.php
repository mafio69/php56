<td>
    @if(Auth::user()->can('zlecenia#zarzadzaj'))
        <div class="btn-group tips" style="min-width:130px;"
    @if($injury->receive_id == 0)
         title="uzupełnij odbiorcę odszkodowania"
    @endif
    >
        <button target="{{ URL::route('dos.other.injuries.get', array('getInprogress', $injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"
        @if($injury->locked_status == 5)
            disabled="disabled"
        @endif
        >w osbłudze</button>
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
        @if($injury->locked_status == 5)
        disabled="disabled"
        @endif
        >
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getInprogress',$injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    w obsłudze
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getSettled', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    umowa rozliczona
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getCompletedPayment', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm"
                    @if($injury->receive_id == 0)
                       disabled
                    @endif
                >
                    zakończona wypłatą
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getCompletedRefuse', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    zakończona odmową
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getCompletedWithoutRepaired', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    zakończona - bez naprawy
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTotal', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    całkowita
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTotalPayment', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    całkowita wypłata
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTotalRefuse', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    całkowita odmowa
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTotalSettled', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    całkowita umowa rozliczona
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheft', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftPayment', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież wypłata
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftRefuse', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież odmowa
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftSettled', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież umowa rozliczona
                </a>
            </li>
            <li class="divider"></li>
                <li>
                    <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getClaimsResignation',$injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                        ryzagnacja z roszczeń
                    </a>
                </li>
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getCancel',$injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    anuluj zlecenie
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftFinishedPayment', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież zakończona wypłatą
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftFinishedRefuse', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież zakończona odmową
                </a>

                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTotalFinishedPayment', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    całkowita zakończona wypłatą
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTotalFinishedRefuse', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    całkowita zakończona odmową
                </a>
            </li>
        </ul>
        @if($injury->locked_status == 5)
            <i class="fa fa-lock unlock red md-ico tips modal-open-sm" target="{{ URL::route('dos.other.injuries.get', array('getUnlock', $injury->id)) }}" data-toggle="modal" data-target="#modal-sm" title="umożliw zarządzanie szkodą"></i>
{{--            <i class="fa fa-lock unlock red md-ico tips " title="zablokowane zarządzanie zleceniem"></i>--}}
        @endif
        @if($injury->locked_status == '-5')
            <i class="fa fa-unlock lock red md-ico tips modal-open-sm" target="{{ URL::route('dos.other.injuries.get', array('getLock',$injury->id)) }}" data-toggle="modal" data-target="#modal-sm" title="zablokuj zarządzanie szkodą"></i>
{{--            <i class="fa fa-unlock lock red md-ico tips " title="odblokowane zarządzanie zleceniem"></i>--}}
        @endif
    </div>
    @endif
</td>
