<td>
    @if(Auth::user()->can('zlecenia_(szkody)#zarzadzaj'))
        <div class="btn-group "  style="min-width:92px;" >
            <button target="{{ URL::route('injuries-getRestoreDeleted', array($injury->id)) }}" type="button" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć szkodę</button>
        </div>
    @endif
</td>
