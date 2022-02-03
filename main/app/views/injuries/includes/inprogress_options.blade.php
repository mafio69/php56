<td>
    @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj'))
        <div class="btn-group "  style="min-width:92px;" >
            @if($injury->vehicle && $injury->vehicle->cfm == 1 || ( $injury->branch_id > 0 && $injury->branch->company->groups->count() > 0 && ( $injury->getDocument(3,6)->first() || $injury->getDocument(3,49)->first() || $injury->getDocument(3,60)->first()) ) )
                @if($injury->step == 10)
                    <button target="{{ URL::to('injuries/manage/to-settle', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">do rozliczenia</button>
                @elseif($injury->step == 11)
                    <button target="{{ URL::to('injuries/manage/to-settle', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">do rozliczenia asysta</button>
                @elseif($injury->step == 13)
                    <button target="{{ URL::to('injuries/manage/settled', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">rozliczone</button>
                @elseif($injury->step == 14)
                    <button target="{{ URL::to('injuries/manage/settled', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">rozliczone asysta</button>
                @elseif($injury->step == 22)
                    <button target="{{ URL::route('injuries-getCompleteRefused', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">zakończona - odmową ZU</button>
                @endif
            @elseif(in_array($injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46]))
                <button target="{{ URL::route('injuries-getTotalFinished', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"
                        @if($injury->locked_status == 5)
                        disabled="disabled"
                        @endif
                >przenieś do zakończonych</button>
            @else
               <button target="{{ URL::route('injuries-getComplete', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm"
                   @if($injury->locked_status == 5)
                   disabled="disabled"
                   @endif
               >zakończ - wypłatą</button>
           @endif
           <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
               @if($injury->locked_status == 5)
               disabled="disabled"
               @endif
           >
           <span class="caret"></span>
           <span class="sr-only">Toggle Dropdown</span>
           </button>
           <ul class="dropdown-menu" role="menu">
               @if($injury->step == 22)
                   <li><a href="#" target="{{ URL::route('injuries-getCompleteRefused', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">zakończona - odmową ZU</a></li>
                   <li class="divider"></li>
               @endif
               @if($injury->vehicle && $injury->vehicle->cfm == 1 || ( $injury->branch_id > 0 && $injury->branch->company->groups->count() > 0 && ( $injury->getDocument(3,6)->first() || $injury->getDocument(3,49)->first() || $injury->getDocument(3,60)->first() ) ) )
                   @if($injury->step == 10)
                       <li><a href="#" target="{{ URL::to('injuries/manage/to-settle', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">do rozliczenia</a></li>
                   @elseif($injury->step == 11)
                       <li><a href="#" target="{{ URL::to('injuries/manage/to-settle', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">do rozliczenia asysta</a></li>
                       <li><a href="#" target="{{ URL::to('injuries/manage/back-from-to-settle-to-inprogress', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć na 'w obsłudze'</a></li>
                   @elseif($injury->step == 13)
                       <li><a href="#" target="{{ URL::route('injuries-getComplete', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">rozliczone</a></li>
                       <li><a href="#" target="{{ URL::to('injuries/manage/back-from-to-settle-to-inprogress', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć na 'w obsłudze'</a></li>
                   @elseif($injury->step == 14)
                       <li><a href="#" target="{{ URL::route('injuries-getComplete', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">rozliczone asysta</a></li>
                       <li><a href="#" target="{{ URL::to('injuries/manage/back-from-to-settle-to-inprogress', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć na 'w obsłudze'</a></li>
                   @endif
                   <li class="divider"></li>
               @else
                   <li><a href="#" target="{{ URL::route('injuries-getComplete', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">zakończona - wypłatą</a></li>
               @endif

               @if($injury->step == 11 || $injury->step == 14)
                   <li><a href="#" target="{{ URL::route('injuries-getRefusal', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">odmowa ZU asysta</a></li>
                   <li><a href="#" target="{{ URL::to('injuries/manage/back-from-to-settle-to-inprogress', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć na 'w obsłudze'</a></li>
               @else
                   <li><a href="#" target="{{ URL::route('injuries-getRefusal', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">odmowa ZU</a></li>
               @endif
               <li>
                   <a href="#" target="{{ URL::to('injuries/manage/claims-resignation', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">rezygnacja z roszczeń</a>
               </li>
               <li>
                   <a href="#" target="{{ URL::to('injuries/manage/complete-without-assistance', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                       zakończona bez asysty
                   </a>
               </li>
               <li class="divider"></li>
               @if(in_array($injury->step, [30, 31, 32, 33, 34,35]))
                   <li>
                       <a href="#" target="{{ URL::route('injuries-getContractSettled', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">umowa rozliczona</a>
                   </li>
               @elseif(in_array($injury->step, [40, 41, 42, 43, 44,45]))
                   <li>
                       <a href="#" target="{{ URL::route('injuries-getAgreementSettled', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">umowa rozliczona</a>
                   </li>
               @endif
               @if(!in_array($injury->step, [30, 31, 32, 33, 34, 35, 36, 37]))
                   <li><a href="#" target="{{ URL::route('injuries-getTotal', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">szkoda całkowita nowa</a></li>
               @elseif($injury->step == 30)
                   <li>
                       <a href="#" target="{{ URL::route('injuries-getTotalInjuries', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">szkoda całkowita</a>
                   </li>
               @endif
               <li>
                   <a href="#" target="{{ URL::to('injuries/manage/complete-total-without-assistance', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                       zakończona bez asysty - szkoda całkowita
                   </a>
               </li>
               @if(!in_array($injury->step, [40, 41, 42, 43, 44, 45, 46]))
                   <li><a href="#" target="{{ URL::route('injuries-getTheft', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">kradzież</a></li>
               @endif
               <li class="divider"></li>
               <li><a href="#" target="{{ URL::route('injuries-getCancel', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">anuluj szkodę</a></li>
               @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj#przepnij_szkode'))
                   <li class="divider"></li>
                   <li>
                       <a href="#" target="{{ URL::route('injuries-getChangeInjuryStatus', array($injury->id)) }}" class="modal-open-sm" data-toggle="modal" data-target="#modal-sm">
                           <i class="fa fa-exchange fa-fw" aria-hidden="true"></i> przepnij szkodę
                       </a>
                   </li>
               @endif
           </ul>

           @if($injury->locked_status == 5)
               <i class="fa fa-lock unlock red md-ico tips modal-open-sm" target="{{ URL::route('injuries-unlock', array($injury->id)) }}" data-toggle="modal" data-target="#modal-sm" title="umożliw zarządzanie szkodą"></i>
           @endif
           @if($injury->locked_status == '-5')
               <i class="fa fa-unlock lock red md-ico tips modal-open-sm" target="{{ URL::route('injuries-lock', array($injury->id)) }}" data-toggle="modal" data-target="#modal-sm" title="zablokuj zarządzanie szkodą"></i>
           @endif
       </div>
    @endif
</td>
