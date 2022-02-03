<td>
    @if(Auth::user()->can('zlecenia#zarzadzaj'))

    <div class="btn-group " style="min-width:130px;" >
        <a href="{{ URL::route('dos.other.injuries.create-mobile', [$injury->id]) }}" type="button" class="btn btn-primary btn-sm ">
            przyjmij szkodę
        </a>
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ URL::route('dos.other.injuries.create-mobile', [$injury->id])  }}" >przyjmij szkodę</a></li>
            <li><a href="#" target="{{ URL::route('dos.other.injuries.get', array('getCancelMobile',$injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                anuluj zlecenie
                </a></li>
                <li><a href="#" target="{{ URL::route('dos.other.injuries.get', array('getDelete',$injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    usuń zlecenie
                    </a></li>
        </ul>
    </div>
    @endif
</td>
