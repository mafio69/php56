<td>
    @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj'))
        <div class="btn-group " style="min-width:92px;">
            <button target="{{ URL::route('injuries-getRestoreCompleted', array($injury->id)) }}" type="button"
                    class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć szkodę
            </button>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>

            <ul class="dropdown-menu" role="menu">
                <li><a href="#" target="{{ URL::route('injuries-getRestoreCompleted', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć szkodę</a></li>
                <li><a href="#" target="{{ URL::to('injuries/manage/claims-resignation', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">rezygnacja z roszczeń</a></li>
                @if($injury->step != 26)
                    <li>
                        <a href="#"
                           target="{{ URL::to('injuries/manage/complete-without-assistance', array($injury->id)) }}"
                           class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                            zakończona bez asysty
                        </a>
                    </li>
                @endif
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
        </div>
    @endif
</td>
