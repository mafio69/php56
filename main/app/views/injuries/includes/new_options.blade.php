<td>
    @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj'))
        <div class="btn-group " style="min-width:130px;">
        <button target="{{ URL::route('injuries-assignCompany', array($injury->id)) }}" type="button"
                class="btn btn-primary btn-sm modal-open-lg" data-toggle="modal" data-target="#modal-lg"
                @if($injury->locked_status == 5)
                disabled="disabled"
                @endif
        >przypisz serwis
        </button>
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
                @if($injury->locked_status == 5)
                disabled="disabled"
                @endif
        >
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li><a href="#" target="{{ URL::route('injuries-assignCompany', array($injury->id)) }}"
                   class="modal-open-lg" data-toggle="modal" data-target="#modal-lg">przypisz serwis</a></li>
            <li><a href="#" target="{{ URL::route('injuries-withoutCompany', array($injury->id)) }}"
                   class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">proceduj bez serwisu</a></li>
            <li><a href="#" target="{{ URL::to('injuries/manage/claims-resignation', array($injury->id)) }}"
                   class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">rezygnacja z roszczeń</a></li>
            <li>
                <a href="#" target="{{ URL::to('injuries/manage/complete-without-assistance', array($injury->id)) }}"
                   class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                    zakończona bez asysty
                </a>
            </li>
            <li class="divider"></li>
            {{--
            <li><a href="#" target="{{ URL::route('injuries-getComplete-l', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">zakończono bez likwidacji</a></li>
            <li><a href="#" target="{{ URL::route('injuries-getComplete-n', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">zakończono bez naprawy</a></li>
            <li class="divider"></li>
            --}}
            <li><a href="#" target="{{ URL::route('injuries-getTotal', array($injury->id)) }}" class="modal-open-sm"
                   data-toggle="modal" data-target="#modal-sm">szkoda całkowita nowa</a></li>
            <li><a href="#" target="{{ URL::route('injuries-getTheft', array($injury->id)) }}" class="modal-open-sm"
                   data-toggle="modal" data-target="#modal-sm">kradzież</a></li>
            <li class="divider"></li>
            <li><a href="#" target="{{ URL::route('injuries-getCancel', array($injury->id)) }}" class="modal-open-sm"
                   data-toggle="modal" data-target="#modal-sm">anuluj szkodę</a></li>
            @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj#przepnij_szkode'))
                <li class="divider"></li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getChangeInjuryStatus', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                        <i class="fa fa-exchange fa-fw" aria-hidden="true"></i> przepnij szkodę
                    </a>
                </li>
            @endif
        </ul>
        @if($injury->locked_status == 5)
            <i class="fa fa-lock unlock red md-ico tips modal-open-sm"
               target="{{ URL::route('injuries-unlock', array($injury->id)) }}" data-toggle="modal"
               data-target="#modal-sm" title="umożliw zarządzanie szkodą"></i>
        @endif
        @if($injury->locked_status == '-5')
            <i class="fa fa-unlock lock red md-ico tips modal-open-sm"
               target="{{ URL::route('injuries-lock', array($injury->id)) }}" data-toggle="modal"
               data-target="#modal-sm" title="zablokuj zarządzanie szkodą"></i>
        @endif
    </div>
    @endif
</td>
