<td>
    @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj'))
        <div class="btn-group " style="min-width:130px;" >
            <a href="{{ URL::to('injuries/make/create-new-entity-ea', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm ">
                przyjmij szkodę
            </a>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ URL::to('injuries/make/create-new-entity-ea', array($injury->id)) }}" >przyjmij szkodę</a></li>
                <li class="divider"></li>
                <li><a href="#" target="{{ URL::to('injuries/delete-ea', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">usuń szkodę</a></li>
            </ul>
        </div>
    @endif
</td>
