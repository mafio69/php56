<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">Usuwanie serwisu {{ $company->name }}</h4>
</div>
<div class="modal-body" style="overflow:hidden;">
    <form action="{{ URL::to('companies/delete',  [$company->id]) }}" method="post"  id="dialog-form">
        {{Form::token()}}
        <p>Potwierdź usunięcie serwisu wraz z podległymi jemu warsztatami.</p>
        @if($company->branches->count() > 0)
            <ul class="list-group">
                <?php $has_injury = false;?>
                @foreach($company->branches as $branch)
                    @foreach($branch->injuries as $injury)
                        <?php $has_injury = true;?>
                        <li class="list-group-item">nr sprawy: {{ $injury->case_nr }}</li>
                    @endforeach
                @endforeach
            </ul>
            @if($has_injury)
                <p class="text-danger">Usuwany serwis posiadał przypisane do niego procedowane szkody.</p>
            @endif
        @endif

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
    <button type="button" class="btn btn-primary" id="set">Usuń</button>
</div>