<td>
    @if(Auth::user()->can('zlecenia#zarzadzaj'))
        <a href="#" type="button" target="{{ URL::route('dos.other.injuries.get', array('getRestore', $injury->id)) }}" class="btn btn-primary btn-sm modal-open-sm" data-toggle="modal" data-target="#modal-sm">przywróć szkodę</a>
    @endif
</td>
