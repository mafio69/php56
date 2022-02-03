<td>
    @if(Auth::user()->can('zlecenia#zarzadzaj'))
        <div class="btn-group " style="min-width:130px;" >
        <button target="{{ URL::route('dos.other.injuries.get', array('getTheftFinishedPayment',$injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"
                @if($injury->locked_status == 5)
                disabled="disabled"
                @endif
        >kradzież zakończona wypłatą</button>
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
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheft', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftPayment', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież wypłata
                </a>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getTheftRefuse', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    kradzież odmowa
                </a>
            </li>
            <li class="divider"></li>
                <li>
                    <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getClaimsResignation',$injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                        ryzagnacja z roszczeń
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
            </li>
            <li class="divider"></li>
            <li>
                <a href="#" target="{{ URL::route('dos.other.injuries.get', array('getRestore', $injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć szkodę</a>
            </li>
        </ul>
    </div>
    @endif
</td>
