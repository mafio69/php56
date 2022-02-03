<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie warsztatu {{ $branch->short_name }}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('company/garages/delete', [$branch->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <p>Potwierdź usunięcie warsztatu.</p>
        @if($branch->injuries->count() > 0)
            <p class="text-danger">Usuwany warsztat posiadał przypisane do niego procedowane szkody:</p>
            <ul class="list-group">
            @foreach($branch->injuries as $injury)
                <li class="list-group-item">nr sprawy: {{ $injury->case_nr }}</li>
            @endforeach
            </ul>
        @endif
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>