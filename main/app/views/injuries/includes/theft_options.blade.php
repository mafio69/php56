<td>
    @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj'))
        <div class="btn-group " style="min-width:130px;">
            <button target="{{ URL::route('injuries-getTotalFinished', array($injury->id)) }}" type="button"
                    class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"
                    @if($injury->locked_status == 5)
                    disabled="disabled"
                    @endif
            >przenieś do zakończonych
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
                <li>
                    <a href="#" target="{{ URL::route('injuries-getTotalFinished', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm"
                       @if($injury->locked_status == 5)
                       disabled="disabled"
                            @endif
                    >przenieś do zakończonych</a>
                </li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getRestore', array($injury->id)) }}" class="modal-open-sm"
                       data-toggle="modal" data-target="#modal-sm">przywróć szkodę</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getDiscontinuationInvestigation', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">postanowienie o umorzeniu
                        dochodzenia</a>
                </li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getDeregistrationVehicle', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">wyrejestrowanie pojazdu</a>
                </li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getTransferredDok', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przekazano do DOK</a>
                </li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getNoSignsPunishment', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">brak znamion czynu karalnego</a>
                </li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getUsurpation', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">kradzież przywłaszczenie</a>
                </li>
                <li>
                    <a href="#" target="{{ URL::route('injuries-getAgreementSettled', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">umowa rozliczona</a>
                </li>
                <li><a href="#" target="{{ URL::to('injuries/manage/claims-resignation', array($injury->id)) }}"
                       class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">rezygnacja z roszczeń</a></li>
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
